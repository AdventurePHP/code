<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to add a user.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class add_controller extends umgtbaseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('UserForm');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            $FormValues = RequestHandler::getValues(array(
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
            $user = new GenericDomainObject('User');

            foreach($FormValues as $Key => $Value){

               if(!empty($Value)){
                  $user->setProperty($Key,$Value);
                // end if
               }

             // end foreach
            }

            $uM->saveUser($user);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user','userview' => '')));

          // end else
         }

         $this->setPlaceHolder('UserAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>