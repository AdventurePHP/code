<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\modules\usermanagement\pres\documentcontroller\registration;

use APF\core\configuration\ConfigurationException;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\singleton\Singleton;
use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use Exception;

/**
 * This document controller handles the user registration.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.09.2011<br />
 */
class RegistrationController extends UmgtBaseController {

   public function transformContent() {

      $form = $this->getForm('register');

      if ($form->isSent() && $form->isValid()) {

         $uM = $this->getManager();

         $user = new UmgtUser();

         $firstName = $form->getFormElementByName('firstname');
         $firstNameValue = $firstName->getValue();
         $user->setFirstName($firstNameValue);

         $lastName = $form->getFormElementByName('lastname');
         $lastNameValue = $lastName->getValue();
         $user->setLastName($lastNameValue);

         $street = $form->getFormElementByName('street');
         $user->setStreetName($street->getValue());

         $number = $form->getFormElementByName('number');
         $user->setStreetNumber($number->getValue());

         $zip = $form->getFormElementByName('zip');
         $user->setZIPCode($zip->getValue());

         $city = $form->getFormElementByName('city');
         $user->setCity($city->getValue());

         $email = $form->getFormElementByName('email');
         $user->setEMail($email->getValue());

         $userName = $form->getFormElementByName('username');
         $userNameValue = $userName->getValue();
         $user->setUsername($userNameValue);

         $password = $form->getFormElementByName('password');
         $user->setPassword($password->getValue());

         // assemble display name to have a more readable user within the umgt mgmt UI
         if (empty($firstNameValue) && empty($lastNameValue)) {
            $user->setDisplayName($userNameValue);
         } else {
            $user->setDisplayName($lastNameValue . ', ' . $firstNameValue);
         }

         // add initial groups and roles if applicable
         try {
            foreach ($this->getInitialGroups() as $initialGroup) {
               $user->addGroup($initialGroup);
            }

            foreach ($this->getInitialRoles() as $initialRole) {
               $user->addRole($initialRole);
            }
         } catch (ConfigurationException $e) {
            $l = Singleton::getInstance(Logger::class);
            /* @var $l Logger */
            $l->logEntry('registration', 'Registration cannot add initial groups or roles due to the following '
                  . 'exception: ' . $e . ' This may be ok, in case you have no initial groups and/or roles specified.',
                  LogEntry::SEVERITY_INFO);
         }

         try {
            // Lets have a look if the username/email is always in use and show an error message
            try {
               $config = $this->getConfiguration('APF\modules\usermanagement\pres', 'login.ini');
               $loginType = $config->getSection('Default')->getValue('login.type', 'username');
            } catch (ConfigurationException $e) {
               $loginType = 'username';
            }

            if ($loginType === 'username') {
               $regUser = $uM->loadUserByUserName($userNameValue);
            } else {
               $regUser = $uM->loadUserByEMail($email->getValue());
            }

            if ($regUser === null) {
               $uM->saveUser($user);
               $this->getTemplate('register-ok')->transformOnPlace();
            } else {
               $form->setPlaceHolder('register-error', $this->getTemplate('register-error-user-already-exists')->transformTemplate());
               $form->transformOnPlace();
            }

         } catch (Exception $e) {
            $this->getTemplate('system-error')->transformOnPlace();
            $l = Singleton::getInstance(Logger::class);
            /* @var $l Logger */
            $l->logEntry('registration', 'Registration is not possible due to ' . $e, LogEntry::SEVERITY_ERROR);
         }
      } elseif ($form->isSent() && !$form->isValid()) {
         $form->setPlaceHolder('register-error', $this->getTemplate('register-error')->transformTemplate());
         $form->transformOnPlace();
      } else {
         $form->transformOnPlace();
      }

   }

   /**
    * Evaluates the user's initial groups that are applied during registration.
    *
    * @return UmgtGroup[] The list of initial groups.
    * @throws ConfigurationException In case of any misconfiguration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1 12.12.2011
    */
   private function getInitialGroups() {

      $config = $this->getConfiguration('APF\modules\usermanagement\pres', 'registration.ini');

      $sectionName = 'Default';
      if (!$config->hasSection($sectionName)) {
         throw new ConfigurationException('Section "' . $sectionName . '" is not defined within registration.ini');
      }

      $section = $config->getSection($sectionName);

      $uM = $this->getManager();

      $groups = [];

      if ($section->hasSection('group')) {
         $initialGroups = $section->getSection('group');
         foreach ($initialGroups->getValueNames() as $name) {
            $group = $uM->loadGroupByName($initialGroups->getValue($name));
            if ($group !== null) {
               $groups[] = $group;
            }
         }
      }

      return $groups;
   }

   /**
    * Evaluates the user's initial roles that are applied during registration.
    *
    * @return UmgtRole[] The list of initial roles.
    * @throws ConfigurationException In case of any misconfiguration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1 12.12.2011
    */
   private function getInitialRoles() {

      $config = $this->getConfiguration('APF\modules\usermanagement\pres', 'registration.ini');

      $sectionName = 'Default';
      if (!$config->hasSection($sectionName)) {
         throw new ConfigurationException('Section "default" is not defined within registration.ini');
      }

      $section = $config->getSection($sectionName);

      $uM = $this->getManager();

      $roles = [];

      if ($section->hasSection('role')) {
         $initialRoles = $section->getSection('role');
         foreach ($initialRoles->getValueNames() as $name) {
            $role = $uM->loadRoleByName($initialRoles->getValue($name));
            if ($role !== null) {
               $roles[] = $role;
            }
         }
      }

      return $roles;
   }

}
