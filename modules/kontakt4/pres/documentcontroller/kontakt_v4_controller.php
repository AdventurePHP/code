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

   /**
    * @namespace modules::kontakt4::pres::documentcontroller
    * @class kontakt_v4_controller
    *
    * Document controller for the form view of the contact module.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 03.06.2006<br />
    * Version 0.2, 04.06.2006<br />
    * Version 0.3, 23.02.2007<br />
    * Version 0.4, 12.09.2009 (Refactoring due to changes of the form taglibs)<br />
    */
   class kontakt_v4_controller extends baseController {

      public function kontakt_v4_controller(){
      }

      /**
       * @public
       *
       * Displays the form and handles the user input.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 03.06.2006<br />
       * Version 0.2, 04.06.2006<br />
       * Version 0.3, 29.03.2007<br />
       * Version 0.4, 27.05.2007<br />
       * Version 0.5, 12.09.2009 (Refactoring due to changes of the form taglibs)<br />
       */
      function transformContent(){

         $form = &$this->__getForm('contact');

         // generate a generic action url, to be included in various pages
         $action = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],$_REQUEST);
         $form->setAction($action);

         if($form->isSent() && $form->isValid()){

            $oFD = new oFormData();

            $recipient = &$form->getFormElementByName('Empfaenger');
            $oFD->set('RecipientID',$recipient->getAttribute('value'));

            $name = &$form->getFormElementByName('AbsenderName');
            $oFD->set('SenderName',$name->getAttribute('value'));

            $email = &$form->getFormElementByName('AbsenderAdresse');
            $oFD->set('SenderEMail',$email->getAttribute('value'));

            $subject = &$form->getFormElementByName('Betreff');
            $oFD->set('Subject',$subject->getAttribute('value'));

            $text = &$form->getFormElementByName('Text');
            $oFD->set('Text',$text->get('Content'));

            $cM = &$this->__getServiceObject('modules::kontakt4::biz','contactManager');
            $cM->sendContactForm($oFD);

          // end if
         }
         else{

            // label the button
            $config = &$this->__getConfiguration('modules::kontakt4','language');
            $button = &$form->getFormElementByName('send');
            $button->setAttribute('value',$config->getValue($this->__Language,'form.button'));

            // fill listeners with the language dependent values
            $senderError = &$form->getFormElementByID('sender-error');
            $senderError->setPlaceHolder('content',$config->getValue($this->__Language,'form.name.error'));

            $addrError = &$form->getFormElementByID('addr-error');
            $addrError->setPlaceHolder('content',$config->getValue($this->__Language,'form.email.error'));

            $subjectError = &$form->getFormElementByID('subject-error');
            $subjectError->setPlaceHolder('content',$config->getValue($this->__Language,'form.subject.error'));

            $textError = &$form->getFormElementByID('text-error');
            $textError->setPlaceHolder('content',$config->getValue($this->__Language,'form.text.error'));

            // fill recipient list
            $recipients = & $form->getFormElementByName('Empfaenger');

            $cM = &$this->__getServiceObject('modules::kontakt4::biz','contactManager');
            $recipientList = $cM->loadRecipients();

            for($i = 0; $i < count($recipientList); $i++){
               $recipients->addOption($recipientList[$i]->get('Name'),$recipientList[$i]->get('oID'));
             // end if
            }

            $form->transformOnPlace();

          // end else
         }

       // end function
      }

    // end class
   }
?>