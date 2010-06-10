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

   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgt_base_controller');
   import('tools::http','HeaderManager');

   /**
    * @package modules::usermanagement::pres::documentcontroller
    * @class umgt_delete_controller
    *
    * Implements the controller to delete a user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgt_delete_controller extends umgt_base_controller {

      public function transformContent(){

         $userid = RequestHandler::getValue('userid');
         $uM = &$this->getManager();
         $User = $uM->loadUserById($userid);
         $this->setPlaceHolder('DisplayName', $User->getProperty('DisplayName'));
         $Form__No = &$this->__getForm('UserDelNo');
         $Form__Yes = &$this->__getForm('UserDelYes');

         if($Form__Yes->isSent()){

            $User = new GenericDomainObject('User');
            $User->setProperty('UserID',$userid);
            $uM->deleteUser($User);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

          // end if
         }
         elseif($Form__No->isSent()){
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));
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