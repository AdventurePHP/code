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
 * Implements the controller to add a permission.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class PermissionAddController extends UmgtBaseController {

   public function transformContent() {

      $uM = & $this->getManager();

      $form = & $this->getForm('PermissionAdd');

      if ($form->isSent() == true && $form->isValid() == true) {

         $displayName = $form->getFormElementByName('DisplayName');
         $name = $form->getFormElementByName('Name');
         $value = $form->getFormElementByName('Value');

         $permission = new UmgtPermission();
         $permission->setDisplayName($displayName->getValue());
         $permission->setName($name->getValue());
         $permission->setValue($value->getValue());
         $uM->savePermission($permission);
         $this->getResponse()->forward($this->generateLink(['mainview' => 'permission', 'permissionview' => '']));

      }

      $form->transformOnPlace();

   }

}
