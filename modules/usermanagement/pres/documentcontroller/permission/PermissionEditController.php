<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\modules\usermanagement\pres\documentcontroller\permission;

use APF\modules\usermanagement\biz\model\UmgtPermission;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to edit a permission.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class PermissionEditController extends UmgtBaseController {

   public function transformContent() {

      // get current permission id
      $permissionId = $this->getRequest()->getParameter('permissionid');

      // initialize the form
      $form = $this->getForm('PermissionEdit');
      $permissionIdControl = $form->getFormElementByName('permissionid');
      $permissionIdControl->setAttribute('value', $permissionId);

      $displayName = $form->getFormElementByName('DisplayName');
      $name = $form->getFormElementByName('Name');
      $value = $form->getFormElementByName('Value');

      $uM = $this->getManager();

      if ($form->isSent()) {

         if ($form->isValid()) {

            $permission = new UmgtPermission();
            $permission->setObjectId($permissionId);
            $permission->setDisplayName($displayName->getValue());
            $permission->setName($name->getValue());
            $permission->setValue($value->getValue());
            $uM->savePermission($permission);
            $this->getResponse()->forward($this->generateLink(['mainview' => 'permission', 'permissionview' => '', 'permissionid' => '']));

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
