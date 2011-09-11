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
import('tools::request', 'RequestHandler');

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_edit_controller
 *
 * Implements the controller to edit a permission.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class umgt_edit_controller extends umgt_base_controller {

   public function transformContent() {

      // get current permission id
      $permissionId = RequestHandler::getValue('permissionid');

      // initialize the form
      $form = &$this->getForm('PermissionEdit');
      $permissionIdControl = &$form->getFormElementByName('permissionid');
      $permissionIdControl->setAttribute('value', $permissionId);

      $uM = &$this->getManager();

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $fields = &$form->getFormElementsByTagName('form:text');
            $permission = new UmgtPermission();
            $permission->setObjectId($permissionId);

            $fieldCount = count($fields);
            for ($i = 0; $i < $fieldCount; $i++) {
               $permission->setProperty($fields[$i]->getAttribute('name'), $fields[$i]->getAttribute('value'));
            }

            $uM->savePermission($permission);
            HeaderManager::forward($this->generateLink(array('mainview' => 'permission', 'permissionview' => '', 'permissionid' => '')));

         } else {
            $form->transformOnPlace();
         }

      } else {

         // load permission
         $permission = $uM->loadPermissionByID($permissionId);

         // prefill form
         $displayName = &$form->getFormElementByName('DisplayName');
         $displayName->setAttribute('value', $permission->getDisplayName());

         $name = &$form->getFormElementByName('Name');
         $name->setAttribute('value', $permission->getName());

         $value = &$form->getFormElementByName('Value');
         $value->setAttribute('value', $permission->getValue());

         // display form
         $form->transformOnPlace();

      }

   }

}

?>