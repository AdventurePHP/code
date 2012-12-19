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

/**
 * @package modules::guestbook::pres::documentcontroller
 * @class guestbook_v1_controller
 *
 *  Document controller for 'guestbook'.
 *
 * @author Christian Sch�fer
 * @version
 * Version 0.1, 12.04.2007<br />
 */
class guestbook_v1_controller extends BaseDocumentController {

   public function transformContent() {
      $gM = &$this->getAndInitServiceObject('modules::guestbook::biz', 'GuestbookManager', $this->getAttribute('guestbookid'));
      $guestbook = $gM->loadGuestbook();

      $this->setPlaceHolder('Name', $guestbook->getName());
      $this->setPlaceHolder('Description', $guestbook->getDescription());
   }

}
