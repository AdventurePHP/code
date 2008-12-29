<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class ass2user_controller
   *
   *  Implements the controller to assign a role to a user.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   *  Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
   */
   class ass2user_controller extends umgtbaseController
   {

      function ass2user_controller(){
      }


      /**
      *  @public
      *
      *  Displays the view to assign a role to a user.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.12.2008<br />
      */
      function transformContent(){

         // get role id
         $roleid = RequestHandler::getValue('roleid');

         // initialize the form
         $Form__User = &$this->__getForm('User');
         $user = &$Form__User->getFormElementByName('User[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $role = $uM->loadRoleById($roleid);
         $users = $uM->loadUsersNotWithRole($role);
         $count = count($users);

         // display a hint, if a role already assigned to all users
         if($count == 0){
           $template = &$this->__getTemplate('NoMoreUser');
           $template->transformOnPlace();
           return true;
          // end if
         }

         // fill multiselect field
         for($i = 0; $i < $count; $i++){
            $user->addOption($users[$i]->getProperty('LastName').', '.$users[$i]->getProperty('FirstName'),$users[$i]->getProperty('UserID'));
          // end for
         }

         // assign role to the desired users
         if($Form__User->get('isSent') && $Form__User->get('isValid')){

            $options = &$user->getSelectedOptions();
            $newUsers = array();

            for($i = 0; $i < count($options); $i++){
               $newUser = new GenericDomainObject('User');
               $newUser->setProperty('UserID',$options[$i]->getAttribute('value'));
               $newUsers[] = $newUser;
               unset($newUser);
             // end for
            }

            $uM->assignRole2Users($role,$newUsers);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

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