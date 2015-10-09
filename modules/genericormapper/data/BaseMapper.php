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
namespace APF\modules\genericormapper\data;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\configuration\ConfigurationException;
use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\core\database\DatabaseConnection;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;

/**
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
    * Identifies the param that defines additional indices relevant for database setup.
    *
    * @var string $ADDITIONAL_INDICES_INDICATOR
    */
   protected static $ADDITIONAL_INDICES_INDICATOR = 'AddIndices';
   protected static $STORAGE_ENGINE_INDICATOR = 'StorageEngine';

   /**
    * Namespace, where the configuration files are located.
    *
    * @var string $configNamespace
    */
   protected $configNamespace = null;

   /**
    * Name affix of the configuration files.
    *
    * @var string $configNameAffix
    */
   protected $configNameAffix = null;

   /**
    * Instance of the database driver.
    *
    * @var AbstractDatabaseHandler $dbDriver
    */
   protected $dbDriver = null;

   /**
    * Stores the connection name to be able to restore the connection on wakeup.
    *
    * @var string $connectionName
    *
    * @since 1.12
    */
   protected $connectionName = null;

   /**
    * Object mapping table.
    *
    * @var string[] $mappingTable
    */
   protected $mappingTable = array();

   /**
    * Additional indices for the object tables.
    *
    * @var string[] $mappingIndexTable
    *
    * @since 1.12
    */
   protected $mappingIndexTable = array();

   /**
    * Storage Engine for the object tables.
    *
    * @var string[] $mappingStorageEngineTable
    *
    * @since 2.1
    */
   protected $mappingStorageEngineTable = array();

   /**
    * Storage Engine for the relation tables.
    *
    * @var string[] $relationStorageEngineTable
    *
    * @since 2.1
    */
   protected $relationStorageEngineTable = array();

   /**
    * Object relation table.
    *
    * @var array $relationTable
    */
   protected $relationTable = array();

   /**
    * Domain object table
    *
    * @var string[] $domainObjectsTable
    *
    * @since 1.14
    */
   protected $domainObjectsTable = array();

   /**
    * Indicates, if a additional configuration was already imported.
    *
    * @var string[] $importedConfigCache
    */
   protected $importedConfigCache = array();

   /**
    * Indicates, whether the generated statements should be logged for debugging purposes.
    *
    * @var boolean $logStatements
    */
   protected $logStatements = false;

   /**
    * Defines the config file extension the GORM instance uses.
    *
    * @var string $configFileExtension
    *
    * @since 1.14
    */
   private $configFileExtension = 'ini';

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
         $logStatements = strtolower($logStatements) == 'true' ? true : false;
      }

      $this->logStatements = $logStatements;
   }

   /**
    * DI initialization method to setup and re-initialize (on session restore!) the password hash providers.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function setup() {

      // ID#102: Only create database connection in case no connection name has been specified or
      // driver instance has been injected. This allows usage of DIServiceManager and
      // classic usage to create database connections via the ConnectionManager.
      if (!empty($this->connectionName)) {
         $this->createDatabaseConnection();
      }

      // Only create mapping/relation/objects table when mapping has been
      // configured directly via
      //
      // conf.namespace.method = "setConfigNamespace"
      // conf.namespace.value = "..."
      // conf.affix.method = "setConfigNameAffix"
      // conf.affix.value = "..."
      //
      // or mixed setup (DI service + basic config). Otherwise - this is
      // when configuring mappings with DI services -, there is no need
      // to do so. Regarding caching/performance, the import cache already
      // ensures, that the mapping/relation/objects table is only created
      // once per config!
      if (!empty($this->configNamespace) && !empty($this->configNameAffix)) {
         $this->addMappingConfiguration($this->configNamespace, $this->configNameAffix);
         $this->addRelationConfiguration($this->configNamespace, $this->configNameAffix);
         $this->addDomainObjectsConfiguration($this->configNamespace, $this->configNameAffix);
      }

      // Do not initialize the lookup tables more than once per session (if created SESSIONSINGLETON).
      // Otherwise, once per request (SINGLETON).
      $this->markAsInitialized();
   }

   /**
    * Initializes the database connection. This is used on creation of the mapper
    * and on wakeup after session de-serialization.
    *
    * @since 1.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.03.2010 (Introduced due to bug 299)<br />
    */
   protected function createDatabaseConnection() {
      $cM = &$this->getServiceObject(ConnectionManager::class);
      /* @var $cM ConnectionManager */
      $this->dbDriver = &$cM->getConnection($this->connectionName);
   }

   /**
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

      $t = &Singleton::getInstance(BenchmarkTimer::class);
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

            // Add additional index definition to separate table. We do this before generating the mapping definition,
            // because generateMappingItem() removes the additional index definition to keep the internal mapping table
            // clean.
            if (isset($addObjects[$objectName][self::$ADDITIONAL_INDICES_INDICATOR])) {
               $this->mappingIndexTable[$objectName] = $addObjects[$objectName][self::$ADDITIONAL_INDICES_INDICATOR];
            }
            // Add Storage Engine definition to separate table. We do this before generating the mapping definition,
            // because generateMappingItem() removes the additional index definition to keep the internal mapping table
            // clean.
            if (isset($addObjects[$objectName][self::$STORAGE_ENGINE_INDICATOR])) {
               $this->mappingStorageEngineTable[$objectName] = $addObjects[$objectName][self::$STORAGE_ENGINE_INDICATOR];
            }

            // Only create new items to avoid overwriting items with same name from different configuration files
            // (since we do not have namespaces for domain objects).
            if (!isset($this->mappingTable[$objectName])) {
               $this->mappingTable[$objectName] = $this->generateMappingItem($objectName, $addObjects[$objectName]);
            }

         }

         // mark object config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addMappingConfiguration()');
   }

   public function getConfigFileExtension() {
      return $this->configFileExtension;
   }

   /**
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
    * Resolves the table and primary key name within the object definition configuration.
    *
    * @param string $objectName Name of the current configuration section (=name of the current object).
    * @param array $objectSection Current object definition params.
    *
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
      // remove the storage Engine definition
      unset($objectSection[self::$STORAGE_ENGINE_INDICATOR]);

      return $objectSection;
   }

   /**
    * Imports additional relation information.
    *
    * @param string $configNamespace The desired configuration namespace.
    * @param string $configNameAffix The configuration affix of the desired configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.10.2008<br />
    */
   public function addRelationConfiguration($configNamespace, $configNameAffix) {

      $t = &Singleton::getInstance(BenchmarkTimer::class);
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
            // Only create new items to avoid overwriting items with same name from different configuration files
            // (since we do not have namespaces for relations).
            if (!isset($this->relationTable[$relationName])) {
               $this->relationTable[$relationName] = $this->generateRelationItem($relationName, $addRelations[$relationName]);
            }

            if (isset($addObjects[$relationName][self::$STORAGE_ENGINE_INDICATOR])) {
               $this->relationStorageEngineTable[$relationName] = $addObjects[$relationName][self::$STORAGE_ENGINE_INDICATOR];
            }
         }

         // mark relation config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addRelationConfiguration()');
   }

   /**
    * Resolves the table name, source and target id of the relation definition within
    * the relation configuration.
    *
    * @param string $relationName nam of the current configuration section (=name of the current relation).
    * @param array $relationSection current relation definition params.
    *
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

      // remove the Storage Engine definition
      unset($relationSection[self::$STORAGE_ENGINE_INDICATOR]);

      return $relationSection;
   }

   /**
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

      $t = &Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $t->start('BaseMapper::addDomainObjectsConfiguration()');

      // add config, if not already included
      $cacheKey = md5($configNamespace . $configNameAffix . '_domainobjects');
      if (!isset($this->importedConfigCache[$cacheKey])) {
         try {
            // import and merge config
            $addConfig = $this->getConfiguration($configNamespace, $configNameAffix . '_domainobjects.' . $this->getConfigFileExtension());

            // extract configuration to support pre 1.13 GORM config
            foreach ($addConfig->getSectionNames() as $sectionName) {
               $section = $addConfig->getSection($sectionName);
               $this->domainObjectsTable[$sectionName] = array();
               foreach ($section->getValueNames() as $valueName) {
                  $this->domainObjectsTable[$sectionName][$valueName] = $section->getValue($valueName);
               }
               if ($section->hasSection('Base')) {
                  $this->domainObjectsTable[$sectionName]['Base'] = array(
                        'Class' => $section->getSection('Base')->getValue('Class'),
                  );
               }
            }
         } catch (ConfigurationException $e) {
            // do nothing, because we accept that (domain objects are optional)!
         }

         // mark object config as cached
         $this->importedConfigCache[$cacheKey] = true;
      }

      $t->stop('BaseMapper::addDomainObjectsConfiguration()');
   }

   /**
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
    * Checks, whether or not an object has been registered/defined w/ the GORM.
    *
    * @param string $objectName The name of the object to check.
    *
    * @return bool <em>True</em> in case object is defined, <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.05.2014 (ID#141: added check method for objects)<br />
    */
   public function isObjectDefined($objectName) {
      return isset($this->mappingTable[$objectName]);
   }

   /**
    * Checks, whether or not an object property has been registered/defined w/ the GORM.
    *
    * @param string $objectName The name of the object to check.
    * @param string $propertyName The name of the property to check.
    *
    * @return bool <em>True</em> in case object property is defined, <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.05.2014 (ID#141: added check method for properties)<br />
    */
   public function isObjectPropertyDefined($objectName, $propertyName) {
      return isset($this->mappingTable[$objectName][$propertyName]);
   }

}
