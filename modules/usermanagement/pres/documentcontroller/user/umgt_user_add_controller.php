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
 * @package modules::usermanagement::pres::documentcontroller::user
 * @class umgt_user_add_controller
 *
 * Implements the controller to add a user.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_user_add_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('UserForm');
      if ($form->isSent() == true && $form->isValid() == true) {

         $firstName = &$form->getFormElementByName('FirstName');
         $lastName = &$form->getFormElementByName('LastName');
         $streetName = &$form->getFormElementByName('StreetName');
         $streetNumber = &$form->getFormElementByName('StreetNumber');
         $zipCode = &$form->getFormElementByName('ZIPCode');
         $city = &$form->getFormElementByName('City');
         $email = &$form->getFormElementByName('EMail');
         $mobile = &$form->getFormElementByName('Mobile');
         $username = &$form->getFormElementByName('Username');
         $password = &$form->getFormElementByName('Password');

         $uM = &$this->getManager();
         $user = new UmgtUser();

         $user->setFirstName($firstName->getValue());
         $user->setLastName($lastName->getValue());
         $user->setStreetName($streetName->getValue());
         $user->setStreetNumber($streetNumber->getValue());
         $user->setZIPCode($zipCode->getValue());
         $user->setCity($city->getValue());
         $user->setEMail($email->getValue());
         $user->setMobile($mobile->getValue());
         $user->setUsername($username->getValue());
         $user->setPassword($password->getValue());

         $uM->saveUser($user);
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'userview' => null)));

      }

      $form->transformOnPlace();

   }

}
