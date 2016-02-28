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
namespace APF\modules\usermanagement\pres\documentcontroller\user;

use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\form\FormControl;
use APF\tools\form\taglib\DateSelectorTag;
use APF\tools\form\validator\AbstractFormValidator;

/**
 * Implements the edit controller for a user.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class UserEditController extends UmgtBaseController {

   /**
    * Displays and handles the user edit form.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    * Version 0.2, 02.01.2009 (Added the password fields handling)<br />
    */
   public function transformContent() {

      // get the userid from the request
      $userId = $this->getRequest()->getParameter('userid');

      // setup the form
      $form = $this->getForm('UserForm');
      $fieldUserId = $form->getFormElementByName('userid');
      $fieldUserId->setAttribute('value', $userId);

      $firstName = $form->getFormElementByName('FirstName');
      $lastName = $form->getFormElementByName('LastName');
      /* @var $birthday DateSelectorTag */
      $birthday = $form->getFormElementByName('Birthday');
      $streetName = $form->getFormElementByName('StreetName');
      $streetNumber = $form->getFormElementByName('StreetNumber');
      $zipCode = $form->getFormElementByName('ZIPCode');
      $city = $form->getFormElementByName('City');
      $email = $form->getFormElementByName('EMail');
      $mobile = $form->getFormElementByName('Mobile');
      $username = $form->getFormElementByName('Username');

      // get the manager
      $uM = $this->getManager();

      if ($form->isSent()) {

         if ($form->isValid()) {

            // setup the domain object
            $user = new UmgtUser();
            $user->setObjectId($userId);

            // read the "normal" fields
            $user->setFirstName($firstName->getValue());
            $user->setLastName($lastName->getValue());
            $user->setBirthday($birthday->getValue());
            $user->setStreetName($streetName->getValue());
            $user->setStreetNumber($streetNumber->getValue());
            $user->setZIPCode($zipCode->getValue());
            $user->setCity($city->getValue());
            $user->setEMail($email->getValue());
            $user->setMobile($mobile->getValue());
            $user->setUsername($username->getValue());

            // read the password field
            $passField1 = $form->getFormElementByName('Password');
            $passField2 = $form->getFormElementByName('Password2');
            $pass1 = $passField1->getAttribute('value');
            $pass2 = $passField2->getAttribute('value');

            $response = $this->getResponse();

            if (!empty($pass1)) {

               if ($pass1 !== $pass2) {
                  $passField1->markAsInvalid();
                  $passField2->markAsInvalid();
                  $passField1->appendCssClass($this->getMarkerClass($passField1));
                  $passField2->appendCssClass($this->getMarkerClass($passField2));
                  $this->setPlaceHolder('UserEdit', $form->transformForm());
               } else {

                  // add the password to the object
                  $user->setPassword($pass2);

                  // save the user
                  $uM->saveUser($user);
                  $response->forward($this->generateLink(['mainview' => 'user', 'userview' => '', 'userid' => '']));

               }

            } else {
               $uM->saveUser($user);
               $response->forward($this->generateLink(['mainview' => 'user', 'userview' => '', 'userid' => '']));
            }

         } else {
            $form->transformOnPlace();
         }

      } else {

         $user = $uM->loadUserByID($userId);

         // pre-fill form
         $firstName->setValue($user->getFirstName());
         $lastName->setValue($user->getLastName());
         $birthday->setValue($user->getBirthday());
         $streetName->setValue($user->getStreetName());
         $streetNumber->setValue($user->getStreetNumber());
         $zipCode->setValue($user->getZIPCode());
         $city->setValue($user->getCity());
         $email->setValue($user->getEMail());
         $mobile->setValue($user->getMobile());
         $username->setValue($user->getUsername());

         $form->transformOnPlace();

      }

   }

   private function getMarkerClass(FormControl &$control) {
      $marker = $control->getAttribute(AbstractFormValidator::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
      if (empty($marker)) {
         $marker = AbstractFormValidator::$DEFAULT_MARKER_CLASS;
      }

      return $marker;
   }

}
