<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class umgt_delete_controller
   *
   *  Implements the controller to delete a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class umgt_delete_controller extends umgtbaseController
   {

      function transformContent(){

         // get the group id from the request
         $groupid = RequestHandler::getValue('groupid');

         // load the current group and print the display name
         $uM = &$this->__getAndInitServiceObject('modules::usermanagement::biz','umgtManager','Default');
         $Group = $uM->loadGroupByID($groupid);
         $this->setPlaceHolder('DisplayName', $Group->getProperty('DisplayName'));

         // prepare the forms and execute action
         $Form__No = &$this->__getForm('GroupDelNo');
         $Form__Yes = &$this->__getForm('GroupDelYes');

         if($Form__Yes->isSent()){
            $Group = new GenericDomainObject('Group');
            $Group->setProperty('GroupID',$groupid);
            $uM->deleteGroup($Group);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));
          // end if
         }
         elseif($Form__No->isSent()){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group','groupview' => '','groupid' => '')));
          // end elseif
         }
         else{
            $Form__No->transformOnPlace();
            $Form__Yes->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>