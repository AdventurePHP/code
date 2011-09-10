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
      foreach ($types as $type) {
         /* @var $type GenericDomainObject */
         $select->addOption($type->getProperty('AppObjectName'), $type->getObjectId());
      }

      $form->transformOnPlace();

      // load definition list managed by the filter in case it has been applied
      if ($form->isSent()) {
         $typeId = $form
               ->getFormElementByName('proxytypeid')
               ->getSelectedOption()
               ->getAttribute('value');
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

         $template->setPlaceHolder('AppObjectId', $proxy->getProperty('AppObjectId'));

         $type = $uM->loadVisibilityDefinitionType($proxy);
         $template->setPlaceHolder('AppProxyType', $type->getProperty('AppObjectName'));

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

      // in case no proxy object is found for the present filter, place a custom text
      // denoting this situation.
      if (count($proxies) == 0) {
         $buffer = $this->getTemplate('NoProxy')->transformTemplate();
      }

      $this->setPlaceHolder('ProxyList', $buffer);

   }


   private function getUsers(GenericORMapperDataObject $proxy) {
      $users = $this->getManager()->loadUsersWithVisibilityDefinition($proxy);
      $userList = array();
      foreach ($users as $user) {
         $userList[] = $user->getProperty('Username');
      }
      return implode(', ', $userList);
   }

   private function getGroups(GenericORMapperDataObject $proxy) {
      $groups = $this->getManager()->loadGroupsWithVisibilityDefinition($proxy);
      $groupList = array();
      foreach ($groups as $group) {
         $groupList[] = $group->getProperty('DisplayName');
      }
      return implode(', ', $groupList);
   }

}

?>