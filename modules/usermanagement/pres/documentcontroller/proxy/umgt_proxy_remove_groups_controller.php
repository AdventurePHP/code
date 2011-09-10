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
import('tools::http', 'HeaderManager');
import('modules::usermanagement::pres::documentcontroller::proxy', 'permission_base_controller');

class umgt_proxy_remove_groups_controller extends permission_base_controller {

   public function transformContent() {

      $proxyId = RequestHandler::getValue('proxyid');
      $form = &$this->getForm(self::$FORM_NAME);

      $proxyIdControl = $form->getFormElementByName('proxyid');
      $proxyIdControl->setAttribute('value', $proxyId);

      $uM = &$this->getManager();
      $proxy = $uM->loadVisibilityDefinitionById($proxyId);
      $proxyType = $uM->loadVisibilityDefinitionType($proxy);

      $this->setPlaceHolder('AppObjectId', $proxy->getProperty('AppObjectId'));
      $this->setPlaceHolder('ProxyType', $proxyType->getProperty('AppObjectName'));

      $groups = $uM->loadGroupsWithVisibilityDefinition($proxy);
      $groupsControl = &$form->getFormElementByName('groups');
      foreach ($groups as $id => $DUMMY) {
         $groupsControl->addOption($groups[$id]->getProperty('DisplayName'), $groups[$id]->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {
         $proxy = new GenericDomainObject('AppProxy');
         $proxy->setObjectId($proxyId);
         $uM->detachGroupsFromVisibilityDefinition($proxy, $this->mapSelectedOptions2DomainObjects('groups', 'Group'));

         HeaderManager::forward(
            $this->generateLink(
               array(
                    'mainview' => 'proxy',
                    'proxyview' => null,
                    'proxyid' => null)
            )
         );

      }

      $form->transformOnPlace();

   }

}

?>