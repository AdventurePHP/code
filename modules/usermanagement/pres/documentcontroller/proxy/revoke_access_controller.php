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
   import('tools::request','RequestHandler');
   import('tools::http','HeaderManager');

   /**
    * @package modules::usermanagement::pres::documentcontroller::proxy
    * @class umgt_revoke_access_controller
    *
    * Revokes a visibility permission for a given user or group.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   class umgt_revoke_access_controller extends umgt_base_controller {

      public function transformContent(){

         $uM = &$this->getManager();

         $objectId = RequestHandler::getValue('objectid');
         $objectType = RequestHandler::getValue('objecttype');
         $proxyId = RequestHandler::getValue('proxyid');

         $proxy = new GenericDomainObject('AppProxy');
         $proxy->setObjectId($proxyId);

         $object = new GenericDomainObject($objectType);
         $object->setObjectId($objectId);

         $formYes = &$this->__getForm('RevokeYes');
         $formNo = &$this->__getForm('RevokeNo');

         if($formYes->isSent()){
            
            if($objectType == 'User'){
               $uM->detachUsersFromVisibilityDefinition($proxy,array($object));
            }
            else {
               $uM->detachGroupsFromVisibilityDefinition($proxy,array($object));
            }

          // end if
         }
         elseif($formNo->isSent()){
         }
         else{

            $this->setPlaceHolder('object_type',strtolower($objectType));

            if($objectType == 'User'){
               $object = $this->getManager()->loadUserByID($objectId);
            }
            else {
               $object = $this->getManager()->loadGroupByID($objectId);
            }
            $this->setPlaceHolder('displayname',$object->getProperty('DisplayName'));
            $this->setPlaceHolder('proxyid',$proxyId);

            $proxyType = $uM->loadVisibilityDefinitionType($proxy);
            $this->setPlaceHolder('proxytype',$proxyType->getProperty('AppObjectName'));

            $formYes->transformOnPlace();
            $formNo->transformOnPlace();
            return;
         }

         HeaderManager::forward($this->__generateLink(
                    array(
                       'mainview' => 'proxy',
                       'proxyview' => 'details',
                       'proxyid' => $proxyId
                       )
                    )
                 );

       // end function
      }

    // end class
   }
?>