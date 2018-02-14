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
namespace APF\modules\usermanagement\pres\documentcontroller\group;

use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Implements the controller to add a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class GroupAddController extends UmgtBaseController {

   public function transformContent() {

      $form = $this->getForm('GroupAdd');
      if ($form->isSent() && $form->isValid()) {

         $uM = $this->getManager();

         $group = new UmgtGroup();

         $displayName = $form->getFormElementByName('DisplayName');
         $group->setDisplayName($displayName->getValue());

         $description = $form->getFormElementByName('Description');
         $group->setDescription($description->getValue());

         $uM->saveGroup($group);

         // redirect to the desired view
         $this->getResponse()->forward($this->generateLink(['mainview' => 'group', 'groupview' => '']));

      }
      $form->transformOnPlace();

   }

}
