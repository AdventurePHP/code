<?php
namespace APF\modules\genericormapper\data\tools;

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
use APF\modules\genericormapper\data\BaseMapper;
use APF\modules\genericormapper\data\GenericORMapperException;

/**
 * @package APF\modules\genericormapper\data
 * @class GenericORMapperManagementTool
 *
 * This tool allows you to setup a database for use with the generic or mapper. It enables
 * you to generate the table layout from a given couple of configuration files (objects and
 * relations). In order to adapt the automatic
 *
 * In order to adapt the automatically generated change-set, please ensure the last param
 * to be <em>false</em>. This results in displaying the change statements rather to execute
 * them against the given database.
 * <p/>
 * Changes to the database layout can be applied using the <strong>GenericORMapperUpdate</strong>
 * utility. Please refer to the documentation of this tool for update details!
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.05.2008<br />
 */
class GenericORMapperManagementTool extends BaseMapper {

   /**
    * @private
    * @var string[] Indicators for the data type mapping.
    */
   private $rowTypeMappingFrom = array(
      '/^VARCHAR\(([0-9]+)\)$/i',
      '/^TEXT$/i',
      '/^DATE$/i'
   );

   /**
    * @private
    * @var string[] Replace strings for the data type mapping.
    */
   private $rowTypeMappingTo = array(
      'VARCHAR($1) character set [charset] NOT NULL default \'\'',
      'TEXT character set [charset] NOT NULL',
      'DATE NOT NULL default \'0000-00-00\''
   );

   /**
    * @private
    * @var string Stores the MySQL storage engine type.
    */
   private $storageEngine = 'MyISAM';

   /**
    * @private
    * @var string The data type that is used for the indexed id columns.
    */
   private $indexColumnDataType = 'INT(5) UNSIGNED';

   /**
    * @private
    * @var string The character set of the tables to create.
    */
   private $tableCharset = 'utf8';

   /**
    * @var string[] Mapping table reconstructed from the given database connection.
    */
   private $reEngineeredMappingTable = array();
   private $databaseMappingTables = array();

   /**
    * @var string[] Relation table reconstructed from the given database connection.
    */
   private $reEngineeredRelationTable = array();
   private $databaseRelationTables = array();

   /**
    * @var string[] Stores the new mapping entries.
    */
   private $newMappings = array();

   /**
    * @var string[] Stores the removed mapping entries.
    */
   private $removedMappings = array();

   /**
    * @var string[] Stores the attributes of mapping entries, that have been added.
    */
   private $newMappingAttributes = array();

   /**
    * @var string[] Stores the attributes of mapping entries, that have been removed.
    */
   private $removedMappingAttributes = array();

   /**
    * @var string[] Stores the attributes of mapping entries, that have been altered.
    */
   private $alteredMappingAttributes = array();

   /**
    * @var string[] Stores the new relation entries.
    */
   private $newRelations = array();

   /**
    * @var string[] Stores the removed relation entries.
    */
   private $removedRelations = array();

   /**
    * @var string[] Stores the attributes of relation entries, that have been altered.
    */
   private $alteredRelationAttributes = array();

   /**
    * @var string[] Stores the changes index column fields for mapping tables.
    */
   private $alteredIndexDataColumnTypeObjectFields = array();

   /**
    * @var string[] Stores the changes index column fields for relation tables.
    */
   private $alteredIndexDataColumnTypeRelationFields = array();

   /**
    * @var string[] Stores the update statements.
    */
   private $updateStatements = array();

   /**
    * @public
    *
    * Let's you influence the storage engine that is used to create the tables with.
    * <p/>
    * Please note, that changes in storage engine changes on database layout updates are
    * not supported as of now!
    *
    * @param string $engine The name of the storage engine.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2010<br />
    */
   public function setStorageEngine($engine) {
      $this->storageEngine = $engine;
   }

   public function getStorageEngine() {
      return $this->storageEngine;
   }

   /**
    * @public
    *
    * Let's you influence the data type of the indexed id columns to have
    * a greater range of objects to store within the database.
    * <p/>
    * Please be aware, that the value set with this method is directly used
    * within the create and update statements.
    *
    * @see http://dev.mysql.com/doc/refman/5.0/en/data-types.html
    *
    * @param string $dataType The column data type for indexed id columns.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2010<br />
    */
   public function setIndexColumnDataType($dataType) {
      $this->indexColumnDataType = $dataType;
   }

   public function getIndexColumnDataType() {
      return $this->indexColumnDataType;
   }

   /**
    * @public
    * @since 1.12
    *
    * Let's you influence the character sets, the tables are created with. By default,
    * utf8 is used to have good compatibility with most of the application cases. If
    * you want to change it for certain reasons, use this method conjunction with an
    * appropriate MySQL character set.
    *
    * @see http://dev.mysql.com/doc/refman/5.0/en/data-types.html
    *
    * @param string $tableCharset The desired charset (e.g. utf8).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.04.2010<br />
    */
   public function setTableCharset($tableCharset) {
      $this->tableCharset = $tableCharset;
   }

   public function getTableCharset() {
      return $this->tableCharset;
   }

   /**
    * @public
    *
    * Creates or updates a database, that is used with the GenericORRelationMapper. You can
    * choose between direct update (<em>$updateInPlace=true</em>) and displaying the update
    * statements for manual update (<em>$updateInPlace=false</em>). Default is direct update.
    *
    * Requires the following parameters to be set by the appropriate setter methods:
    * <ul>
    * <li><em>$configNamespace</em>: namespace, where the desired mapper configuration is located</li>
    * <li><em>$configNameAffix</em>: name affix of the object and relation definition files</li>
    * <li><em>$connectionName</em>: name of the connection, that the mapper should use to access the database</li>
    * </ul>
    * This can be done by the following code snippet:
    * <code>
    * $gormTool = new GenericORMapperManagementTool();
    * $gormTool->setConnectionName('foo');
    *                                     // config namespace  // config affix
    * $gormTool->addMappingConfiguration('VENDOR\blah\blah',   'foo');
    *
    *                                      // config namespace  // config affix
    * $gormTool->addRelationConfiguration('VENDOR\blah\blah',   'foo');
    * ...
    * $gormTool->run(true); // true=update/create database directly, false=print statements for manual creation/update
    * </code>
    *
    * @param boolean $updateInPlace Defines, if the update should be done for you (true) or if
    *                               the update statement should only be displayed (false).
    *                               Default is true.
    * @throws GenericORMapperException In case of missing connection name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2009<br />
    */
   public function run($updateInPlace = true) {

      // ID#104: clean up volatile data to allow multiple runs with deterministic results
      $this->reEngineeredMappingTable = array();
      $this->databaseMappingTables = array();
      $this->reEngineeredRelationTable = array();
      $this->databaseRelationTables = array();
      $this->newMappings = array();
      $this->removedMappings = array();
      $this->newMappingAttributes = array();
      $this->removedMappingAttributes = array();
      $this->alteredMappingAttributes = array();
      $this->newRelations = array();
      $this->removedRelations = array();
      $this->alteredRelationAttributes = array();
      $this->updateStatements = array();
      $this->alteredIndexDataColumnTypeObjectFields = array();
      $this->alteredIndexDataColumnTypeRelationFields = array();

      // Add mapping and relation configuration if passed along with the call.
      // To support setup with multiple configurations, please add each of the
      // configuration files prior to call this method. E.g.:
      // $setup = new GenericORMapperManagementTool();
      // $setup->setContext('blah');
      // $setup->addMappingConfiguration('VENDOR\path\to\my\application', 'foo');
      // $setup->addMappingConfiguration('VENDOR\path\to\my\application', 'bar');
      // $setup->run();
      if (!empty($this->configNamespace) && !empty($this->configNameAffix)) {
         $this->addMappingConfiguration($this->configNamespace, $this->configNameAffix);
         $this->addRelationConfiguration($this->configNamespace, $this->configNameAffix);
      }

      // ID#102: Only create database connection in case no connection name has been specified or
      // driver instance has been injected. This allows usage of DIServiceManager and
      // classic usage to create database connections via the ConnectionManager.
      if (!empty($this->connectionName)) {
         $this->createDatabaseConnection();
      }

      // analyze the current database
      $this->analyzeDatabaseTables();

      // re-engineer the database tables concerning the relations
      $this->reEngineerRelations();

      // re-engineer the database tables concerning the objects
      $this->reEngineerMappings();

      // analyze the old and new mapping configuration
      $this->analyzeMappingConfigurationChanges();

      // generate mapping update statements
      $this->generateMappingUpdateStatements();

      // analyze old and new relation configuration
      $this->analyzeRelationConfigurationChanges();

      // generate relation update statements
      $this->generateRelationUpdateStatements();

      // analyze existing index column data type
      $this->analyzeIndexColumnDataTypeChanges();

      // generate index data type update statements
      $this->generateIndexColumnDataTypeStatements();

      // analyze potential changes in storage engines
      // NOTE: this option is commented out by now, since the mechanism
      // implemented by 1.14-alpha is not sufficient for different storage
      // engines by table. Within further check-ins we will provide a
      // mechanism to specify the storage engine per table. Then this
      // functionality will be re-enabled!
      //$this->generateStorageEngineUpdate($sql);

      // print alter statements or execute them immediately in case we have a connection name and
      // we have been told to update the database directly
      if ($updateInPlace === true) {
         foreach ($this->updateStatements as $statement) {
            $this->dbDriver->executeTextStatement($statement);
         }
      } else {
         echo '<pre>';
         foreach ($this->updateStatements as $statement) {
            echo $statement . PHP_EOL . PHP_EOL . PHP_EOL;
         }
         echo '</pre>';
      }
   }

   /**
    * Compares two mapping keys. In case of mappings case sensitive comparison is done.
    *
    * @param string $a The first key.
    * @param string $b The second key.
    * @return int Compare status (0=equal, 1=different).
    */
   private function compareMappings($a, $b) {
      if ($a === $b) {
         return 0;
      }
      return 1;
   }

   private function compareMappingValues($a, $b) {
      return $this->compareMappings($a, $b);
   }

   /**
    * Compares two relation keys. In case of relations case insensitive comparison is done.
    *
    * @param string $a The first key.
    * @param string $b The second key.
    * @return int Compare status (0=equal, 1=different).
    */
   /** @noinspection PhpUnusedPrivateMethodInspection Internally used for sort()'ing */
   private function compareRelations($a, $b) {
      $a = strtolower($a);
      $b = strtolower($b);
      if ($a === $b) {
         return 0;
      }
      return 1;
   }

   /**
    * Returns the type of
    *
    * @param string $tableName The
    * @return string The relation type declaration.
    */
   private function getRelationTypeLabel($tableName) {
      $tableName = strtolower($tableName);
      if (substr_count($tableName, 'ass_') > 0) {
         return 'ASSOCIATION';
      }
      return 'COMPOSITION';
   }

   /**
    * @private
    *
    * Returns the fields, that are relevant for comparison.
    *
    * @param string[] $fields
    * @return string[] The fields relevant for comparison.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.10.2009<br />
    * Version 0.2, 13.10.2009 (Corrected check for primary key)<br />
    */
   private function getRelevantFields($fields) {
      $resultFields = array();

      foreach ($fields as $field) {
         if ($field['Key'] != 'PRI' // do exclude primary key, but allow MUL indices!
               && $field['Field'] != 'CreationTimestamp'
               && $field['Field'] != 'ModificationTimestamp'
         ) {
            $resultFields[] = $field;
         }
      }

      return $resultFields;
   }

   /**
    * @private
    *
    * Returns the name of the primary key.
    *
    * @param string[] $fields The current definition's fields.
    * @return string The name of the primary key.
    */
   private function getPrimaryKeyName($fields) {
      foreach ($fields as $field) {
         if ($field['Key'] == 'PRI') {
            return $field['Field'];
         }
      }
      return null;
   }

   /**
    * @private
    *
    * Analyzes the given database and stores the tables included.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.10.2009<br />
    * Version 0.2, 21.11.2010 (Now ignoring tables that are not created by the GORM)<br />
    */
   private function analyzeDatabaseTables() {

      $selectTables = 'SHOW TABLES;';
      $resultTables = $this->dbDriver->executeTextStatement($selectTables);

      while ($dataTables = $this->dbDriver->fetchData($resultTables)) {

         // gather the offset we are provided by the database due
         // to the fact, that we ordered an associative array!
         $keys = array_keys($dataTables);
         $offset = $keys[0];

         // collect tables
         $tablePrefix = substr($dataTables[$offset], 0, 4);
         switch ($tablePrefix) {
            case 'ent_':
               $this->databaseMappingTables[] = $dataTables[$offset];
               break;
            case 'ass_':
            case 'cmp_':
               $this->databaseRelationTables[] = $dataTables[$offset];
               break;
         }
      }
   }

   /**
    * @private
    *
    * Creates a relation mapping out of the database tables.
    *
    * @author Christian Achatz
    * @version
    * Version 0.2, 07.03.2011 (Added support for relations between the same table)<br />
    */
   private function reEngineerRelations() {

      // create reverse engineered mapping entries
      foreach ($this->databaseRelationTables as $relationTable) {

         $selectCreate = 'SHOW COLUMNS FROM ' . $relationTable;
         $resultCreate = $this->dbDriver->executeTextStatement($selectCreate);

         $fields = array();
         while ($dataCreate = $this->dbDriver->fetchData($resultCreate)) {
            $fields[] = $dataCreate;
         }

         $relationName = substr($relationTable, 4);
         $sourceId = $fields[0]['Field'];
         $targetId = $fields[1]['Field'];
         $sourceObject = str_replace('ID', '', $sourceId);
         $targetObject = str_replace('ID', '', $targetId);

         $this->reEngineeredRelationTable[$relationName] = array(
            'Type' => $this->getRelationTypeLabel($relationTable),
            'Table' => $relationTable,
            'SourceID' => $sourceId,
            'TargetID' => $targetId,
            'SourceObject' => str_replace('Source_', '', $sourceObject),
            'TargetObject' => str_replace('Target_', '', $targetObject),
            'Timestamps' => ((isset ($fields[2]) === true && $fields[2]['Field'] == 'CreationTimestamp') ? 'TRUE' : 'FALSE')
         );
      }
   }

   /**
    * @private
    *
    * Analyzes the current set of database tables and adds an alter statement for the
    * storage engine if necessary.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2011<br />
    */
   private function generateStorageEngineUpdate() {

      foreach ($this->databaseMappingTables as $objectTable) {

         $selectEngine = 'SHOW CREATE TABLE `' . $objectTable . '`';
         $resultEngine = $this->dbDriver->executeTextStatement($selectEngine);
         $dataEngine = $this->dbDriver->fetchData($resultEngine);

         preg_match('/\s*?ENGINE=([^\s]+)\s*?/', $dataEngine['Create Table'], $matches);
         $engine = $matches[1];

         if ($this->getStorageEngine() != $engine) {
            $this->updateStatements[] = 'ALTER TABLE `' . $objectTable . '` ENGINE = ' . $this->getStorageEngine() . ';';
         }
      }
   }

   /**
    * @private
    *
    * Creates a object mapping out of the database tables.
    */
   private function reEngineerMappings() {

      foreach ($this->databaseMappingTables as $objectTable) {

         $selectCreate = 'SHOW COLUMNS FROM ' . $objectTable;
         $resultCreate = $this->dbDriver->executeTextStatement($selectCreate);

         $fields = array();
         while ($dataCreate = $this->dbDriver->fetchData($resultCreate)) {
            $fields[] = $dataCreate;
         }
         $mainFields = $this->getRelevantFields($fields);
         $primaryKey = $this->getPrimaryKeyName($fields);
         $objectName = str_replace('ID', '', $primaryKey);

         $objectFields = array();
         foreach ($mainFields as $field) {

            $objectFields[$field['Field']] = strtoupper($field['Type']);

            /*
              CREATE TABLE IF NOT EXISTS `test` (
              `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `Test1` varchar(10) ,
              `Test2` varchar(10) DEFAULT '',
              `Test3` varchar(10) DEFAULT NULL,
              `Test4` varchar(10) NOT NULL,
              `Test5` varchar(10) NOT NULL DEFAULT '',
              PRIMARY KEY (`ID`)
              ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

              +-------+------------------+------+-----+---------+----------------+
              | Field | Type             | Null | Key | Default | Extra          |
              +-------+------------------+------+-----+---------+----------------+
              | ID    | int(10) unsigned | NO   | PRI | NULL    | auto_increment |
              | Test1 | varchar(10)      | YES  |     | NULL    |                |
              | Test2 | varchar(10)      | YES  |     |         |                |
              | Test3 | varchar(10)      | YES  |     | NULL    |                |
              | Test4 | varchar(10)      | NO   |     | NULL    |                |
              | Test5 | varchar(10)      | NO   |     |         |                |
              +-------+------------------+------+-----+---------+----------------+
             */

            // correct empty NULL values as NO for MySQL4
            if (empty($field['Null'])) {
               $field['Null'] = 'NO';
            }

            //
            /* if($field['Null'] == 'YES' && $field['Default'] == 'NULL'){
              $objectFields[$field['Field']] .= '';
              }
              elseif($field['Null'] == 'YES' && empty($field['Default'])){
              $objectFields[$field['Field']] .= 'DEFAULT \'\'';
              }
              elseif($field['Null'] == 'NO' && $field['Default'] == 'NULL'){
              $objectFields[$field['Field']] .= 'NOT NULL';
              }
              elseif($field['Null'] == 'NO' && empty($field['Default'])){
              $objectFields[$field['Field']] .= 'NOT NULL DEFAULT \'\'';
              } */

            // add a null/not null indicator to preserve the correct data type
            if ($field['Null'] == 'NO') {
               $objectFields[$field['Field']] .= ' NOT NULL';
            } else {
               $objectFields[$field['Field']] .= ' NULL';
            }

            // add default indicator to preserve correct data type
            if (!empty($field['Default'])) {
               $objectFields[$field['Field']] .= ' DEFAULT \'' . $field['Default'] . '\'';
            } else {
               $objectFields[$field['Field']] .= ' DEFAULT \'\'';
            }

         }

         $this->reEngineeredMappingTable[$objectName] = array_merge(
            array(
               'ID' => $primaryKey,
               'Table' => $objectTable
            ),
            $objectFields
         );
      }
   }

   /**
    * @private
    *
    * Generates update statements for the mapping configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2009<br />
    * Version 0.2, 13.10.2009 (Added ticks to delimit table names.)<br />
    */
   private function generateMappingUpdateStatements() {

      foreach ($this->newMappings as $newMapping => $DUMMY) {
         $this->updateStatements[] =
               $this->generateMappingTableLayout(
                  $newMapping,
                  $this->mappingTable[$newMapping]
               );
      }

      foreach ($this->removedMappings as $removedMapping => $DUMMY) {
         $this->updateStatements[] = 'DROP TABLE '
               . $this->reEngineeredMappingTable[$removedMapping]['Table']
               . ';';
      }

      foreach ($this->newMappingAttributes as $newAttribute => $values) {

         /* @var $values array */
         if (count($values) > 0) {

            foreach ($values as $name => $dataType) {
               $dataType = preg_replace(
                  $this->rowTypeMappingFrom,
                  $this->rowTypeMappingTo,
                  $dataType
               );

               // add dynamic character set specification
               $dataType = str_replace('[charset]', $this->getTableCharset(), $dataType);

               $this->updateStatements[] = 'ALTER TABLE `'
                     . $this->mappingTable[$newAttribute]['Table'] . '` ADD `'
                     . $name . '` ' . $dataType . ';';
            }
         }
      }

      foreach ($this->removedMappingAttributes as $removedAttribute => $values) {

         if (count($values) > 0) {

            foreach ($values as $name => $dataType) {
               $this->updateStatements[] = 'ALTER TABLE `'
                     . $this->mappingTable[$removedAttribute]['Table'] . '` DROP `' . $name . '`;';
            }
         }
      }

      foreach ($this->alteredMappingAttributes as $alteredAttribute => $values) {

         if (count($values) > 0) {

            foreach ($values as $name) {
               $dataType = preg_replace(
                  $this->rowTypeMappingFrom,
                  $this->rowTypeMappingTo,
                  $this->mappingTable[$alteredAttribute][$name]
               );

               // add dynamic character set specification
               $dataType = str_replace('[charset]', $this->getTableCharset(), $dataType);

               $this->updateStatements[] = 'ALTER TABLE `'
                     . $this->mappingTable[$alteredAttribute]['Table'] . '` CHANGE `' . $name . '` '
                     . '`' . $name . '` ' . $dataType . ';';
            }
         }
      }
   }

   /**
    * @private
    *
    * Analyzes the old and new mapping configuration and stores the changes locally.
    */
   private function analyzeMappingConfigurationChanges() {

      // gather overall mapping changes
      $this->newMappings = array_diff_ukey(
         $this->mappingTable,
         $this->reEngineeredMappingTable,
         array($this, 'compareMappings')
      );
      $this->removedMappings = array_diff_ukey(
         $this->reEngineeredMappingTable,
         $this->mappingTable,
         array($this, 'compareMappings')
      );

      // evaluate changes within the attributes
      foreach ($this->mappingTable as $mappingKey => $mappingValue) {

         // only scan entries, that are not within the new and removed ones!
         if (!isset($this->newMappings[$mappingKey])
               && !isset($this->removedMappings[$mappingKey])
         ) {

            // new columns
            $this->newMappingAttributes[$mappingKey] = array_diff_ukey(
               $this->mappingTable[$mappingKey],
               $this->reEngineeredMappingTable[$mappingKey],
               array($this, 'compareMappings')
            );

            // removed columns
            $this->removedMappingAttributes[$mappingKey] = array_diff_ukey(
               $this->reEngineeredMappingTable[$mappingKey],
               $this->mappingTable[$mappingKey],
               array($this, 'compareMappings')
            );

            // changed columns
            foreach ($this->mappingTable[$mappingKey] as $key => $value) {

               // only scan entries, that are also existent within the re-engineered mapping table!
               if (isset($this->reEngineeredMappingTable[$mappingKey][$key])) {
                  $diff = $this->compareMappingValues(
                     $this->mappingTable[$mappingKey][$key],
                     $this->reEngineeredMappingTable[$mappingKey][$key]
                  );
                  if ($diff === 1) {
                     $this->alteredMappingAttributes[$mappingKey][] = $key;
                  }
               }
            }
         }
      }
   }

   /**
    * @private
    *
    * Analyzes the old and new relation configuration and stores the changes locally.
    * With relations, only type changes can be applied. Otherwise the data structure
    * gets corrupted!
    */
   private function analyzeRelationConfigurationChanges() {

      // new relations
      $this->newRelations = array_diff_ukey(
         $this->relationTable,
         $this->reEngineeredRelationTable,
         array($this, 'compareRelations')
      );

      // removed relations
      $this->removedRelations = array_diff_ukey(
         $this->reEngineeredRelationTable,
         $this->relationTable,
         array($this, 'compareRelations')
      );

      // evaluate changes within the attributes
      foreach ($this->relationTable as $relationKey => $relationValue) {

         // use lowercase relation key for re-engineered values!
         $reEngRelationKey = strtolower($relationKey);

         // only scan entries, that are not within the new and removed ones!
         if (!isset($this->newRelations[$relationKey])
               && !isset($this->removedRelations[$relationKey])
         ) {

            // changed columns (we only check for relation type, because for all other
            // cases, a new relation *must* be created!)
            foreach ($this->relationTable[$relationKey] as $key => $DUMMY) {

               // only scan entries, that are also existent within the re-engineered
               // relation table! Further, only respect the type key.
               if (isset($this->reEngineeredRelationTable[$reEngRelationKey][$key]) && $key == 'Type') {
                  if ($this->reEngineeredRelationTable[$reEngRelationKey][$key]
                        !== $this->relationTable[$relationKey][$key]
                  ) {
                     $this->alteredRelationAttributes[] = $relationKey;
                  }
               }

               // check for changed columns (e.g. wrong namings for source and target columns)
               if (isset($this->reEngineeredRelationTable[$reEngRelationKey][$key])
                     && ($key === 'SourceID' || $key === 'TargetID')
               ) {
                  if ($this->reEngineeredRelationTable[$reEngRelationKey][$key] !== $this->relationTable[$relationKey][$key]) {
                     $update = 'ALTER TABLE `' . $this->reEngineeredRelationTable[$reEngRelationKey]['Table'] . '` ';
                     $update .= 'CHANGE `' . $this->reEngineeredRelationTable[$reEngRelationKey][$key] . '` `' . $this->relationTable[$relationKey][$key] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\';' . PHP_EOL;
                     $this->updateStatements[] = $update;
                  }
               }

               if (isset($this->reEngineeredRelationTable[$reEngRelationKey][$key])
                     && isset($this->reEngineeredRelationTable[$reEngRelationKey]['TargetID'])
                     && isset($this->reEngineeredRelationTable[$reEngRelationKey]['Table'])
                     && ($key == 'SourceID')
               ) {
                  $sourceIdColumn = str_replace('Source_', '', $this->reEngineeredRelationTable[$reEngRelationKey][$key]);
                  $targetIdColumn = str_replace('Target_', '', $this->reEngineeredRelationTable[$reEngRelationKey]['TargetID']);
                  if ($sourceIdColumn == $this->reEngineeredRelationTable[$reEngRelationKey][$key] &&
                        $targetIdColumn == $this->reEngineeredRelationTable[$reEngRelationKey]['TargetID']
                  ) {
                     // header
                     $update = 'ALTER TABLE `' . $this->reEngineeredRelationTable[$reEngRelationKey]['Table'] . '` ' . PHP_EOL;

                     // source id
                     $update .= 'CHANGE `' . $sourceIdColumn . '`  `Source_' . $sourceIdColumn . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\',' . PHP_EOL;

                     // target id
                     $update .= 'CHANGE `' . $targetIdColumn . '`  `Target_' . $targetIdColumn . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\'' . PHP_EOL;

                     $this->updateStatements[] = $update;
                  }
               }
            }

            // ensure to have the configuration for relation-timestamps
            $this->relationTable[$relationKey]['Timestamps'] = ((isset ($this->relationTable[$relationKey]['Timestamps']) === true && strcasecmp($this->relationTable[$relationKey]['Timestamps'], 'TRUE') == 0) ? 'TRUE' : 'FALSE');

            // check for changed relation-timestamps
            if ($this->relationTable[$relationKey]['Timestamps'] != $this->reEngineeredRelationTable[$reEngRelationKey]['Timestamps']) {
               // header
               $update = 'ALTER TABLE `' . $this->reEngineeredRelationTable[$reEngRelationKey]['Table'] . '` ' . PHP_EOL;

               // add relation-timestamps
               if ($this->relationTable[$relationKey]['Timestamps'] == 'TRUE') {
                  $update .= 'ADD `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP;';
               } // remove relation-timestamps
               else {
                  $update .= 'DROP `CreationTimestamp`;';
               }

               $this->updateStatements[] = $update;
            }
         }
      }
   }

   /**
    * @private
    *
    * Generates update statements for relation changes.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    */
   private function generateRelationUpdateStatements() {

      foreach ($this->newRelations as $newRelation => $DUMMY) {
         $this->updateStatements[] =
               $this->generateRelationTableLayout($this->relationTable[$newRelation]);
      }
      foreach ($this->removedRelations as $removedRelation => $DUMMY) {
         $this->updateStatements[] = 'DROP TABLE '
               . $this->reEngineeredRelationTable[$removedRelation]['Table'] . ';';
      }

      // changed relation types: $this->alteredRelationAttributes
      foreach ($this->alteredRelationAttributes as $alteredRelation) {
         $reEngAlteredRelation = strtolower($alteredRelation);
         $this->updateStatements[] = 'RENAME TABLE `'
               . $this->reEngineeredRelationTable[$reEngAlteredRelation]['Table']
               . '` TO `' . $this->relationTable[$alteredRelation]['Table'] . '`;';
      }
   }

   /**
    * @private
    *
    * Generates a create statement for the object mapping table.
    *
    * @param string $objectName The name of the current object definition.
    * @param string[] $tableAttributes The mapping entry's attributes.
    * @return string The create statement.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    */
   private function generateMappingTableLayout($objectName, $tableAttributes) {

      // header
      $create = 'CREATE TABLE IF NOT EXISTS `' . $tableAttributes['Table'] . '` (' . PHP_EOL;

      // id row
      $create .= '  `' . $tableAttributes['ID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL auto_increment,' . PHP_EOL;

      // object properties
      foreach ($tableAttributes as $key => $value) {
         if ($key != 'ID' && $key != 'Table') {
            $value = preg_replace($this->rowTypeMappingFrom, $this->rowTypeMappingTo, $value);

            // add dynamic character set specification
            $value = str_replace('[charset]', $this->getTableCharset(), $value);

            $create .= '  `' . $key . '` ' . $value . ',' . PHP_EOL;
         }
      }

      // creation and modification information
      $create .= '  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,' . PHP_EOL;
      $create .= '  `ModificationTimestamp` timestamp NOT NULL default \'0000-00-00 00:00:00\',' . PHP_EOL;

      // primary key
      $create .= '  PRIMARY KEY (`' . $tableAttributes['ID'] . '`)';

      // additional indices
      $additionalIndices = $this->getAdditionalIndexDefinition($objectName);
      if ($additionalIndices !== null) {
         $create .= ',' . PHP_EOL . $additionalIndices . PHP_EOL;
      } else {
         $create .= PHP_EOL;
      }

      // footer
      $create .= ') ENGINE=' . $this->getStorageEngine() . ' DEFAULT CHARSET=' . $this->getTableCharset() . ';';

      // print statement
      return $create;

   }

   /**
    * @private
    *
    * Generates a create statement for the relation table.
    *
    * @param string[] $tableAttributes The relation's attributes.
    * @return string The create statement.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    * Version 0.2, 24.08.2012 (Added support for creation-timestamp)<br />
    */
   private function generateRelationTableLayout($tableAttributes) {

      // header
      $create = 'CREATE TABLE IF NOT EXISTS `' . $tableAttributes['Table'] . '` (' . PHP_EOL;

      // source id
      $create .= '  `' . $tableAttributes['SourceID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\',' . PHP_EOL;

      // target id
      $create .= '  `' . $tableAttributes['TargetID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\',' . PHP_EOL;

      // creation information
      if (isset ($tableAttributes['Timestamps']) === true && strcasecmp($tableAttributes['Timestamps'], 'TRUE') == 0) {
         $create .= '  `CreationTimestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,' . PHP_EOL;
      }

      // key for all forward JOINs
      $create .= '  KEY `JOIN` (`' . $tableAttributes['SourceID'] . '`, `' . $tableAttributes['TargetID'] . '`),' . PHP_EOL;

      // key for all reverse JOINs (see http://forum.adventure-php-framework.org/viewtopic.php?f=8&t=548)
      $create .= '  KEY `REVERSEJOIN` (`' . $tableAttributes['TargetID'] . '`, `' . $tableAttributes['SourceID'] . '`)' . PHP_EOL;

      // footer
      return $create . ') ENGINE=' . $this->getStorageEngine() . ' DEFAULT CHARSET=' . $this->getTableCharset() . ';';

   }

   private function getAdditionalIndexDefinition($objectName) {

      // exit early returning null to indicate that no additional index definition is available
      if (!isset($this->mappingIndexTable[$objectName])) {
         return null;
      }

      $indices = array();

      foreach (explode('|', $this->mappingIndexTable[$objectName]) as $index) {

         $current = $index;

         // gather type
         $startPos = strpos($current, '(');
         $endPos = strpos($current, ')', $startPos);
         $type = substr($current, $startPos + 1, $endPos - $startPos - 1);

         // replace index type to extract fields
         $current = substr_replace($current, '', $startPos, $endPos + 1 - $startPos);

         $fields = array();

         foreach (explode(',', $current) as $field) {
            $fields[] = '`' . $field . '`';
         }

         $type .= ' KEY `' . preg_replace('/[^A-Za-z0-9]/', '', $index) . '` (' . implode(', ', $fields) . ')';

         // resolve INDEX KEY
         $type = preg_replace('/INDEX KEY/i', 'KEY', $type);

         $indices[] = '  ' . $type;

      }

      return implode(',' . PHP_EOL, $indices);

   }

   private function analyzeIndexColumnDataTypeChanges() {

      $indexColumnDataType = $this->getIndexColumnDataType();
      $normalizedIndexColumnDataType = strtolower($indexColumnDataType);

      foreach ($this->databaseMappingTables as $objectTable) {

         $selectCreate = 'SHOW COLUMNS FROM ' . $objectTable;
         $resultCreate = $this->dbDriver->executeTextStatement($selectCreate);

         $fields = array();
         while ($dataCreate = $this->dbDriver->fetchData($resultCreate)) {
            $fields[] = $dataCreate;
         }

         $primaryKey = $this->getPrimaryKeyName($fields);

         foreach ($fields as $field) {
            if ($field['Field'] === $primaryKey) {
               // check for data type and note changes:
               if (strtolower($field['Type']) !== $normalizedIndexColumnDataType) {
                  $this->alteredIndexDataColumnTypeObjectFields[] = array(
                     'Table' => $objectTable,
                     'Field' => $primaryKey,
                     'ToDataType' => $indexColumnDataType
                  );
               }

            }
         }
      }

      foreach ($this->databaseRelationTables as $relationTable) {

         $selectCreate = 'SHOW COLUMNS FROM ' . $relationTable;
         $resultCreate = $this->dbDriver->executeTextStatement($selectCreate);

         $fields = array();
         while ($dataCreate = $this->dbDriver->fetchData($resultCreate)) {
            $fields[] = $dataCreate;
         }

         foreach ($fields as $field) {

            if (strpos($field['Field'], 'Source_') !== false || strpos($field['Field'], 'Target_') !== false) {
               // check for data type and note changes:
               if (strtolower($field['Type']) !== $normalizedIndexColumnDataType) {
                  $this->alteredIndexDataColumnTypeRelationFields[] = array(
                     'Table' => $relationTable,
                     'Field' => $field['Field'],
                     'ToDataType' => $indexColumnDataType
                  );
               }
            }

         }
      }

   }

   private function generateIndexColumnDataTypeStatements() {
      foreach ($this->alteredIndexDataColumnTypeObjectFields as $field) {
         $this->updateStatements[] = 'ALTER TABLE `' . $field['Table'] . '` CHANGE COLUMN `' . $field['Field'] . '` `' . $field['Field'] . '` ' . $field['ToDataType'] . ' NOT NULL auto_increment;';
      }
      // generate relation table statements tailored to relation data model (w/o auto_increment)
      foreach ($this->alteredIndexDataColumnTypeRelationFields as $field) {
         $this->updateStatements[] = 'ALTER TABLE `' . $field['Table'] . '` CHANGE COLUMN `' . $field['Field'] . '` `' . $field['Field'] . '` ' . $field['ToDataType'] . ' NOT NULL default \'0\';';
      }
   }

}
