<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::variablen','variablenHandler');


   class edit_controller extends baseController
   {

      function edit_controller(){
      }


      function transformContent(){

         $_LOCALS = variablenHandler::registerLocal(array('userid'));

         $Form__Edit = &$this->__getForm('UserEdit');
         $UserID = &$Form__Edit->getFormElementByName('userid');
         $UserID->setAttribute('value',$_LOCALS['userid']);

         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

         if($Form__Edit->get('isSent') == true){

            if($Form__Edit->get('isValid') == true){

               $Fields = &$Form__Edit->getFormElementsByTagName('form:text');

               $User = new GenericDomainObject('User');
               $User->setProperty('UserID',$_LOCALS['userid']);

               $fieldcount = count($Fields);
               for($i = 0; $i < $fieldcount; $i++){
                  $User->setProperty($Fields[$i]->getAttribute('name'),$Fields[$i]->getAttribute('value'));
                // end for
               }

               $uM->saveUser($User);
               header('Location: ?mainview=user');

             // end if
            }
            else{
               $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());
             // end else
            }

          // end if
         }
         else{

            // load user
            $User = $uM->loadUserByID($_LOCALS['userid']);

            // prefill form
            $FirstName = &$Form__Edit->getFormElementByName('FirstName');
            $FirstName->setAttribute('value',$User->getProperty('FirstName'));

            $LastName = &$Form__Edit->getFormElementByName('LastName');
            $LastName->setAttribute('value',$User->getProperty('LastName'));

            $StreetName = &$Form__Edit->getFormElementByName('StreetName');
            $StreetName->setAttribute('value',$User->getProperty('StreetName'));

            $StreetNumber = &$Form__Edit->getFormElementByName('StreetNumber');
            $StreetNumber->setAttribute('value',$User->getProperty('StreetNumber'));

            $ZIPCode = &$Form__Edit->getFormElementByName('ZIPCode');
            $ZIPCode->setAttribute('value',$User->getProperty('ZIPCode'));

            $City = &$Form__Edit->getFormElementByName('City');
            $City->setAttribute('value',$User->getProperty('City'));

            $EMail = &$Form__Edit->getFormElementByName('EMail');
            $EMail->setAttribute('value',$User->getProperty('EMail'));

            $Mobile = &$Form__Edit->getFormElementByName('Mobile');
            $Mobile->setAttribute('value',$User->getProperty('Mobile'));

            $Username = &$Form__Edit->getFormElementByName('Username');
            $Username->setAttribute('value',$User->getProperty('Username'));

            $Password = &$Form__Edit->getFormElementByName('Password');
            $Password->setAttribute('value',$User->getProperty('Password'));

            // display form
            $this->setPlaceHolder('UserEdit',$Form__Edit->transformForm());

          // end else
         }

       // end function
      }

    // end class
   }
?>