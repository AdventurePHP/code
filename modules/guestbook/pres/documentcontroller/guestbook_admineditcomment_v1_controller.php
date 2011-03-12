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
   import('modules::guestbook::pres::documentcontroller','guestbookBaseController');

   /**
    *  @package modules::guestbook::pres::documentcontroller
    *  @class guestbook_admineditcomment_v1_controller
    *
    *  Implementiert den DocumentController f�r das Stylesheet 'admineditcomment.html'.<br />
    *
    *  @author Christian Sch�fer
    *  @version
    *  Version 0.1, 19.05.2007<br />
    */
   class guestbook_admineditcomment_v1_controller extends guestbookBaseController {

      /**
       *  @public
       *
       *  Implementiert die asbtrakte Methode "transformContent" aus "APFObject".<br />
       *
       *  @author Christian Sch�fer
       *  @version
       *  Version 0.1, 19.05.2007<br />
       */
      public function transformContent() {

         $values = RequestHandler::getValues(array('Title','Text','entryid','commentid'));

         $form = &$this->getForm('Comment');

         if ($form->isSent()) {

            if ($form->isValid()) {

               $gM = &$this->getGuestbookManager();

               $comment = new Comment();
               $comment->setTitle($values['Title']);
               $comment->setText($values['Text']);
               $comment->setId($values['commentid']);

               $gM->saveComment($values['entryid'], $comment);

            }

         } else {

            $gM = &$this->getGuestbookManager();
            $comment = $gM->loadComment($values['commentid']);

            $Title = & $form->getFormElementByName('Title');
            $Title->setAttribute('value', $comment->getTitle());

            $Text = & $form->getFormElementByName('Text');
            $Text->setContent($comment->getText());

            $EntryID = & $form->getFormElementByName('entryid');
            $EntryID->setAttribute('value', $values['entryid']);

            $CommentID = & $form->getFormElementByName('commentid');
            $CommentID->setAttribute('value', $comment->getId());

         }

         $this->setPlaceHolder('Form', $form->transformForm());

      }

   }
?>