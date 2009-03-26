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
   *  @class guestbook_adminedit_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'adminedit.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.05.2007<br />
   */
   class guestbook_adminedit_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_adminedit_v1_controller(){

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
                                                                'Text',
                                                                'entryid'
                                                               )
                                                         );

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die asbtrakte Methode "transformContent" aus "coreObject".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      function transformContent(){

         // Referenz auf das Formular holen
         $Form__GuestbookEntry = &$this->__getForm('GuestbookEntry');

         if($Form__GuestbookEntry->get('isSent') == true){

            if($Form__GuestbookEntry->get('isValid')){

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
               $Entry->set('ID',$this->_LOCALS['entryid']);

               // Eintrag speichern
               $gM->saveEntry($Entry);

             // end if
            }

          // end if
         }
         else{

            // Manager holen
            $gM = &$this->__getGuestbookManager();

            // Eintrag laden
            $Entry = $gM->loadEntry($this->_LOCALS['entryid']);

            // Werte füllen
            $Name = & $Form__GuestbookEntry->getFormElementByName('Name');
            $Name->setAttribute('value',$Entry->get('Name'));

            $EMail = & $Form__GuestbookEntry->getFormElementByName('EMail');
            $EMail->setAttribute('value',$Entry->get('EMail'));

            $City = & $Form__GuestbookEntry->getFormElementByName('City');
            $City->setAttribute('value',$Entry->get('City'));

            $Website = & $Form__GuestbookEntry->getFormElementByName('Website');
            $Website->setAttribute('value',$Entry->get('Website'));

            $ICQ = & $Form__GuestbookEntry->getFormElementByName('ICQ');
            $ICQ->setAttribute('value',$Entry->get('ICQ'));

            $MSN = & $Form__GuestbookEntry->getFormElementByName('MSN');
            $MSN->setAttribute('value',$Entry->get('MSN'));

            $Skype = & $Form__GuestbookEntry->getFormElementByName('Skype');
            $Skype->setAttribute('value',$Entry->get('Skype'));

            $AIM = & $Form__GuestbookEntry->getFormElementByName('AIM');
            $AIM->setAttribute('value',$Entry->get('AIM'));

            $Yahoo = & $Form__GuestbookEntry->getFormElementByName('Yahoo');
            $Yahoo->setAttribute('value',$Entry->get('Yahoo'));

            $Text = & $Form__GuestbookEntry->getFormElementByName('Text');
            $Text->set('Content',$Entry->get('Text'));

            $ID = & $Form__GuestbookEntry->getFormElementByName('entryid');
            $ID->setAttribute('value',$Entry->get('ID'));

          // end else
         }

         // Formular anzeigen
         $this->setPlaceHolder('Form',$Form__GuestbookEntry->transformForm());

       // end function
      }

    // end class
   }
?>