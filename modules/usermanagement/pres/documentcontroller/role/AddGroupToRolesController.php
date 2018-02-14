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
namespace APF\modules\usermanagement\pres\documentcontroller\role;

use APF\modules\usermanagement\biz\model\UmgtRole;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\form\taglib\MultiSelectBoxTag;
use APF\tools\form\taglib\SelectBoxOptionTag;

/**
 * Let's you add a group to one or more roles.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.09.2011<br />
 */
class AddGroupToRolesController extends UmgtBaseController {

   public function transformContent() {

      $form = $this->getForm('Roles');

      $uM = $this->getManager();

      $group = $uM->loadGroupByID($this->getRequest()->getParameter('groupid'));
      $roles = $uM->loadRolesNotWithGroup($group);

      if (count($roles) === 0) {
         $tmpl = $this->getTemplate('NoMoreRoles');
         $tmpl->getLabel('message-1')->setPlaceHolder('display-name', $group->getDisplayName());
         $tmpl->getLabel('message-2')->setPlaceHolder('group-view-link', $this->generateLink(['mainview' => 'group', 'roleview' => null, 'groupid' => null]));
         $tmpl->transformOnPlace();

         return;
      }

      $rolesControl = $form->getFormElementByName('Roles');
      /* @var $rolesControl MultiSelectBoxTag */
      foreach ($roles as $role) {
         $rolesControl->addOption($role->getDisplayName(), $role->getObjectId());
      }

      $form->getLabel('display-name')->setPlaceHolder('display-name', $group->getDisplayName());

      if ($form->isSent() && $form->isValid()) {

         $options = $rolesControl->getSelectedOptions();
         $additionalRoles = [];
         foreach ($options as $option) {
            /* @var $option SelectBoxOptionTag */
            $additionalRole = new UmgtRole();
            $additionalRole->setObjectId($option->getValue());
            $additionalRoles[] = $additionalRole;
            unset($additionalRole);
         }

         $uM->attachGroupToRoles($group, $additionalRoles);

         // back to group main view
         $this->getResponse()->forward($this->generateLink(['mainview' => 'group', 'roleview' => null, 'groupid' => null]));

      } else {
         $form->transformOnPlace();
      }
   }

}
