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

   import('tools::link','FrontcontrollerLinkHandler');
   import('modules::kontakt4::biz','ContactFormData');

   /**
    * @package modules::kontakt4::pres::documentcontroller
    * @class contact_form_controller
    *
    * Document controller for the form view of the contact module.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 23.02.2007<br />
    * Version 0.4, 12.09.2009 (Refactoring due to changes of the form taglibs)<br />
    */
   class contact_form_controller extends base_controller {
      
      /**
       * @public
       *
       * Displays the form and handles the user input.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       * Version 0.3, 29.03.2007<br />
       * Version 0.4, 27.05.2007<br />
       * Version 0.5, 12.09.2009 (Refactoring due to changes of the form taglibs)<br />
       */
      public function transformContent(){

         $form = &$this->__getForm('contact');

         // generate a generic action url, to be included in various pages
         $action = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array());
         $form->setAction($action);

         // fill recipient list
         $recipients = & $form->getFormElementByName('Empfaenger');

         $cM = &$this->__getServiceObject('modules::kontakt4::biz','ContactManager');
         /* @var $recipientList ContactFormRecipient[] */
         $recipientList = $cM->loadRecipients();

         for($i = 0; $i < count($recipientList); $i++){
            $recipients->addOption($recipientList[$i]->getName(),$recipientList[$i]->getId());
          // end if
         }

         if($form->isSent() && $form->isValid()){

            $formData = new ContactFormData();

            $recipient = &$form->getFormElementByName('Empfaenger');
            $option = &$recipient->getSelectedOption();
            $recipientId = $option->getAttribute('value');
            $formData->setRecipientId($recipientId);

            $name = &$form->getFormElementByName('AbsenderName');
            $formData->setSenderName($name->getAttribute('value'));

            $email = &$form->getFormElementByName('AbsenderAdresse');
            $formData->setSenderEmail($email->getAttribute('value'));

            $subject = &$form->getFormElementByName('Betreff');
            $formData->setSubject($subject->getAttribute('value'));

            $text = &$form->getFormElementByName('Text');
            $formData->setMessage($text->getContent());

            $cM = &$this->__getServiceObject('modules::kontakt4::biz','ContactManager');
            $cM->sendContactForm($formData);

          // end if
         }
         else{

            // label the button
            $config = $this->getConfiguration('modules::kontakt4','language');
            $value = $config->getSection($this->__Language)->getValue('form.button');
            $button = &$form->getFormElementByName('send');
            $button->setAttribute('value',$value);

            // fill listeners with the language dependent values
            $senderError = &$form->getFormElementByID('sender-error');
            $senderError->setPlaceHolder('content',$config->getSection($this->__Language)->getValue('form.name.error'));

            $addrError = &$form->getFormElementByID('addr-error');
            $addrError->setPlaceHolder('content',$config->getSection($this->__Language)->getValue('form.email.error'));

            $subjectError = &$form->getFormElementByID('subject-error');
            $subjectError->setPlaceHolder('content',$config->getSection($this->__Language)->getValue('form.subject.error'));

            $textError = &$form->getFormElementByID('text-error');
            $textError->setPlaceHolder('content',$config->getSection($this->__Language)->getValue('form.text.error'));

            $captchaError = &$form->getFormElementByID('captcha-error');
            $captchaError->setPlaceHolder('content',$config->getSection($this->__Language)->getValue('form.captcha.error'));

            $form->transformOnPlace();

          // end else
         }

       // end function
      }

    // end class
   }
?>