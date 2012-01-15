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
import('modules::usermanagement::pres::documentcontroller::proxy', 'permission_base_controller');

class umgt_proxy_remove_users_controller extends permission_base_controller {

   public function transformContent() {

      $proxyId = RequestHandler::getValue('proxyid');
      $form = &$this->getForm(self::$FORM_NAME);

      $proxyIdControl = $form->getFormElementByName('proxyid');
      $proxyIdControl->setAttribute('value', $proxyId);

      $uM = &$this->getManager();
      $proxy = $uM->loadVisibilityDefinitionById($proxyId);
      $proxyType = $uM->loadVisibilityDefinitionType($proxy);

      $this->setPlaceHolder('AppObjectId', $proxy->getAppObjectId());
      $this->setPlaceHolder('ProxyType', $proxyType->getAppObjectName());

      $users = $uM->loadUsersWithVisibilityDefinition($proxy);
      $usersControl = &$form->getFormElementByName('users');
      foreach ($users as $id => $DUMMY) {
         $usersControl->addOption($users[$id]->getDisplayName(), $users[$id]->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {
         $proxy = new UmgtVisibilityDefinition();
         $proxy->setObjectId($proxyId);
         $uM->detachUsersFromVisibilityDefinition($proxy, $this->mapSelectedOptions2DomainObjects('users', 'UmgtUser'));

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