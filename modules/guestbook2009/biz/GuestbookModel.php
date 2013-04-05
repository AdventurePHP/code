<?php
namespace APF\modules\guestbook2009\biz;

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
use APF\core\pagecontroller\APFObject;

/**
 * @package APF\APF\modules\guestbook2009\biz
 * @class GuestbookModel
 *
 * The GuestbookModel represents the application status. It is used to store the
 * id of the guestbook.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.05.2009<br />
 */
final class GuestbookModel extends APFObject {

   /**
    * @var int Stores the id of the guestbook. It is filled by the taglib including the
    * guestbook and consumed by the service and data mapper.
    */
   private $guestbookId;

   public function getGuestbookId() {
      return $this->guestbookId;
   }

   public function setGuestbookId($guestbookId) {
      $this->guestbookId = $guestbookId;
   }

}
