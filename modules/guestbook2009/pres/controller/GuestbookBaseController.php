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
use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\guestbook2009\biz\GuestbookService;

/**
 * @package APF\modules\guestbook2009\pres\controller
 * @class GuestbookBaseController
 *
 * Provides basic services for all guestbook2009 document controllers.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 23.03.2013<br />
 */
abstract class GuestbookBaseController extends BaseDocumentController {

   /**
    * @return GuestbookService The instance of the guestbook service.
    */
   protected function &getGuestbookService() {
      return $this->getDIServiceObject('APF\modules\guestbook2009\biz', 'GuestbookService');
   }

}