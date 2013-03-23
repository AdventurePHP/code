<?php
namespace APF\modules\guestbook2009\pres\controller\admin;

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
use APF\modules\guestbook2009\pres\controller\admin\GuestbookBackendBaseController;

/**
 * @package modules::guestbook2009::pres
 * @class GuestbookLogoutController
 *
 * Handles the logout call. The class itself is only a wrapper to call the
 * service. This is done, because the guestbook is not based on the front
 * controller.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 21.05.2009<br />
 */
class GuestbookLogoutController extends GuestbookBackendBaseController {

   public function transformContent() {
      $this->getGuestbookService()->logout();
   }

}
