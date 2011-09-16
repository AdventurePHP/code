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
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');

/**
 * @package modules::usermanagement::pres::documentcontroller::registration
 * @class umgt_registration_controller
 *
 * This document controller handles the user registration.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.09.2011<br />
 */
class umgt_registration_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('register');

      if ($form->isSent() && $form->isValid()) {

         $uM = $this->getManager();

         $user = new UmgtUser();

         $firstName = $form->getFormElementByName('firstname');
         $user->setFirstName($firstName->getValue());

         $lastName = $form->getFormElementByName('lastname');
         $user->setLastName($lastName->getValue());

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
         $user->setUsername($userName->getValue());

         $password = $form->getFormElementByName('password');
         $user->setPassword($password->getValue());

         try {
            $uM->saveUser($user);
            $this->getTemplate('register-ok')->transformOnPlace();
         } catch (Exception $e) {
            $this->getTemplate('system-error')->transformOnPlace();
            import('core::logging', 'Logger');
            $l = &Singleton::getInstance('Logger');
            /* @var $l Logger */
            $l->logEntry('registration', 'Registration is not possible due to ' . $e, 'ERROR');
         }
      } elseif ($form->isSent() && !$form->isValid()) {
         $form->setPlaceHolder('register-error', $this->getTemplate('register-error')->transformTemplate());
         $form->transformOnPlace();
      } else {
         $form->transformOnPlace();
      }

   }

}

?>