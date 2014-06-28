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
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to list the existing permissions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class PermissionListController extends UmgtBaseController {

   public function transformContent() {

      // load the permission list
      $uM = & $this->getManager();
      $permissionList = $uM->getPagedPermissionList();

      // display list
      $buffer = (string) '';
      $template = & $this->getTemplate('Permission');
      foreach ($permissionList as $permission) {
         $template->setPlaceHolder('DisplayName', $permission->getDisplayName());
         $template->setPlaceHolder('Name', $permission->getName());
         $template->setPlaceHolder('Value', $permission->getValue());
         $id = $permission->getObjectId();
         $template->setPlaceHolder('permission_edit', $this->generateLink(array('mainview' => 'permission', 'permissionview' => 'edit', 'permissionid' => $id)));
         $template->setPlaceHolder('permission_delete', $this->generateLink(array('mainview' => 'permission', 'permissionview' => 'delete', 'permissionid' => $id)));

         $buffer .= $template->transformTemplate();
      }
      $this->setPlaceHolder('ListPermissions', $buffer);

   }

}
