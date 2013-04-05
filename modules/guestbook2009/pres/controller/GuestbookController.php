<?php
namespace APF\modules\guestbook2009\pres\controller;

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

/**
 * @package APF\modules\guestbook2009\pres
 * @class GuestbookController
 *
 * Displays the guestbook's language dependent attributes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
class GuestbookController extends GuestbookBaseController {

   public function transformContent() {
      $guestbook = $this->getGuestbookService()->loadGuestbook();
      $this->setPlaceHolder('title', $guestbook->getTitle());
      $this->setPlaceHolder('description', $guestbook->getDescription());
   }

}
