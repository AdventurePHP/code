<?php
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');

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