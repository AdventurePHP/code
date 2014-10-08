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
namespace APF\modules\usermanagement\pres\documentcontroller\user;

use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\http\HeaderManager;

/**
 * Implements the controller to delete a user.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class UserDeleteController extends UmgtBaseController {

   public function transformContent() {

      $userId = self::getRequest()->getParameter('userid');
      $uM = & $this->getManager();
      $user = $uM->loadUserById($userId);

      $this->getLabel('DisplayName')->setPlaceHolder('display-name', $user->getDisplayName());

      $formNo = & $this->getForm('UserDelNo');
      $formYes = & $this->getForm('UserDelYes');

      if ($formYes->isSent()) {
         $user = new UmgtUser();
         $user->setObjectId($userId);
         $uM->deleteUser($user);
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'userview' => null, 'userid' => null)));
      } elseif ($formNo->isSent()) {
         HeaderManager::forward($this->generateLink(array('mainview' => 'user', 'userview' => null, 'userid' => null)));
      } else {
         $formNo->transformOnPlace();
         $formYes->transformOnPlace();
      }

   }

}
