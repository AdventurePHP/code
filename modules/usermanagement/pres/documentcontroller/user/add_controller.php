<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class add_controller extends baseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('UserAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = variablenHandler::registerLocal(
                                                          array(
                                                                'DisplayName',
                                                                'FirstName',
                                                                'LastName',
                                                                'StreetName',
                                                                'StreetNumber',
                                                                'ZIPCode',
                                                                'City',
                                                                'EMail',
                                                                'Phone',
                                                                'Mobile',
                                                                'Username',
                                                                'Password'
                                                               )
                                                         );

            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
            $User = new GenericDomainObject('User');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $User->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->saveUser($User);
            header('Location: ?mainview=user');

          // end else
         }
         $this->setPlaceHolder('UserAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>