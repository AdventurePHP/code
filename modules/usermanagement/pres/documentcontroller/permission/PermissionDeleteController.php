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
namespace APF\modules\usermanagement\pres\documentcontroller\permission;

use APF\modules\usermanagement\biz\model\UmgtPermission;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the delete controller for a permission.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class PermissionDeleteController extends UmgtBaseController {

   public function transformContent() {

      $permissionId = $this->getRequest()->getParameter('permissionid');
      $uM = & $this->getManager();
      $permission = $uM->loadPermissionByID($permissionId);
      $this->getLabel('display-name')->setPlaceHolder('display-name', $permission->getDisplayName());

      $formNo = & $this->getForm('PermissionDelNo');
      $formYes = & $this->getForm('PermissionDelYes');

      $response = $this->getResponse();

      if ($formYes->isSent()) {

         $permission = new UmgtPermission();
         $permission->setObjectId($permissionId);
         $uM->deletePermission($permission);
         $response->forward($this->generateLink(['mainview' => 'permission', 'permissionview' => '', 'permissionid' => '']));

      } elseif ($formNo->isSent()) {
         $response->forward($this->generateLink(['mainview' => 'permission', 'permissionview' => '', 'permissionid' => '']));
      } else {
         $formNo->transformOnPlace();
         $formYes->transformOnPlace();
      }

   }

}
