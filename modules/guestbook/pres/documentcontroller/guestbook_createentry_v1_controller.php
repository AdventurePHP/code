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
   *  @namespace modules::guestbook::pres::documentcontroller
   *  @class guestbook_createentry_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'createentry.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class guestbook_createentry_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


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

         // Referenz auf das Formular holen
         $Form__GuestbookEntry = &$this->__getForm('GuestbookEntry');

         if($Form__GuestbookEntry->get('isValid') && $Form__GuestbookEntry->get('isSent')){

            // Manager holen
            $gM = &$this->__getGuestbookManager();

            // Eintrag erzeugen
            $Entry = new Entry();
            $Entry->set('Name',$this->_LOCALS['Name']);
            $Entry->set('EMail',$this->_LOCALS['EMail']);
            $Entry->set('City',$this->_LOCALS['City']);
            $Entry->set('Website',$this->_LOCALS['Website']);
            $Entry->set('ICQ',$this->_LOCALS['ICQ']);
            $Entry->set('MSN',$this->_LOCALS['MSN']);
            $Entry->set('Skype',$this->_LOCALS['Skype']);
            $Entry->set('AIM',$this->_LOCALS['AIM']);
            $Entry->set('Yahoo',$this->_LOCALS['Yahoo']);
            $Entry->set('Text',$this->_LOCALS['Text']);

            // Eintrag speichern
            $gM->saveEntry($Entry);

          // end if
         }
         else{

            // Button beschriften
            $Config = &$this->__getConfiguration('modules::guestbook','guestbook_lang');
            $Button = &$Form__GuestbookEntry->getFormElementByName('CreateGuestbookEntryButton');
            $Button->setAttribute('value',$Config->getValue($this->__Language,'CreateEntry.Form.Button'));

            // Formular anzeigen
            $this->setPlaceHolder('Form',$Form__GuestbookEntry->transformForm());

          // end elseif
         }

       // end function
      }

    // end class
   }
?>