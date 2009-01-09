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


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('GroupAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = variablenHandler::registerLocal(array('DisplayName'));

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $Group = new GenericDomainObject('Group');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $Group->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->saveGroup($Group);
            header('Location: ?mainview=group');

          // end else
         }
         $this->setPlaceHolder('GroupAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>