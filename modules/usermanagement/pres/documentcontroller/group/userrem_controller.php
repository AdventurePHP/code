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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class userrem_controller extends baseController
   {

      function userrem_controller(){
      }


      function transformContent(){

         $Form__User = &$this->__getForm('User');
         $User = &$Form__User->getFormElementByName('User');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $Users = $uM->loadUserList();
         $count = count($Users);

         for($i = 0; $i < $count; $i++){
            $User->addOption($Users[$i]->getProperty('LastName').', '.$Users[$i]->getProperty('FirstName'),$Users[$i]->getProperty('UserID'));
          // end for
         }

         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $Option = &$User->getSelectedOption();
            $UserID = $Option->getAttribute('value');
            $_LOCALS = variablenHandler::registerLocal(array('groupid'));
            $uM->removeUserFromGroup($UserID,$_LOCALS['groupid']);
            header('Location: ?mainview=group');

          // end if
         }
         else{
            $Form__User->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>