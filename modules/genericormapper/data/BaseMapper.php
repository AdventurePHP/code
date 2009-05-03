<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::database','connectionManager');


   /**
   *  @namespace modules::genericormapper::data
   *  @class BaseMapper
   *
   *  Implements the base class for all concrete or-mapper implementations.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   *  Version 0.2, 14.05.2008<br />
   *  Version 0.3, 26.10.2008 (Added the addMappingConfiguration() and addRelationConfiguration() methods)<br />
   *  Version 0.4, 30.12.2008 (Prettified the benchmark ids)<br />
   */
   class BaseMapper extends coreObject
   {

      /**
      *  @protected
      *  Namespace, where the configuration files are located.
      */
      protected $__ConfigNamespace = null;

      /**
      *  @protected
      *  Name affix of the configuration files.
      */
      protected $__ConfigNameAffix = null;

      /**
      *  @protected
      *  Instance of the database driver.
      */
      protected $__DBDriver = null;

      /**
      *  @protected
      *  Object mapping table.
      */
      protected $__MappingTable = array();

      /**
      *  @protected
      *  Object relation table.
      */
      protected $__RelationTable = array();

      /**
      *  @protected
      *  Indicates, if a additional configuration was already imported.
      */
      protected $__importedConfigCache = array();

      /**
       * @protected
       * Indicates, whether the generated statements should be logged for debugging purposes.
       */
      protected $__LogStatements = false;


      function BaseMapper(){
      }


      /**
      *  @public
      *
      *  Implements the interface method init() to be able to initialize the mapper with the
      *  service manager.
      *
      *  @param array $initParams list of initialization parameters
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      *  Version 0.2, 14.05.2008 (mapping table creation moved to AbstractORMapper)<br />
      *  Version 0.3, 31.05.2008 (changed behavior due to refactoring)<br />
      *  Version 0.4, 22.06.2008 (refactored the configuration file entries to gain flexibility)<br />
      *  Version 0.5, 23.06.2008 (mapper now must be instanciated by the factory, that configures the mapper)<br />
      *  Version 0.6, 03.05.2009 (added the LogStatements param)<br />
      */
      function init($initParams){

         // set the config namespace
         $this->__ConfigNamespace = $initParams['ConfigNamespace'];

         // set the config name affix
         $this->__ConfigNameAffix = $initParams['ConfigNameAffix'];

         // get connection manager
         $cM = &$this->__getServiceObject('core::database','connectionManager');

         // initialize connection
         $this->__DBDriver = &$cM->getConnection($initParams['ConnectionName']);

         // set debug mode, if desired
         $this->__LogStatements = $initParams['LogStatements'];

       // end function
      }


      /**
      *  @protected
      *
      *  Parse the object configuration definition file.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      *  Version 0.2, 31.05.2008 (Refactoring of the object definition)<br />
      *  Version 0.3, 22.06.2008 (Refactored object configuration adressing)<br />
      *  Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateMappingItem() method)<br />
      */
      protected function __createMappingTable(){

         // invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('BaseMapper::__createMappingTable()');

         // get object configuration
         $ObjectsConfig = &$this->__getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_objects');

         // extract configuration
         $this->__MappingTable = $ObjectsConfig->getConfiguration();

         // resolve definitions
         foreach($this->__MappingTable as $objectName => $DUMMY){
            $this->__MappingTable[$objectName] = $this->__generateMappingItem($objectName,$this->__MappingTable[$objectName]);
          // end foreach
         }

         // stop timer
         $T->stop('BaseMapper::__createMappingTable()');

       // end function
      }


      /**
      *  @protected
      *
      *  Create the object relation table.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 30.05.2008 (properties are now generated instead of configured explicitly)<br />
      *  Version 0.3, 22.06.2008 (refactored relation configuration adressing)<br />
      *  Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateRelationItem() method)<br />
      */
      protected function __createRelationTable(){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('BaseMapper::__createRelationTable()');

         // Get relation configuration
         $RelationsConfig = &$this->__getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_relations');

         // extract configuration
         $this->__RelationTable = $RelationsConfig->getConfiguration();

         // Resolve definitions
         foreach($this->__RelationTable as $relationName => $DUMMY){
            $this->__RelationTable[$relationName] = $this->__generateRelationItem($relationName,$this->__RelationTable[$relationName]);
          // end foreach
         }

         // Stop timer
         $T->stop('BaseMapper::__createRelationTable()');

       // end function
      }


      /**
      *  @public
      *
      *  Imports additional mapping information.
      *
      *  @param string $configNamespace the desired configuration namespace
      *  @param string $configNameAffix the configuration affix of the desired configuration
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.10.2008<br />
      */
      function addMappingConfiguration($configNamespace,$configNameAffix){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('BaseMapper::addMappingConfiguration()');

         // add config, if not already included
         $cacheKey = md5($configNamespace.$configNameAffix.'_objects');
         if(!isset($this->__importedConfigCache[$cacheKey])){

            // import and merge config
            $addConfig = &$this->__getConfiguration($configNamespace,$configNameAffix.'_objects');
            $addObjects = $addConfig->getConfiguration();
            foreach($addObjects as $objectName => $DUMMY){

               if(!isset($this->__MappingTable[$objectName])){
                  $this->__MappingTable[$objectName] = $this->__generateMappingItem($objectName,$addObjects[$objectName]);
                // end else
               }

             // end foreach
            }

            // mark object config as cached
            $this->__importedConfigCache[$cacheKey] = true;

          // end if
         }

         // Stop timer
         $T->stop('BaseMapper::addMappingConfiguration()');

       // end function
      }


      /**
      *  @public
      *
      *  Imports additional relation information.
      *
      *  @param string $configNamespace the desired configuration namespace
      *  @param string $configNameAffix the configuration affix of the desired configuration
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.10.2008<br />
      */
      function addRelationConfiguration($configNamespace,$configNameAffix){

         // Invoke benchmark timer
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('BaseMapper::addRelationConfiguration()');

         // add config, if not already included
         $cacheKey = md5($configNamespace.$configNameAffix.'_relations');
         if(!isset($this->__importedConfigCache[$cacheKey])){

            // import and merge config
            $addConfig = &$this->__getConfiguration($configNamespace,$configNameAffix.'_relations');
            $addRelations = $addConfig->getConfiguration();
            foreach($addRelations as $relationName => $DUMMY){

               if(!isset($this->__RelationTable[$relationName])){
                  $this->__RelationTable[$relationName] = $this->__generateRelationItem($relationName,$addRelations[$relationName]);
                // end else
               }

             // end foreach
            }

            // mark relation config as cached
            $this->__importedConfigCache[$cacheKey] = true;

          // end if
         }

         // Stop timer
         $T->stop('BaseMapper::addRelationConfiguration()');

       // end function
      }


      /**
      *  @protected
      *
      *  Resolves the table and primary key name within the object definition configuration.
      *
      *  @param string $objectName nam of the current configuration section (=name of the current object)
      *  @param array $objectSection current object definition params
      *  @return array $resolvedObjectSection enhanced object definition
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.10.2008<br />
      */
      protected function __generateMappingItem($objectName,$objectSection){

         // resolve standard properties, that derive from the definition
         // - table name:
         $objectSection['Table'] = 'ent_'.strtolower($objectName);
         // - name of the primary key
         $objectSection['ID'] = $objectName.'ID';

         // return section
         return $objectSection;

       // end function
      }


      /**
      *  @protected
      *
      *  Resolves the table name, source and target id of the relation definition within the relation configuration.
      *
      *  @param string $relationName nam of the current configuration section (=name of the current relation)
      *  @param array $relationSection current relation definition params
      *  @return array $resolvedRelationSection enhanced relation definition
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.10.2008<br />
      */
      protected function __generateRelationItem($relationName,$relationSection){

         // Resolve standard properties, that derive from the definition
         // - table name
         if($relationSection['Type'] == 'COMPOSITION'){
            $relationSection['Table'] = 'cmp_'.strtolower($relationName);
          // end if
         }
         else{
            $relationSection['Table'] = 'ass_'.strtolower($relationName);
          // end else
         }

         // - name of the primary key of the source object
         $relationSection['SourceID'] = $relationSection['SourceObject'].'ID';

         // - name of the primary key of the target object
         $relationSection['TargetID'] = $relationSection['TargetObject'].'ID';

         // return section
         return $relationSection;

       // end function
      }

    // end class
   }
?>