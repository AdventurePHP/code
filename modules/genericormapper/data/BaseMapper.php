<?php
   import('core::database','connectionManager');
   import('core::logging','Logger');
   import('modules::genericormapper::biz','GenericDomainObject');
   import('modules::genericormapper::data','GenericCriterionObject');


   /**
   *  @package modules::genericormapper::data
   *  @class BaseMapper
   *
   *  Implements the base class for all concrete or-mapper implementations.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   *  Version 0.2, 14.05.2008<br />
   */
   class BaseMapper extends coreObject
   {

      /**
      *  @private
      *  Namespace, where the configuration files are located.
      */
      var $__ConfigNamespace = null;

      /**
      *  @private
      *  Name affix of the configuration files.
      */
      var $__ConfigNameAffix = null;


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


      function BaseMapper(){
      }


      /**
      *  @public
      *
      *  Implements the interface method init() to be able to initialize<br />
      *  the mapper with the service manager.<br />
      *
      *  @param array $InitParams list of initialization parameters
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      *  Version 0.2, 14.05.2008 (mapping table creation moved to AbstractORMapper)<br />
      *  Version 0.3, 31.05.2008 (changed behavior due to refactoring)<br />
      *  Version 0.4, 22.06.2008 (refactored the configuration file entries to gain flexibility)<br />
      *  Version 0.5, 23.06.2008 (mapper now must be instanciated by the factory, that configures the mapper)<br />
      */
      function init($InitParams){

         // set the config namespace
         $this->__ConfigNamespace = $InitParams['ConfigNamespace'];

         // set the config name affix
         $this->__ConfigNameAffix = $InitParams['ConfigNameAffix'];

         // get connection manager
         $cM = &$this->__getServiceObject('core::database','connectionManager');

         // initialize connection
         $this->__DBDriver = &$cM->getConnection($InitParams['ConnectionName']);

       // end function
      }


      /**
      *  @private
      *
      *  Parse the object configuration definition file.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      *  Version 0.2, 31.05.2008 (refactoring of the object definition)<br />
      *  Version 0.3, 22.06.2008 (refactored object configuration adressing)<br />
      */
      function __createMappingTable(){

         // invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('__createMappingTable()');

         // get object configuration
         $ObjectsConfig = &$this->__getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_objects');

         // extract configuration
         $this->__MappingTable = $ObjectsConfig->getConfiguration();

         // resolve definitions
         foreach($this->__MappingTable as $ObjectName => $DUMMY){

            // resolve standard properties, that derive from the definition
            // - table name:
            $this->__MappingTable[$ObjectName]['Table'] = 'ent_'.strtolower($ObjectName);
            // - name of the primary key
            $this->__MappingTable[$ObjectName]['ID'] = $ObjectName.'ID';

          // end foreach
         }

         // stop timer
         $T->stop('__createMappingTable()');

       // end function
      }


      /**
      *  @private
      *
      *  Create the object relation table.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 30.05.2008 (properties are now generated instead of configured explicitly)<br />
      *  Version 0.3, 22.06.2008 (refactored relation configuration adressing)<br />
      */
      function __createRelationTable(){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('__createRelationTable()');

         // Get relation configuration
         $RelationsConfig = &$this->__getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_relations');

         // extract configuration
         $this->__RelationTable = $RelationsConfig->getConfiguration();

         // Resolve definitions
         foreach($this->__RelationTable as $RelationName => $DUMMY){

            // Resolve standard properties, that derive from the definition
            // - table name
            if($this->__RelationTable[$RelationName]['Type'] == 'COMPOSITION'){
               $this->__RelationTable[$RelationName]['Table'] = 'cmp_'.strtolower($RelationName);
             // end if
            }
            else{
               $this->__RelationTable[$RelationName]['Table'] = 'ass_'.strtolower($RelationName);
             // end else
            }

            // - name of the primary key of the source object
            $this->__RelationTable[$RelationName]['SourceID'] = $this->__RelationTable[$RelationName]['SourceObject'].'ID';

            // - name of the primary key of the target object
            $this->__RelationTable[$RelationName]['TargetID'] = $this->__RelationTable[$RelationName]['TargetObject'].'ID';

          // end foreach
         }


         // Stop timer
         $T->stop('__createRelationTable()');

       // end function
      }

    // end class
   }
?>