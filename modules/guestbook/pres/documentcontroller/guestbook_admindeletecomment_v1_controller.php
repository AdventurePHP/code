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

   import('modules::guestbook::biz','GuestbookManager');
   import('core::session','SessionManager');
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');
   import('tools::http', 'HeaderManager');

   /**
    *  @package modules::guestbook::pres::documentcontroller
    *  @class guestbook_admindeletecomment_v1_controller
    *
    *  Implementiert den DocumentController f�r das Stylesheet 'admindeletecomment.html'.<br />
    *
    *  @author Christian Achatz
    *  @version
    *  Version 0.1, 19.05.2007<br />
    */
   class guestbook_admindeletecomment_v1_controller extends guestbookBaseController {

      public function transformContent() {

         $Form__FormNo = &$this->__getForm('FormNo');
         $Form__FormYes = &$this->__getForm('FormYes');

         $oSessMgr = new SessionManager($this->getGuestbookNamespace());

         if ($oSessMgr->loadSessionData('AdminView') == true) {

            if ($Form__FormNo->isSent()) {
               $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'display', 'commentid' => ''));
               HeaderManager::forward($link);
            }

            if ($Form__FormYes->isSent()) {

               $gM = &$this->getGuestbookManager();

               $comment = new Comment();
               $comment->setId(RequestHandler::getValue('commentid'));

               $gM->deleteComment($comment);

               $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'display', 'commentid' => ''));
               HeaderManager::forward($link);

            }

            $this->setPlaceHolder('FormNo', $Form__FormNo->transformForm());
            $this->setPlaceHolder('FormYes', $Form__FormYes->transformForm());

         } else {
            $link = LinkHandler::generateLink($_SERVER['REQUEST_URI'], array('gbview' => 'display', 'commentid' => ''));
            HeaderManager::forward($link);
         }

      }

   }
?>