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
import('modules::guestbook::biz', 'GuestbookManager');
import('core::session', 'SessionManager');
import('modules::guestbook::pres::documentcontroller', 'guestbookBaseController');
import('tools::http', 'HeaderManager');
import('tools::link', 'LinkGenerator');

/**
 * @package modules::guestbook::pres::documentcontroller
 * @class guestbook_admindelete_v1_controller
 *
 *  Document controller for 'admindelete'.<br />
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 05.05.2007<br />
 */
class guestbook_admindelete_v1_controller extends guestbookBaseController {

   public function transformContent() {

      $Form__FormNo = &$this->getForm('FormNo');
      $Form__FormYes = &$this->getForm('FormYes');

      $session = new SessionManager($this->getGuestbookNamespace());

      if ($session->loadSessionData('AdminView') == true) {

         if ($Form__FormNo->isSent()) {
            $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('gbview' => 'display', 'entryid' => '')));
            HeaderManager::forward($link);
         }

         if ($Form__FormYes->isSent()) {

            $gM = &$this->getGuestbookManager();

            $Entry = new Entry();
            $Entry->setId(RequestHandler::getValue('entryid'));

            $gM->deleteEntry($Entry);

            $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('gbview' => 'display', 'entryid' => '')));
            HeaderManager::forward($link);
         }

         $Form__FormNo->transformOnPlace();
         $Form__FormYes->transformOnPlace();
      } else {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array('gbview' => 'display', 'entryid' => '')));
         HeaderManager::forward($link);
      }
   }

}
