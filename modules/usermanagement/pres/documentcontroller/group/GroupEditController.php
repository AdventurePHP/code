<?php
namespace APF\modules\usermanagement\pres\documentcontroller\group;

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
use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\http\HeaderManager;
use APF\tools\request\RequestHandler;

/**
 * @package APF\modules\usermanagement\pres\documentcontroller\group
 * @class GroupEditController
 *
 * Implements the controller to edit a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class GroupEditController extends UmgtBaseController {

   public function transformContent() {

      $groupId = RequestHandler::getValue('groupid');

      $form = &$this->getForm('GroupEdit');
      $groupIdField = &$form->getFormElementByName('groupid');
      $groupIdField->setAttribute('value', $groupId);

      $displayName = &$form->getFormElementByName('DisplayName');
      $description = &$form->getFormElementByName('Description');

      $uM = &$this->getManager();

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $group = new UmgtGroup();
            $group->setObjectId($groupId);
            $group->setDisplayName($displayName->getValue());
            $group->setDescription($description->getValue());
            $uM->saveGroup($group);
            HeaderManager::forward($this->generateLink(array('mainview' => 'group', 'groupview' => '', 'groupid' => '')));

         } else {
            $form->transformOnPlace();
         }

      } else {
         $group = $uM->loadGroupByID($groupId);
         $displayName->setValue($group->getDisplayName());
         $description->setValue($group->getDescription());
         $form->transformOnPlace();
      }

   }

}
