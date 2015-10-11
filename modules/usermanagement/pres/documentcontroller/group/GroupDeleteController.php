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
namespace APF\modules\usermanagement\pres\documentcontroller\group;

use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to delete a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class GroupDeleteController extends UmgtBaseController {

   public function transformContent() {

      // get the group id from the request
      $groupId = $this->getRequest()->getParameter('groupid');

      // load the current group and print the display name
      $uM = & $this->getManager();
      $group = $uM->loadGroupByID($groupId);
      $this->getLabel('display-name')->setPlaceHolder('display-name', $group->getDisplayName());

      // prepare the forms and execute action
      $formNo = & $this->getForm('GroupDelNo');
      $formYes = & $this->getForm('GroupDelYes');

      $response = $this->getResponse();

      if ($formYes->isSent()) {
         $group = new UmgtGroup();
         $group->setObjectId($groupId);
         $uM->deleteGroup($group);
         $response->forward($this->generateLink(['mainview' => 'group', 'groupview' => '', 'groupid' => '']));
      } elseif ($formNo->isSent()) {
         $response->forward($this->generateLink(['mainview' => 'group', 'groupview' => '', 'groupid' => '']));
      } else {
         $formNo->transformOnPlace();
         $formYes->transformOnPlace();
      }

   }

}
