<?php
   import('modules::usermanagement::biz','umgtManager');
   import('tools::request','RequestHandler');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class edit_controller
   *
   *  Implements the controller to add a user to a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class add2group_controller extends umgtbaseController
   {

      function add2group_controller(){
      }


      /**
      *  @public
      *
      *  Displays the views to add a user to groups.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      *  Version 0.2, 29.12.2008 (Applied API change of the usermanagement manager)<br />
      */
      function transformContent(){

         // init the form and load the relevant groups
         $userid = RequestHandler::getValue('userid');
         $Form__Group = &$this->__getForm('Group');
         $Group = &$Form__Group->getFormElementByName('Group[]');
         $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');
         $user = $uM->loadUserById($userid);
         $Groups = $uM->loadGroupsNotWithUser($user);
         $count = count($Groups);

         // display a note, if there are no groups to add the user to
         if($count == 0) {
            $Template = &$this->__getTemplate('NoMoreGroups');
            $Template->transformOnPlace();
            return true;
          // end if
         }

         // add the groups to the option field
         for($i = 0; $i < $count; $i++){
            $Group->addOption($Groups[$i]->getProperty('DisplayName'),$Groups[$i]->getProperty('GroupID'));
          // end for
         }

         // handle the click event
         if($Form__Group->get('isSent') && $Form__Group->get('isValid')){

            $options = &$Group->getSelectedOptions();
            $count = count($options);

            $newGroups = array();
            for($i = 0; $i < $count; $i++){
               $newGroup = new GenericDomainObject('Group');
               $newGroup->setProperty('GroupID',$options[$i]->getAttribute('value'));
               $newGroups[] = $newGroup;
               unset($newGroup);
             // end for
            }

            $uM->assignUser2Groups($user,$newGroups);
            HeaderManager::forward($this->__generateLink(array('mainview' => 'user', 'userview' => '','userid' => '')));

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