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
namespace APF\modules\guestbook2009\pres\controller\admin;

use APF\modules\guestbook2009\biz\Entry;
use APF\tools\request\RequestHandler;

/**
 * Implements the document controller to handle the delete flow.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
class GuestbookDeleteEntryController extends GuestbookBackendBaseController {

   public function transformContent() {

      $entryId = RequestHandler::getValue('entryid');
      if ($entryId === null) {
         $this->displayEntrySelection('delete');
      } else {

         $form_yes = & $this->getForm('delete_yes');
         $form_no = & $this->getForm('delete_no');

         if ($form_no->isSent() || $form_yes->isSent()) {

            $entry = null;
            if ($form_yes->isSent()) {
               $entry = new Entry();
               $entry->setId($entryId);
            }

            $this->getGuestbookService()->deleteEntry($entry);
         } else {

            $hidden_yes_entryid = & $form_yes->getFormElementByName('entryid');
            $hidden_yes_entryid->setAttribute('value', $entryId);
            $form_yes->transformOnPlace();

            $hidden_no_entryid = & $form_no->getFormElementByName('entryid');
            $hidden_no_entryid->setAttribute('value', $entryId);
            $form_no->transformOnPlace();

            $template_confirm_text = $this->getTemplate('confirm_text');
            $template_confirm_text->transformOnPlace();
         }
      }
   }

}
