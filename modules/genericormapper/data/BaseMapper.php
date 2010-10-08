<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('modules::genericormapper::data','GenericDomainObject');
   import('modules::genericormapper::data','GenericORMapperException');

   /**
    * @package modules::genericormapper::data
    * @class BaseMapper
    *
    * Implements the base class for all concrete or-mapper implementations.<br />
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    * Version 0.2, 14.05.2008<br />
    * Version 0.3, 26.10.2008 (Added the addMappingConfiguration() and addRelationConfiguration() methods)<br />
    * Version 0.4, 30.12.2008 (Prettified the benchmark ids)<br />
    */
   class BaseMapper extends APFObject {

      /**
       * @protected
       * @var string Namespace, where the configuration files are located.
       */
      protected $__ConfigNamespace = null;

      /**
       * @protected
       * @var string Name affix of the configuration files.
       */
      protected $__ConfigNameAffix = null;

      /**
       * @protected
       * @var AbstractDatabaseHandler Instance of the database driver.
       */
      protected $__DBDriver = null;

      /**
       * @since 1.12
       * Stores the connection name to be able to restore the connection on wakeup.
       * @var string The name of the connection to use.
       */
      protected $__DBConnectionName = null;

      /**
       * @protected
       * @var string[] Object mapping table.
       */
      protected $__MappingTable = array();

      /**
       * @protected
       * @since 1.12
       * @var string[] Additional indices for the object tables.
       */
      protected $__MappingIndexTable = array();

      /**
       * @protected
       * @var string[] Object relation table.
       */
      protected $__RelationTable = array();

      /**
       * @protected
       * @var string[] Indicates, if a additional configuration was already imported.
       */
      protected $__importedConfigCache = array();

      /**
       * @protected
       * @var boolean Indicates, whether the generated statements should be logged for debugging purposes.
       */
      protected $__LogStatements = false;

      /**
       * @protected
       * @var string Identifies the param that defines additional indices relevant for database setup.
       */
      protected static $ADDITIONAL_INDICES_INDICATOR = 'AddIndices';

      public function BaseMapper(){
      }

      /**
       * @public
       *
       * Implements the interface method init() to be able to initialize the mapper with the
       * service manager.
       *
       * @param string[] $initParam List of initialization parameters.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.04.2008<br />
       * Version 0.2, 14.05.2008 (mapping table creation moved to AbstractORMapper)<br />
       * Version 0.3, 31.05.2008 (changed behavior due to refactoring)<br />
       * Version 0.4, 22.06.2008 (refactored the configuration file entries to gain flexibility)<br />
       * Version 0.5, 23.06.2008 (mapper now must be instanciated by the factory, that configures the mapper)<br />
       * Version 0.6, 03.05.2009 (added the LogStatements param)<br />
       */
      public function init($initParam){

         // set the config namespace
         $this->__ConfigNamespace = $initParam['ConfigNamespace'];

         // set the config name affix
         $this->__ConfigNameAffix = $initParam['ConfigNameAffix'];

         // create the connection
         $this->__DBConnectionName = $initParam['ConnectionName'];
         $this->createDatabaseConnection();

         // set debug mode, if desired
         $this->__LogStatements = $initParam['LogStatements'];

       // end function
      }

      /**
       * @protected
       * @since 1.12
       *
       * Initializes the database connection. This is used on creation of the mapper
       * and on wakeup after session de-serialization.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.03.2010 (Introduced due to bug 299)<br />
       */
      protected function createDatabaseConnection(){
         $cM = &$this->__getServiceObject('core::database','ConnectionManager');
         $this->__DBDriver = &$cM->getConnection($this->__DBConnectionName);
      }

      /**
       * @public
       *
       * Returns the instance of the current database instance to be able to natively
       * execute statements against the database without extra configuration.
       *
       * @return AbstractDatabaseHandler The instance of the current database connection.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function &getDBDriver(){
         return $this->__DBDriver;
      }

      /**
       * @protected
       *
       * Parse the object configuration definition file.<br />
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.04.2008<br />
       * Version 0.2, 31.05.2008 (Refactoring of the object definition)<br />
       * Version 0.3, 22.06.2008 (Refactored object configuration adressing)<br />
       * Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateMappingItem() method)<br />
       */
      protected function __createMappingTable(){

         // invoke benchmark timer
         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('BaseMapper::__createMappingTable()');

         // get object configuration
         $objectsConfig = $this->getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_objects');

         // extract configuration to support pre 1.13 GORM config
         foreach($objectsConfig->getSectionNames() as $sectionName){
            $section = $objectsConfig->getSection($sectionName);
            $this->__MappingTable[$sectionName] = array();
            foreach($section->getValueNames() as $valueName){
               $this->__MappingTable[$sectionName][$valueName] = $section->getValue($valueName);
            }
         }

         // resolve definitions
         foreach($this->__MappingTable as $objectName => $DUMMY){
            
            // add additional index definition to separate table
            if(isset($this->__MappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR])){
               $this->__MappingIndexTable[$objectName] = $this->__MappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR];
               //unset($this->__MappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR]);
            }

            $this->__MappingTable[$objectName] = $this->__generateMappingItem($objectName,$this->__MappingTable[$objectName]);

          // end foreach
         }

         $t->stop('BaseMapper::__createMappingTable()');

       // end function
      }

      /**
       * @protected
       *
       * Create the object relation table.<br />
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 30.05.2008 (properties are now generated instead of configured explicitly)<br />
       * Version 0.3, 22.06.2008 (refactored relation configuration adressing)<br />
       * Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateRelationItem() method)<br />
       */
      protected function __createRelationTable(){

         // invoke benchmark timer
         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('BaseMapper::__createRelationTable()');

         // Get relation configuration
         $relationsConfig = $this->getConfiguration($this->__ConfigNamespace,$this->__ConfigNameAffix.'_relations');

         // extract configuration to support pre 1.13 GORM config
         foreach($relationsConfig->getSectionNames() as $sectionName){
            $section = $relationsConfig->getSection($sectionName);
            $this->__RelationTable[$sectionName] = array();
            foreach($section->getValueNames() as $valueName){
               $this->__RelationTable[$sectionName][$valueName] = $section->getValue($valueName);
            }
         }

         // resolve definitions
         foreach($this->__RelationTable as $relationName => $DUMMY){
            $this->__RelationTable[$relationName] = $this->__generateRelationItem($relationName,$this->__RelationTable[$relationName]);
          // end foreach
         }

         $t->stop('BaseMapper::__createRelationTable()');

       // end function
      }

      /**
       * @public
       *
       * Imports additional mapping information.
       *
       * @param string $configNamespace the desired configuration namespace
       * @param string $configNameAffix the configuration affix of the desired configuration
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.10.2008<br />
       */
      public function addMappingConfiguration($configNamespace,$configNameAffix){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('BaseMapper::addMappingConfiguration()');

         // add config, if not already included
         $cacheKey = md5($configNamespace.$configNameAffix.'_objects');
         if(!isset($this->__importedConfigCache[$cacheKey])){

            // import and merge config
            $addConfig = $this->getConfiguration($configNamespace,$configNameAffix.'_objects.ini');

            // extract configuration to support pre 1.13 GORM config
            $addObjects = array();
            foreach($addConfig->getSectionNames() as $sectionName){
               $section = $addConfig->getSection($sectionName);
               $addObjects[$sectionName] = array();
               foreach($section->getValueNames() as $valueName){
                  $addObjects[$sectionName][$valueName] = $section->getValue($valueName);
               }
            }

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

         $t->stop('BaseMapper::addMappingConfiguration()');

       // end function
      }

      /**
       * @public 
       * 
       * Allows you to initialize/enhance the generic or mapper's mapping configuration using
       * the DI service manager. See documentation of the
       * <em>GenericORMapperDIMappingConfiguration</em> class on configuration definition.
       *
       * @param GenericORMapperDIMappingConfiguration $config The additional mapping configuration.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.06.2010<br />
       */
      public function addDIMappingConfiguration(GenericORMapperDIMappingConfiguration $config){
         $this->addMappingConfiguration($config->getConfigNamespace(),$config->getConfigAffix());
      }

      /**
       * @public
       *
       * Imports additional relation information.
       *
       * @param string $configNamespace the desired configuration namespace
       * @param string $configNameAffix the configuration affix of the desired configuration
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.10.2008<br />
       */
      public function addRelationConfiguration($configNamespace,$configNameAffix){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $t->start('BaseMapper::addRelationConfiguration()');

         // add config, if not already included
         $cacheKey = md5($configNamespace.$configNameAffix.'_relations');
         if(!isset($this->__importedConfigCache[$cacheKey])){

            // import and merge config
            $addConfig = $this->getConfiguration($configNamespace,$configNameAffix.'_relations.ini');

            // extract configuration to support pre 1.13 GORM config
            $addRelations = array();
            foreach($addConfig->getSectionNames() as $sectionName){
               $section = $addConfig->getSection($sectionName);
               $addRelations[$sectionName] = array();
               foreach($section->getValueNames() as $valueName){
                  $addRelations[$sectionName][$valueName] = $section->getValue($valueName);
               }
            }

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

         $t->stop('BaseMapper::addRelationConfiguration()');

       // end function
      }

      /**
       * @public
       *
       * Allows you to initialize/enhance the generic or mapper's relation configuration using
       * the DI service manager. See documentation of the
       * <em>GenericORMapperDIMappingConfiguration</em> class on configuration definition.
       *
       * @param GenericORMapperDIRelationConfiguration $config The additional relation configuration.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.06.2010<br />
       */
      public function addDIRelationConfiguration(GenericORMapperDIRelationConfiguration $config){
         $this->addRelationConfiguration($config->getConfigNamespace(),$config->getConfigAffix());
      }

      /**
       * @protected
       *
       * Resolves the table and primary key name within the object definition configuration.
       *
       * @param string $objectName Name of the current configuration section (=name of the current object).
       * @param array $objectSection Current object definition params.
       * @return string[] Enhanced object definition.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.10.2008<br />
       */
      protected function __generateMappingItem($objectName,$objectSection){

         // resolve standard properties, that derive from the definition
         // - table name:
         $objectSection['Table'] = 'ent_'.strtolower($objectName);
         // - name of the primary key
         $objectSection['ID'] = $objectName.'ID';
         // remove the additional table definition
         unset($objectSection[self::$ADDITIONAL_INDICES_INDICATOR]);

         // return section
         return $objectSection;

       // end function
      }

      /**
       * @protected
       *
       * Resolves the table name, source and target id of the relation definition within the relation configuration.
       *
       * @param string $relationName nam of the current configuration section (=name of the current relation)
       * @param array $relationSection current relation definition params
       * @return string[] Enhanced relation definition
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.10.2008<br />
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