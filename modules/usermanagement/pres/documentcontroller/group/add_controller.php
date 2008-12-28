<?php
   import('modules::usermanagement::biz','umgtManager');
   import('modules::usermanagement::pres::documentcontroller','umgtbaseController');
   import('tools::http','HeaderManager');


   /**
   *  @namespace modules::usermanagement::pres::documentcontroller
   *  @class delete_controller
   *
   *  Implements the controller to add a group.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.12.2008<br />
   */
   class add_controller extends umgtbaseController
   {

      function add_controller(){
      }


      function transformContent(){

         $Form__Add = &$this->__getForm('GroupAdd');
         if($Form__Add->get('isSent') == true && $Form__Add->get('isValid') == true){

            // get the business object
            $uM = &$this->__getServiceObject('modules::usermanagement::biz','umgtManager');

            // get the form element's value
            $displayName = &$Form__Add->getFormElementByName('DisplayName');
            $Group = new GenericDomainObject('Group');
            $Group->setProperty('DisplayName',$displayName->getAttribute('value'));
            $uM->saveGroup($Group);

            // redirect to the desired view
            HeaderManager::forward($this->__generateLink(array('mainview' => 'group', 'groupview' => '')));

          // end else
         }
         $this->setPlaceHolder('GroupAdd',$Form__Add->transformForm());

       // end function
      }

    // end class
   }
?>