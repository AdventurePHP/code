<?php
   import('modules::usermanagement::biz','umgtBase');
   import('core::database','connectionManager');


   class umgtBaseRelationMapper extends coreObject
   {

      /**
      *  @private
      *  ID of the current application.
      */
      var $__ApplicationID = null;


      /**
      *  @private
      *  Instance of the database driver.
      */
      var $__DBDriver = null;


      /**
      *  @private
      *  Object mapping table.
      */
      var $__MappingTable = array();


      /**
      *  @private
      *  Object relation table.
      */
      var $__RelationTable = array();


      function umgtBaseRelationMapper(){
      }


      /**
      *  @module init()
      *  @public
      *
      *  Implements the interface method init() to be able to initialize<br />
      *  the mapper with the service manager.<br />
      *
      *  @param int $ApplicationID; id of the current application
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      function init($ApplicationID){

         // Set application id
         $this->__ApplicationID = $ApplicationID;

         // create db driver if necessary
         if($this->__DBDriver === null){

            // get connection manager
            $cM = &$this->__getServiceObject('core::database','connectionManager');
            $this->__DBDriver = &$cM->getConnection('usermanagement');

          // end if
         }

         // create mapping table if necessary
         if(count($this->__MappingTable) == 0){
            $this->__createMappingTable();
          // end if
         }

       // end function
      }


      /**
      *  @module __createMappingTable()
      *  @private
      *
      *  Parse the composition definition string and return a table of mapped
      *  relation definitions like.
      *
      *  $Relations = array(
      *                     'Associations' => array(),
      *                     'Compositions' => array()
      *                     );
      */
      function __createMappingTable(){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('__createMappingTable()');

         // Get object configuration
         $ObjectsConfig = &$this->__getConfiguration('modules::usermanagement','umgt_'.$this->__ApplicationID.'_objects');

         // Extract configuration
         $this->__MappingTable = $ObjectsConfig->getConfiguration();

         // Resolve definitions
         foreach($this->__MappingTable as $ObjectName => $DUMMY){

            // Resolve associations
            if(isset($this->__MappingTable[$ObjectName]['Associations']) && !empty($this->__MappingTable[$ObjectName]['Associations'])){
               $this->__MappingTable[$ObjectName]['Associations'] = $this->__convertPipeString2Array($this->__MappingTable[$ObjectName]['Associations']);
             // end if
            }

            // Resolve compositions
            if(isset($this->__MappingTable[$ObjectName]['Compositions']) && !empty($this->__MappingTable[$ObjectName]['Compositions'])){
               $this->__MappingTable[$ObjectName]['Compositions'] = $this->__convertPipeString2Array($this->__MappingTable[$ObjectName]['Compositions']);
             // end if
            }

            // Resolve attributes
            if(isset($this->__MappingTable[$ObjectName]['Properties']) && !empty($this->__MappingTable[$ObjectName]['Properties'])){
               $this->__MappingTable[$ObjectName]['Properties'] = $this->__convertPipeString2Array($this->__MappingTable[$ObjectName]['Properties']);
             // end if
            }

          // end foreach
         }

         // Stop timer
         $T->stop('__createMappingTable()');

         //echo printObject($this->__MappingTable);

       // end function
      }


      /**
      *  @module __createRelationTable()
      *  @private
      *
      *  Create the object relation table.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function __createRelationTable(){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('__createRelationTable()');

         // Get relation configuration
         $RelationsConfig = &$this->__getConfiguration('modules::usermanagement','umgt_'.$this->__ApplicationID.'_relations');

         // extract configuration
         $this->__RelationTable = $RelationsConfig->getConfiguration();

         // Stop timer
         $T->start('__createRelationTable()');

         //echo printObject($this->__RelationTable);

       // end function
      }


      /**
      *  @module __convertPipeString2Array()
      *  @private
      *
      *  Seperates a string like "name1:value1|name2:value2" into an associative array.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 04.05.2008<br />
      *  Version 0.2, 11.05.2008 (benchmark timer call removed)<br />
      */
      function __convertPipeString2Array($RelationString){

         // Separate string by pipe
         $MappingPairs = explode('|',$RelationString);

         // Initialize params
         $count = count($MappingPairs);
         $Mapping = array();

         for($i = 0; $i < $count; $i++){

            // Separate by double point
            $Temp = explode(':',$MappingPairs[$i]);
            $Mapping[trim($Temp[0])] = trim($Temp[1]);

          // end for
         }

         // Return mapping array
         return $Mapping;

       // end function
      }






      function __createRelation($Name,$SourceID,$TargetID){

         // read configuration for relation

       // end function
      }



    // end class
   }
?>