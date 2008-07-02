<?php
   import('modules::guestbook::biz','guestbookManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');


   /**
   *  @package modules::guestbook::pres::documentcontroller
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

         $this->_LOCALS = variablenHandler::registerLocal(array(
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