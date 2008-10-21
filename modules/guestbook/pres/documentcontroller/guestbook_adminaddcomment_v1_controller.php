<?php
   import('modules::guestbook::biz','guestbookManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');


   /**
   *  @package modules::guestbook::pres::documentcontroller
   *  @class guestbook_adminaddcomment_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'adminaddcomment.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.05.2007<br />
   */
   class guestbook_adminaddcomment_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      function guestbook_adminaddcomment_v1_controller(){

         $this->_LOCALS = variablenHandler::registerLocal(array(
                                                                'Title',
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
         $Form__GuestbookAddComment = &$this->__getForm('GuestbookAddComment');

         // Aktion für Eintrag
         if($Form__GuestbookAddComment->get('isSent') == true){

            if($Form__GuestbookAddComment->get('isValid')){

               // Manager holen
               $gM = &$this->__getGuestbookManager();

               // Eintrag erzeugen
               $Comment = new Comment();
               $Comment->set('Title',$this->_LOCALS['Title']);
               $Comment->set('Text',$this->_LOCALS['Text']);

               // Eintrag speichern
               $gM->saveComment($this->_LOCALS['entryid'],$Comment);

             // end if
            }

          // end if
         }

         // Formular anzeigen
         $ID = &$Form__GuestbookAddComment->getFormElementByName('entryid');
         $ID->setAttribute('value',$this->_LOCALS['entryid']);
         $this->setPlaceHolder('Form',$Form__GuestbookAddComment->transformForm());

       // end function
      }

    // end class
   }
?>