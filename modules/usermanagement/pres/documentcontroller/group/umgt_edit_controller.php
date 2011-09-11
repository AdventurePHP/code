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
import('tools::request', 'RequestHandler');
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');
import('tools::http', 'HeaderManager');

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_edit_controller
 *
 * Implements the controller to edit a group.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.12.2008<br />
 */
class umgt_edit_controller extends umgt_base_controller {

   public function transformContent() {

      $groupId = RequestHandler::getValue('groupid');

      $form = &$this->getForm('GroupEdit');
      $groupIdField = &$form->getFormElementByName('groupid');
      $groupIdField->setAttribute('value', $groupId);

      $uM = &$this->getManager();

      if ($form->isSent() == true) {

         if ($form->isValid() == true) {

            $fields = &$form->getFormElementsByTagName('form:text');

            $group = new UmgtGroup();
            $group->setObjectId($groupId);

            $fieldCount = count($fields);
            for ($i = 0; $i < $fieldCount; $i++) {
               $group->setProperty($fields[$i]->getAttribute('name'), $fields[$i]->getAttribute('value'));
            }
            $uM->saveGroup($group);
            HeaderManager::forward($this->generateLink(array('mainview' => 'group', 'groupview' => '', 'groupid' => '')));

         } else {
            $this->setPlaceHolder('GroupEdit', $form->transformForm());
         }

      } else {

         // load group
         $group = $uM->loadGroupByID($groupId);

         // prefill form
         $displayName = &$form->getFormElementByName('DisplayName');
         $displayName->setAttribute('value', $group->getProperty('DisplayName'));

         // display form
         $this->setPlaceHolder('GroupEdit', $form->transformForm());

      }

   }

}

?>