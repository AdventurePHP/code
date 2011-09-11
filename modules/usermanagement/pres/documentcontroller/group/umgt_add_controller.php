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
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');
import('tools::http', 'HeaderManager');

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_add_controller
 *
 * Implements the controller to add a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class umgt_add_controller extends umgt_base_controller {

   public function transformContent() {

      $formAdd = &$this->getForm('GroupAdd');
      if ($formAdd->isSent() == true && $formAdd->isValid() == true) {

         // get the business object
         $uM = &$this->getManager();

         // get the form element's value
         $displayName = &$formAdd->getFormElementByName('DisplayName');
         $group = new UmgtGroup();
         $group->setDisplayName($displayName->getAttribute('value'));
         $uM->saveGroup($group);

         // redirect to the desired view
         HeaderManager::forward($this->generateLink(array('mainview' => 'group', 'groupview' => '')));

      }
      $this->setPlaceHolder('GroupAdd', $formAdd->transformForm());

   }

}

?>