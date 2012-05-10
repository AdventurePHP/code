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
import('modules::genericormapper::data', 'GenericDomainObject');
import('modules::genericormapper::data', 'GenericORMapperException');

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
 * Version 0.5, 15.01.2011 (Added support for domain objects)<br />
 */
class BaseMapper extends APFObject {

   /**
    * @protected
    * @var string Namespace, where the configuration files are located.
    */
   protected $configNamespace = null;

   /**
    * @protected
    * @var string Name affix of the configuration files.
    */
   protected $configNameAffix = null;

   /**
    * @protected
    * @var AbstractDatabaseHandler Instance of the database driver.
    */
   protected $dbDriver = null;

   /**
    * @since 1.12
    * Stores the connection name to be able to restore the connection on wakeup.
    * @var string The name of the connection to use.
    */
   protected $connectionName = null;

   /**
    * @protected
    * @var string[] Object mapping table.
    */
   protected $mappingTable = array();

   /**
    * @protected
    * @since 1.12
    * @var string[] Additional indices for the object tables.
    */
   protected $mappingIndexTable = array();

   /**
    * @protected
    * @var array Object relation table.
    */
   protected $relationTable = array();

   /**
    * @protected
    * @since 1.14
    * @var string[] Domain object table
    */
   protected $domainObjectsTable = array();

   /**
    * @protected
    * @var string[] Indicates, if a additional configuration was already imported.
    */
   protected $importedConfigCache = array();

   /**
    * @protected
    * @var boolean Indicates, whether the generated statements should be logged for debugging purposes.
    */
   protected $logStatements = false;

   /**
    * @since 1.14
    * @var string Defines the config file extension the GORM instance uses.
    */
   private $configFileExtension = 'ini';

   /**
    * @protected
    * @var string Identifies the param that defines additional indices relevant for database setup.
    */
   protected static $ADDITIONAL_INDICES_INDICATOR = 'AddIndices';

   public function getConfigNamespace() {
      return $this->configNamespace;
   }

   public function setConfigNamespace($configNamespace) {
      $this->configNamespace = $configNamespace;
   }

   public function getConfigNameAffix() {
      return $this->configNameAffix;
   }

   /**
    * @public
    *
    * Injects the config name affix and sets up the interal mapping structures.
    * Please note, that this method must be called after <em>setConfigNamespace()</em>!
    *
    * @param string $configNameAffix The name of the configuration affix that is used within the configuration file names.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2011<br />
    */
   public function setConfigNameAffix($configNameAffix) {
      $this->configNameAffix = $configNameAffix;
   }

   public function getConnectionName() {
      return $this->connectionName;
   }

   public function setConnectionName($connectionName) {
      $this->connectionName = $connectionName;
   }

   public function getLogStatements() {
      return $this->logStatements;
   }

   public function setLogStatements($logStatements) {
      if (is_string($logStatements) === true) {
         $logStatements = ((strtolower($logStatements) == 'true') ? true : false);
      }

      $this->logStatements = $logStatements;
   }

   public function getConfigFileExtension() {
      return $this->configFileExtension;
   }

   /**
    * @public
    *
    * Injects the desired config file extension to use with the APF's configuration
    * provider concept. Must be called before <em>setConfigNamespace()</em> and
    * <em>setConfigNameAffix()</em>!
    *
    * @param string $configFileExtension The desired file extension.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2011<br />
    */
   public function setConfigFileExtension($configFileExtension) {
      $this->configFileExtension = $configFileExtension;
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
   protected function createDatabaseConnection() {
      $cM = &$this->getServiceObject('core::database', 'ConnectionManager');
      /* @var $cM ConnectionManager */
      $this->dbDriver = &$cM->getConnection($this->connectionName);
   }

   /**
    * @public
    *
    * DI initialization method to setup and re-initialize (on session restore!) the password hash providers.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function setup() {

      $this->createDatabaseConnection();

      // create mapping table if necessary to gain performance
      if (count($this->mappingTable) === 0) {
         $this->createMappingTable();
      }

      // create relation table if necessary to gain performance
      if (count($this->relationTable) === 0) {
         $this->createRelationTable();
      }

      // create service object table if necessary to gain performance
      if (count($this->domainObjectsTable) === 0) {
         $this->createDomainObjectsTable();
      }

      // do not initialize the lookup tables more than once per session.
      $this->markAsInitialized();

   }

   /**
    * @public
    *
    * Returns the instance of the current database instance to be able to native
    * execute statements against the database without extra configuration.
    *
    * @return AbstractDatabaseHandler The instance of the current database connection.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.02.2010<br />
    */
   public function &getDbDriver() {
      return $this->dbDriver;
   }

   /**
    * @public
    *
    * This method can be used to inject the database connection via the
    * DIServiceManager.
    *
    * @param DatabaseConnection $dbDriver The database driver to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.05.2012<br />
    */
   public function setDbDriver(DatabaseConnection $dbDriver) {
      $this->dbDriver = $dbDriver;
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
   protected function createMappingTable() {

      // invoke benchmark timer
      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::createMappingTable()');

      // get object configuration
      $objectsConfig = $this->getConfiguration($this->configNamespace, $this->configNameAffix . '_objects.' . $this->getConfigFileExtension());

      // extract configuration to support pre 1.13 GORM config
      foreach ($objectsConfig->getSectionNames() as $sectionName) {
         $section = $objectsConfig->getSection($sectionName);
         $this->mappingTable[$sectionName] = array();
         foreach ($section->getValueNames() as $valueName) {
            $this->mappingTable[$sectionName][$valueName] = $section->getValue($valueName);
         }
      }

      // resolve definitions
      foreach ($this->mappingTable as $objectName => $DUMMY) {

         // add additional index definition to separate table
         if (isset($this->mappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR])) {
            $this->mappingIndexTable[$objectName] = $this->mappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR];
            //unset($this->mappingTable[$objectName][self::$ADDITIONAL_INDICES_INDICATOR]);
         }

         $this->mappingTable[$objectName] = $this->generateMappingItem($objectName, $this->mappingTable[$objectName]);
      }

      $t->stop('BaseMapper::createMappingTable()');
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
   protected function createRelationTable() {

      // invoke benchmark timer
      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::createRelationTable()');

      // Get relation configuration
      $relationsConfig = $this->getConfiguration($this->configNamespace, $this->configNameAffix . '_relations.' . $this->getConfigFileExtension());

      // extract configuration to support pre 1.13 GORM config
      foreach ($relationsConfig->getSectionNames() as $sectionName) {
         $section = $relationsConfig->getSection($sectionName);
         $this->relationTable[$sectionName] = array();
         foreach ($section->getValueNames() as $valueName) {
            $this->relationTable[$sectionName][$valueName] = $section->getValue($valueName);
         }
      }

      // resolve definitions
      foreach ($this->relationTable as $relationName => $DUMMY) {
         $this->relationTable[$relationName] = $this->generateRelationItem($relationName, $this->relationTable[$relationName]);
      }

      $t->stop('BaseMapper::createRelationTable()');
   }

   /**
    * @protected
    *
    * Create the service object table.<br />
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   protected function createDomainObjectsTable() {

      // invoke benchmark timer
      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::createServiceObjectsTable()');

      // get object configuration if there is one
      try {
         $serviceObjectsConfig = $this->getConfiguration($this->configNamespace, $this->configNameAffix . '_domainobjects.' . $this->getConfigFileExtension());
         foreach ($serviceObjectsConfig->getSectionNames() as $sectionName) {
            $section = $serviceObjectsConfig->getSection($sectionName);
            $this->domainObjectsTable[$sectionName] = array();
            foreach ($section->getValueNames() as $valueName) {
               $this->domainObjectsTable[$sectionName][$valueName] = $section->getValue($valueName);
            }
            if ($section->getSection('Base') !== null) {
               $this->domainObjectsTable[$sectionName]['Base'] = array(
                  'Namespace' => $section->getSection('Base')->getValue('Namespace'),
                  'Class' => $section->getSection('Base')->getValue('Class'),
               );
            }
         }
      } catch (ConfigurationException $e) {
         // do nothing, since not all applications are using domain objects!
      }

      $t->stop('BaseMapper::createServiceObjectsTable()');
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
   public function addMappingConfiguration($configNamespace, $configNameAffix) {

      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::addMappingConfiguration()');

      // add config, if not already included
      $cacheKey = md5($configNamespace . $configNameAffix . '_objects');
      if (!isset($this->importedConfigCache[$cacheKey])) {

         // import and merge config
         $addConfig = $this->getConfiguration($configNamespace, $configNameAffix . '_objects.' . $this->getConfigFileExtension());

         // extract configuration to support pre 1.13 GORM config
         $addObjects = array();
         foreach ($addConfig->getSectionNames() as $sectionName) {
            $section = $addConfig->getSection($sectionName);
            $addObjects[$sectionName] = array();
            foreach ($section->getValueNames() as $valueName) {
               $addObjects[$sectionName][$valueName] = $section->getValue($valueName);
            }
         }

         foreach ($addObjects as $objectName => $DUMMY) {

            if (!isset($this->mappingTable[$objectName])) {
               $this->mappingTable[$objectName] = $this->generateMappingItem($objectName, $addObjects[$objectName]);
            }
         }

         // mark object config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addMappingConfiguration()');
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
   public function addDIMappingConfiguration(GenericORMapperDIMappingConfiguration $config) {
      $this->addMappingConfiguration($config->getConfigNamespace(), $config->getConfigAffix());
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
   public function addRelationConfiguration($configNamespace, $configNameAffix) {

      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::addRelationConfiguration()');

      // add config, if not already included
      $cacheKey = md5($configNamespace . $configNameAffix . '_relations');
      if (!isset($this->importedConfigCache[$cacheKey])) {

         // import and merge config
         $addConfig = $this->getConfiguration($configNamespace, $configNameAffix . '_relations.' . $this->getConfigFileExtension());

         // extract configuration to support pre 1.13 GORM config
         $addRelations = array();
         foreach ($addConfig->getSectionNames() as $sectionName) {
            $section = $addConfig->getSection($sectionName);
            $addRelations[$sectionName] = array();
            foreach ($section->getValueNames() as $valueName) {
               $addRelations[$sectionName][$valueName] = $section->getValue($valueName);
            }
         }

         foreach ($addRelations as $relationName => $DUMMY) {

            if (!isset($this->relationTable[$relationName])) {
               $this->relationTable[$relationName] = $this->generateRelationItem($relationName, $addRelations[$relationName]);
            }
         }

         // mark relation config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addRelationConfiguration()');
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
   public function addDIRelationConfiguration(GenericORMapperDIRelationConfiguration $config) {
      $this->addRelationConfiguration($config->getConfigNamespace(), $config->getConfigAffix());
   }

   /**
    * @public
    *
    * Imports additional domain object mapping information.
    *
    * @param string $configNamespace the desired configuration namespace
    * @param string $configNameAffix the configuration affix of the desired configuration
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function addDomainObjectsConfiguration($configNamespace, $configNameAffix) {

      $t = &Singleton::getInstance('BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::addServiceObjectsConfiguration()');

      // add config, if not already included
      $cacheKey = md5($configNamespace . $configNameAffix . '_domainobjects');
      if (!isset($this->importedConfigCache[$cacheKey])) {

         // import and merge config
         $addConfig = $this->getConfiguration($configNamespace, $configNameAffix . '_domainobjects.' . $this->getConfigFileExtension());

         // extract configuration to support pre 1.13 GORM config
         $addObjects = array();
         foreach ($addConfig->getSectionNames() as $sectionName) {
            $section = $addConfig->getSection($sectionName);
            $addObjects[$sectionName] = array();
            foreach ($section->getValueNames() as $valueName) {
               $addObjects[$sectionName][$valueName] = $section->getValue($valueName);
            }
            if ($section->getSection('Base') !== null) {
               $addObjects[$sectionName]['Base'] = array(
                  'Namespace' => $section->getSection('Base')->getValue('Namespace'),
                  'Class' => $section->getSection('Base')->getValue('Class'),
               );
            }
         }

         foreach ($addObjects as $objectName => $DUMMY) {
            if (!isset($this->domainObjectsTable[$objectName])) {
               $this->domainObjectsTable[$objectName] = $DUMMY;
            }
         }

         // mark object config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addServiceObjectsConfiguration()');
   }

   /**
    * @public
    *
    * Allows you to initialize/enhance the generic or mapper's service object configuration using
    * the DI service manager. See documentation of the
    * <em>GenericORMapperDIDomainObjectsConfiguration</em> class on configuration definition.
    *
    * @param GenericORMapperDIDomainObjectsConfiguration $config The additional service objects configuration.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function addDIDomainObjectsConfiguration(GenericORMapperDIDomainObjectsConfiguration $config) {
      $this->addDomainObjectsConfiguration($config->getConfigNamespace(), $config->getConfigAffix());
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
   protected function generateMappingItem($objectName, $objectSection) {

      // resolve standard properties, that derive from the definition
      // - table name:
      $objectSection['Table'] = 'ent_' . strtolower($objectName);
      // - name of the primary key
      $objectSection['ID'] = $objectName . 'ID';
      // remove the additional table definition
      unset($objectSection[self::$ADDITIONAL_INDICES_INDICATOR]);

      return $objectSection;
   }

   /**
    * @protected
    *
    * Resolves the table name, source and target id of the relation definition within
    * the relation configuration.
    *
    * @param string $relationName nam of the current configuration section (=name of the current relation).
    * @param array $relationSection current relation definition params.
    * @return string[] Enhanced relation definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.10.2008<br />
    * Version 0.2, 16.03.2011 (Re-introduced Tobi's changes to allow self referencing relations)<br />
    */
   protected function generateRelationItem($relationName, $relationSection) {

      // Resolve standard properties, that derive from the definition
      // - table name
      if ($relationSection['Type'] == 'COMPOSITION') {
         $relationSection['Table'] = 'cmp_' . strtolower($relationName);
      } else {
         $relationSection['Table'] = 'ass_' . strtolower($relationName);
      }

      // - name of the primary key of the source object
      $relationSection['SourceID'] = 'Source_' . $relationSection['SourceObject'] . 'ID';

      // - name of the primary key of the target object
      $relationSection['TargetID'] = 'Target_' . $relationSection['TargetObject'] . 'ID';

      return $relationSection;
   }

}

?>