<?php
namespace APF\modules\usermanagement\pres\documentcontroller;

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
 * @package APF\modules\usermanagement\pres\documentcontroller
 * @class RoleNavigationController
 *
 * Implements the document controller to display the role sub menu.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2008<br />
 */
class RoleNavigationController extends UmgtBaseController {

   public function transformContent() {
      $this->setPlaceHolder('manage_roles', $this->generateLink(array('mainview' => 'role', 'roleview' => null, 'roleid' => null)));
      $this->setPlaceHolder('role_add', $this->generateLink(array('mainview' => 'role', 'roleview' => 'add', 'roleid' => null)));
   }

}
