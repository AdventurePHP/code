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

   import('modules::guestbook::biz','GuestbookManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');

   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_createentry_v1_controller
   *
   *  Implementiert den DocumentController f�r das Stylesheet 'createentry.html'.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class guestbook_createentry_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  H�lt lokal verwendete Variablen.
      */
      private $_LOCALS;

      function guestbook_createentry_v1_controller(){

         $this->_LOCALS = RequestHandler::getValues(array(
                                                                'Name',
                                                                'EMail',
                                                                'City',
                                                                'Website',
                                                                'ICQ',
                                                                'MSN',
                                                                'Skype',
                                                                'AIM',
                                                                'Yahoo',
                                                                'Text'
                                                               )
                                                         );

       // end function
      }


      function transformContent(){

         $form = &$this->__getForm('GuestbookEntry');

         if($form->isValid() && $form->isSent()){

            $gM = &$this->__getGuestbookManager();

            $entry = new Entry();
            $entry->set('Name',$this->_LOCALS['Name']);
            $entry->set('EMail',$this->_LOCALS['EMail']);
            $entry->set('City',$this->_LOCALS['City']);
            $entry->set('Website',$this->_LOCALS['Website']);
            $entry->set('ICQ',$this->_LOCALS['ICQ']);
            $entry->set('MSN',$this->_LOCALS['MSN']);
            $entry->set('Skype',$this->_LOCALS['Skype']);
            $entry->set('AIM',$this->_LOCALS['AIM']);
            $entry->set('Yahoo',$this->_LOCALS['Yahoo']);
            $entry->set('Text',$this->_LOCALS['Text']);

            $gM->saveEntry($entry);

          // end if
         }
         else{

            // label button
            $config = &$this->__getConfiguration('modules::guestbook','guestbook_lang');
            $button = &$form->getFormElementByName('CreateGuestbookEntryButton');
            $button->setAttribute('value',$config->getValue($this->__Language,'CreateEntry.Form.Button'));

            $form->transformOnPlace();

          // end elseif
         }

       // end function
      }

    // end class
   }
?>