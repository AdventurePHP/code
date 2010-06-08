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

   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');
   import('tools::request','RequestHandler');

   /**
    * @package modules::usermanagement::pres::documentcontroller::proxy
    * @class umgt_proxyadd_controller
    *
    * Let's the user create a new visibility entry (proxy type <-> proxy <-> user+group).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2010<br />
    */
   class umgt_proxyadd_controller extends umgt_base_controller {

      public function transformContent(){

         $uM = &$this->getManager();
         $form = &$this->__getForm('visibilitydef');

         // prefill mode if "proxytypeid" and "appobjectid" are given
         $proxyTypeId = RequestHandler::getValue('proxytypeid');
         $appObjectId = RequestHandler::getValue('appobjectid');
         $proxyId = RequestHandler::getValue('proxyid');
         $selectedUsers = array();
         $selectedGroups = array();
         if($proxyTypeId != null && $appObjectId != null){
            $proxy = new GenericDomainObject('AppProxy');
            $proxy->setProperty('AppProxyID',$proxyId);
            $selectedUsers = $uM->loadUsersWithVisibilityDefinition($proxy);
            $selectedGroups = $uM->loadGroupsWithVisibilityDefinition($proxy);
         }

         // load the defined visibility types
         $proxyTypes = $uM->loadProxyTypes();
         $typeElement = &$form->getFormElementByName('proxytypeid');

         foreach($proxyTypes as $proxyType){
            $typeElement->addOption(
                    $proxyType->getProperty('AppObjectName'),
                    $proxyType->getProperty('AppProxyTypeID')
            );
         }

         // load users
         $userList = $uM->getPagedUserList();
         $usersElement = &$form->getFormElementByName('users');
         foreach($userList as $user){
            $usersElement->addOption(
                    $user->getProperty('DisplayName'),
                    $user->getProperty('UserID')
            );
         }
         foreach($selectedUsers as $selectedUser){
            $usersElement->setOption2Selected($selectedUser->getProperty('UserID'));
         }

         // load groups
         $groups = $uM->getPagedGroupList();
         $groupsElement = &$form->getFormElementByName('groups');
         foreach($groups as $group){
            $groupsElement->addOption(
                    $group->getProperty('DisplayName'),
                    $group->getProperty('GroupID')
            );
         }
         foreach($selectedGroups as $selectedGroup){
            $groupsElement->setOption2Selected($selectedGroup->getProperty('GroupID'));
         }

         // store visibility definition
         if($form->isSent() && $form->isValid()){

            // setup type
            $type = new GenericDomainObject('AppProxyType');
            $type->setProperty(
                    'AppProxyTypeID',
                    $form->getFormElementByName('proxytypeid')->getSelectedOption()->getAttribute('value')
            );

            // setup proxy
            $definition = new GenericDomainObject('AppProxy');
            $definition->setProperty(
                    'AppObjectId',
                    $form->getFormElementByName('appobjectid')->getAttribute('value')
            );

            // setup users
            $users = array();
            foreach($form->getFormElementByName('users')->getSelectedOptions() as $option){
               $user = new GenericDomainObject('User');
               $user->setProperty('UserID',$option->getAttribute('value'));
               $users[] = $user;
               unset($user);
            }

            // setup groups
            $groups = array();
            foreach($form->getFormElementByName('groups')->getSelectedOptions() as $option){
               $group = new GenericDomainObject('Group');
               $group->setProperty('GroupID',$option->getAttribute('value'));
               $groups[] = $group;
               unset($group);
            }

            $uM->createVisibilityDefinition($type,$definition,$users,$groups);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'proxy', 'proxyview' => null,'proxytypeid' => null)));

         }
         else{
            $form->transformOnPlace();
         }

       // end function
      }

    // end class
   }
?>