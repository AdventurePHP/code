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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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

      /**
      *  @private
      *  Indicates, if a additional configuration was already imported.
      */
      var $__importedConfigCache = array();


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
      *  Version 0.2, 31.05.2008 (Refactoring of the object definition)<br />
      *  Version 0.3, 22.06.2008 (Refactored object configuration adressing)<br />
      *  Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateMappingItem() method)<br />
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
         foreach($this->__MappingTable as $objectName => $DUMMY){
            $this->__MappingTable[$objectName] = $this->__generateMappingItem($objectName,$this->__MappingTable[$objectName]);
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
      *  Version 0.4, 26.10.2008 (Resolving functionality was outsourced to the __generateRelationItem() method)<br />
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
         foreach($this->__RelationTable as $relationName => $DUMMY){
            $this->__RelationTable[$relationName] = $this->__generateRelationItem($relationName,$this->__RelationTable[$relationName]);
          // end foreach
         }

         // Stop timer
         $T->stop('__createRelationTable()');

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
         $T->start('addMappingConfiguration()');

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
         $T->stop('addMappingConfiguration()');

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
         $T->start('addRelationConfiguration()');

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
         $T->stop('addRelationConfiguration()');

       // end function
      }


      /**
      *  @private
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
      function __generateMappingItem($objectName,$objectSection){

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
      *  @private
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
      function __generateRelationItem($relationName,$relationSection){

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