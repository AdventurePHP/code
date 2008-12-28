<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to edit a role.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class edit_controller extends umgtbaseController
   {

      function edit_controller(){
      }


      function transformContent(){

         // get the current role id
         $roleid = RequestHandler::getValue('roleid');

         // initialize the form
         $Form__Edit = &$this->__getForm('RoleEdit');
         $GroupID = &$Form__Edit->getFormElementByName('roleid');
         $GroupID->setAttribute('value',$roleid);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $Role = new GenericDomainObject('Role');
               $Role->setProperty('RoleID',$roleid);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $Role->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveRole($Role);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'role', 'roleview' => '','roleid' => '')));

             // end if
            }
            else{
               $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load group
            $Role = $uM->loadRoleByID($roleid);

            // prefill form
            $DisplayName = &$Form__Edit->getFormElementByName('DisplayName');
            $DisplayName->setAttribute('value',$Role->getProperty('DisplayName'));

            // display form
            $this->setPlaceHolder('RoleEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>