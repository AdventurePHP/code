<?php
namespace APF\modules\usermanagement\pres\documentcontroller\permission;

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
use APF\modules\usermanagement\biz\model\UmgtPermission;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\http\HeaderManager;
use APF\tools\request\RequestHandler;

/**
 * @package APF\modules\usermanagement\pres\documentcontroller
 * @class PermissionEditController
 *
 * Implements the controller to edit a permission.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class PermissionEditController extends UmgtBaseController {

   public function transformContent() {

      // get current permission id
      $permissionId = RequestHandler::getValue('permissionid');

      // initialize the form
      $form = &$this->getForm('PermissionEdit');
      $permissionIdControl = &$form->getFormElementByName('permissionid');
      $permissionIdControl->setAttribute('value', $permissionId);

      $displayName = &$form->getFormElementByName('DisplayName');
      $name = &$form->getFormElementByName('Name');
      $value = &$form->getFormElementByName('Value');

      $uM = &$this->getManager();

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $permission = new UmgtPermission();
            $permission->setObjectId($permissionId);
            $permission->setDisplayName($displayName->getValue());
            $permission->setName($name->getValue());
            $permission->setValue($value->getValue());
            $uM->savePermission($permission);
            HeaderManager::forward($this->generateLink(array('mainview' => 'permission', 'permissionview' => '', 'permissionid' => '')));

         } else {
            $form->transformOnPlace();
         }

      } else {

         $permission = $uM->loadPermissionByID($permissionId);

         $displayName->setAttribute('value', $permission->getDisplayName());
         $name->setAttribute('value', $permission->getName());
         $value->setAttribute('value', $permission->getValue());

         $form->transformOnPlace();

      }

   }

}
