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
   *  @class guestbook_admineditcomment_v1_controller
   *
   *  Implementiert den DocumentController für das Stylesheet 'admineditcomment.html'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 19.05.2007<br />
   */
   class guestbook_admineditcomment_v1_controller extends guestbookBaseController
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      private $_LOCALS;


      function guestbook_admineditcomment_v1_controller(){

         $this->_LOCALS = RequestHandler::getValues(array(
                                                                'Title',
                                                                'Text',
                                                                'entryid',
                                                                'commentid'
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
      *  Version 0.1, 19.05.2007<br />
      */
      public function transformContent(){

         // Referenz auf das Formular holen
         $Form__Comment = &$this->__getForm('Comment');

         if($Form__Comment->get('isSent') == true){

            if($Form__Comment->get('isValid')){

               // Manager holen
               $gM = &$this->__getGuestbookManager();

               // Eintrag erzeugen
               $Comment = new Comment();
               $Comment->set('Title',$this->_LOCALS['Title']);
               $Comment->set('Text',$this->_LOCALS['Text']);
               $Comment->set('ID',$this->_LOCALS['commentid']);

               // Eintrag speichern
               $gM->saveComment($this->_LOCALS['entryid'],$Comment);

             // end if
            }

          // end if
         }
         else{

            // Manager holen
            $gM = &$this->__getGuestbookManager();

            // Eintrag laden
            $Comment = $gM->loadComment($this->_LOCALS['commentid']);

            // Werte füllen
            $Title = & $Form__Comment->getFormElementByName('Title');
            $Title->setAttribute('value',$Comment->get('Title'));

            $Text = & $Form__Comment->getFormElementByName('Text');
            $Text->set('Content',$Comment->get('Text'));

            $EntryID = & $Form__Comment->getFormElementByName('entryid');
            $EntryID->setAttribute('value',$this->_LOCALS['entryid']);

            $CommentID = & $Form__Comment->getFormElementByName('commentid');
            $CommentID->setAttribute('value',$Comment->get('ID'));

          // end else
         }

         // Formular anzeigen
         $this->setPlaceHolder('Form',$Form__Comment->transformForm());

       // end function
      }

    // end class
   }
?>