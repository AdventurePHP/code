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

/**
 * @package modules::usermanagement::pres::documentcontroller::proxy
 * @class umgt_proxy_list_controller
 *
 * Implements the controller listing the existing proxy objects.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 19.04.2010<br />
 */
class umgt_proxy_list_controller extends umgt_base_controller {

   public function transformContent() {

      // display filter form
      $uM = &$this->getManager();
      $form = &$this->getForm('type-filter');

      $types = $uM->loadVisibilityDefinitionTypes();
      $select = &$form->getFormElementByName('proxytypeid');
      /* @var $select form_taglib_select */

      // add default option that deletes any filter
      $select->addOption(
         $this->getConfiguration('modules::usermanagement::pres', 'labels.ini')
               ->getSection($this->getLanguage())
               ->getValue('frontend.proxy.list.delete-filter.label'), 0);

      foreach ($types as $type) {
         /* @var $type UmgtVisibilityDefinition */
         $select->addOption($type->getAppObjectName(), $type->getObjectId());
      }

      $form->transformOnPlace();

      // load definition list managed by the filter in case it has been applied
      if ($form->isSent()) {
         $typeId = $form
               ->getFormElementByName('proxytypeid')
               ->getSelectedOption()
               ->getValue();
         $type = $uM->loadVisibilityDefinitionTypeById($typeId);

         // By convention: the filter can be removed by applying null.
         // In case $typeId is "0", null is returned by loadVisibilityDefinitionTypeById()
         // and this is what we want here.
         $proxies = $uM->getPagedVisibilityDefinitionList($type);
      } else {
         $proxies = $uM->getPagedVisibilityDefinitionList();
      }

      $buffer = (string)'';
      $template = &$this->getTemplate('Proxy');
      foreach ($proxies as $proxy) {

         $proxyId = $proxy->getObjectId();

         $type = $uM->loadVisibilityDefinitionType($proxy);
         $template->getLabel('proxy-def')
               ->setPlaceHolder('app-object-id', $proxy->getAppObjectId())
               ->setPlaceHolder('app-proxy-type', $type->getAppObjectName());

         $template->setPlaceHolder('Users', $this->getUsers($proxy));
         $template->setPlaceHolder('Groups', $this->getGroups($proxy));

         $template->setPlaceHolder('proxy_details', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'details', 'proxyid' => $proxyId)));
         $template->setPlaceHolder('proxy_delete', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'proxydelete', 'proxyid' => $proxyId)));

         $template->setPlaceHolder('proxy_add_user', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'proxy_add_users', 'proxyid' => $proxyId)));
         $template->setPlaceHolder('proxy_remove_user', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'proxy_remove_users', 'proxyid' => $proxyId)));
         $template->setPlaceHolder('proxy_add_group', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'proxy_add_groups', 'proxyid' => $proxyId)));
         $template->setPlaceHolder('proxy_remove_group', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'proxy_remove_groups', 'proxyid' => $proxyId)));

         $buffer .= $template->transformTemplate();
      }

      $this->setPlaceHolder('ProxyList', $buffer);

   }

   private function getUsers(UmgtVisibilityDefinition $proxy) {
      $users = $this->getManager()->loadUsersWithVisibilityDefinition($proxy);
      $userList = '<ul>';
      foreach ($users as $user) {
         $userList .= '<li>' . $user->getUsername() . '</li>';
      }
      return $userList . '</ul>';
   }

   private function getGroups(UmgtVisibilityDefinition $proxy) {
      $groups = $this->getManager()->loadGroupsWithVisibilityDefinition($proxy);
      $groupList = '<ul>';
      foreach ($groups as $group) {
         $groupList .= '<li>' . $group->getDisplayName() . '</li>';
      }
      return $groupList . '</ul>';
   }

}
