<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::guestbook2009::biz','User');
   import('modules::guestbook2009::biz','Entry');

   /**
    * @namespace modules::guestbook2009::pres::controller
    * @class create_controller
    *
    * Implements the document controller, that handles the "create new entry" view.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   class create_controller extends baseController {

      function transformContent(){

         $form = &$this->__getForm('create_entry');

         if($form->get('isSent') && $form->get('isValid')){

            // Fill domain objects by extracting the values
            // from the form elements directly.
            $name = &$form->getFormElementByName('name');
            $email = &$form->getFormElementByName('email');
            $website = &$form->getFormElementByName('website');

            $user = new User();
            $user->setName($name->getAttribute('value'));
            $user->setEmail($email->getAttribute('value'));
            $user->setWebsite($website->getAttribute('value'));

            $title = &$form->getFormElementByName('title');
            $text = &$form->getFormElementByName('text');
            $entry = new Entry();
            $entry->setTitle($title->getAttribute('value'));
            $entry->setText($text->get('Content'));

            $entry->setEditor($user);

            // Save the entry using the business component.
            $gbServive = &$this->__getDIServiceObject('modules::guestbook2009::biz','GuestbookService');
            $gbServive->saveEntry($entry);

          // end if
         }

         // set language dependent button label by using the
         // language and context information of the current
         // DOM node.
         $config = &$this->__getConfiguration('modules::guestbook2009::pres','language');
         $buttonLabel = $config->getValue($this->__Language,'form.label.button');
         $button = &$form->getFormElementByName('send');
         $button->setAttribute('value',$buttonLabel);

         // Transform on definition place to render
         // the content within the surrounding div.
         $form->transformOnPlace();

         // add dynamic link
         $this->setPlaceHolder('overviewlink',
            frontcontrollerLinkHandler::generateLink(
               $_SERVER['REQUEST_URI'],
               array('gbview' => 'list')
            )
         );

       // enf function
      }

    // end class
   }
?>