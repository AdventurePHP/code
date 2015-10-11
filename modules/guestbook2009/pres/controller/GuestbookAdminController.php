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
namespace APF\modules\guestbook2009\pres\controller;

use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Implements the document controller for the admin main view. Generates the links
 * for the subviews to edit or delete an entry.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 18.05.2009<br />
 */
class GuestbookAdminController extends GuestbookBaseController {

   public function transformContent() {

      // invoke the service to check, if the current user may request this page
      $this->getGuestbookService()->checkAccessAllowed();

      // generate the admin menu links to be able to include the module in either page.
      $editLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
            'gbview'    => 'admin',
            'adminview' => 'edit'
      ]));
      $this->setPlaceHolder('editLink', $editLink);

      $deleteLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
            'gbview'    => 'admin',
            'adminview' => 'delete'
      ]));
      $this->setPlaceHolder('deleteLink', $deleteLink);

      $logoutLink = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([
            'gbview'    => 'admin',
            'adminview' => 'logout'
      ]));
      $this->setPlaceHolder('logoutLink', $logoutLink);
   }

}
