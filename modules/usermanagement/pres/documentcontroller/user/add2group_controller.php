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


   class add2group_controller extends baseController
   {

      function add2group_controller(){
      }


      function transformContent(){

         $Form__Group = &$this->__getForm('Group');
         $Group = &$Form__Group->getFormElementByName('Group[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $_LOCALS = variablenHandler::registerLocal(array('userid'));
         $Groups = $uM->loadGroupList($_LOCALS['userid'],true);
         $count = count($Groups);

         for($i = 0; $i < $count; $i++){
            $Group->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         if($Form__Group->get('isSent') && $Form__Group->get('isValid')){

            $Options = &$Group->getSelectedOptions();
            $count = count($Options);

            $NewGroups = array();
            for($i = 0; $i < $count; $i++){
               $NewGroups[] = $Options[$i]->getAttribute('value');
             // end for
            }


            $uM->addUser2Groups($_LOCALS['userid'],$NewGroups);
            header('Location: ?mainview=user');

          // end if
         }
         else{
            $Form__Group->transformOnPlace();
          // end else
         }

       // end function
      }

    // end class
   }
?>