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
import('modules::guestbook2009::pres::controller::admin', 'backend_base_controller');
import('tools::request', 'RequestHandler');

/**
 * @package modules::guestbook2009::pres
 * @class delete_controller
 *
 * Implements the document controller to handle the delete flow.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
class delete_controller extends backend_base_controller {

   public function transformContent() {

      $entryId = RequestHandler::getValue('entryid');
      if ($entryId === null) {
         $this->displayEntrySelection('delete');
      } else {

         $form_yes = &$this->getForm('delete_yes');
         $form_no = &$this->getForm('delete_no');

         if ($form_no->isSent() || $form_yes->isSent()) {

            /* @var $gS GuestbookService */
            $gS = &$this->getDIServiceObject('modules::guestbook2009::biz', 'GuestbookService');

            $entry = null;
            if ($form_yes->isSent()) {
               $entry = new Entry();
               $entry->setId($entryId);
            }

            $gS->deleteEntry($entry);
         } else {

            $hidden_yes_entryid = &$form_yes->getFormElementByName('entryid');
            $hidden_yes_entryid->setAttribute('value', $entryId);
            $form_yes->transformOnPlace();

            $hidden_no_entryid = &$form_no->getFormElementByName('entryid');
            $hidden_no_entryid->setAttribute('value', $entryId);
            $form_no->transformOnPlace();

            $template_confirm_text = $this->getTemplate('confirm_text');
            $template_confirm_text->transformOnPlace();
         }
      }
   }

}
