<?php
   import('modules::usermanagement::biz','umgtBase');


   /**
   *  @package modules::usermanagement::biz
   *  @module umgtApplication
   *
   *  Domain object for application.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 04.05.2008<br />
   */
   class umgtApplication extends umgtBase
   {

      var $__MappingTable = array();
      var $__RelationTable = array();
      var $__ObjectName = 'Application';


      function umgtApplication(){
      }


      /**
      *  @module init()
      *  @public
      *
      *  Implements the init() method of the class coreObject.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 04.05.2008<br />
      */
      function init(){
         $this->__parseRelationTable();
       // end function
      }


      /**
      *
      *
      *
      *
      */
      function getComposedObjects($CompName,$Start = null,$Count = null){

         // Get current object ID
         $ID = $this->getProperty($this->__MappingTable[$this->__ObjectName]['ID']);
         if($ID === null){
            return null;
          // end if
         }
         else{
            //echo printObject($this->__MappingTable);
            //echo '<br />getComposedObjects('.$CompName.')';
            /*echo '<br />CompositionKey: '.*/$CompKey = $this->__MappingTable[$this->__ObjectName]['Compositions'][$CompName];
            return $this->__getRelatedObjects($CompKey,$ID);
          // end else
         }

       // end function
      }


      /**
      *
      *
      *
      *
      */
      function __getRelatedObjects($RelationKey,$ObjectID,$Start = null,$Count = null){

         // Get relation information
         //echo printObject($this->__MappingTable);
         //echo printObject($this->__RelationTable);
         //$RelationKey = $this->__MappingTable[$this->__ObjectName]['Compositions'][$RelationName];
         /*echo printObject(*/$Relation = $this->__RelationTable[$RelationKey]/*)*/;

         // Gather relation parameters
         $TargetObject = $Relation['TargetObject'];
         $SourceName = $Relation['SourceName'];
         $TargetName = $Relation['TargetName'];
         $Table = $Relation['Table'];
         $Object = $Relation['Object'];
         $Class = $this->__MappingTable[$Object]['Class'];


         // Gather target object paramaters
         //echo printObject($this->__MappingTable[$TargetObject]);

         // Create select
         //echo '<br />';
         /*echo */$select = 'SELECT '.$TargetName.' FROM '.$Table.' WHERE '.$SourceName. ' = \''.$ObjectID.'\'';
         //echo '<br />';
         $cM = &$this->__getServiceObject('core::database','connectionManager');
         $SQL = &$cM->getConnection('usermanagement');
         $result = $SQL->executeTextStatement($select);
         $IDs = array();
         while($data = $SQL->fetchData($result)){
            $Objects[] = $this->__DataComponent->loadUserByID($data[$TargetName]);
          // end while
         }

         // return result
         return $Objects;

       // end function
      }


      /**
      *  Gets the configuration section for the current object.
      *
      */
      function &__getConfigurationByObjectName(&$Object){

         // Get class name
         $ClassName = get_class($Object);

         // Search for suitable section name
         $Section = null;
         foreach($this->__MappingTable as $Key => $DUMMY){

            if(isset($this->__MappingTable[$Key]['Class']) && $this->__MappingTable[$Key]['Class'] == $ClassName){
               $Section = &$this->__MappingTable[$Key];
             // end if
            }

          // end foreach
         }

         // Return section
         return $Section;

       // end function
      }


      function getAssociatedObjects($CompKey,$Start = null,$Count = null){

       // end function
      }


      function getGroups(){
         // lazy load groups by data component or return them

       // end function
      }


      function getRoles(){
         // lazy load Roles by data component or return them
      }


      function getRoleByID($RoleID){
         // lazy load Roles by data component or return them
      }


      function getRoleByName($RoleName){
         // lazy load Roles by data component or return them
      }

    // end class
   }
?>