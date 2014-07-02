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
namespace APF\modules\genericormapper\data\tools;

use APF\modules\genericormapper\data\BaseMapper;
use APF\modules\genericormapper\data\GenericORMapperException;

/**
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
    * Indicators for the data type mapping.
    *
    * @var string[] $rowTypeMappingFrom
    */
   protected $rowTypeMappingFrom = array(
         '/^VARCHAR\(([0-9]+)\)$/i',
         '/^TEXT$/i',
         '/^DATE$/i',
   );

   /**
    * Replace strings for the data type mapping.
    *
    * @var string[] $rowTypeMappingTo
    */
   protected $rowTypeMappingTo = array(
         'VARCHAR($1) character set [charset] NOT NULL default \'\'',
         'TEXT character set [charset] NOT NULL',
         'DATE NOT NULL default \'0000-00-00\'',
   );

   /**
    * Stores the default MySQL storage engine type.
    *
    * @var string $storageEngine
    */
   protected $storageEngine = 'MyISAM';

   /**
    * Data type for the index Column
    *
    * @var String $indexColumnDataType
    */
   protected $indexColumnDataType = 'INT(5) UNSIGNED';

   /**
    * Stores the default Charset
    *
    * @var String $tableCharset
    */
   protected $tableCharset = 'utf8';

   /**
    * Mapping and relation tables constructed from the configuration files
    *
    * @var array $tablesFromConfig
    */
   protected $tablesFromConfig = array();

   /**
    * Mapping and relation tables constructed from the given database connection
    *
    * @var array $tablesFromDatabase
    */
   protected $tablesFromDatabase = array();

   /**
    * Stores the new tables
    *
    * @var array $tablesToCreate
    */
   protected $tablesToCreate = array();

   /**
    * Stores the removed tables
    *
    * @var array $tablesToDrop
    */
   protected $tablesToDrop = array();

   /**
    * Stores the new columns
    *
    * @var array $columnsToCreate
    */
   protected $columnsToCreate = array();

   /**
    * Stores the removed columns
    *
    * @var array $columnsToDrop
    */
   protected $columnsToDrop = array();

   /**
    * Stores the changed columns
    *
    * @var array $columnsToChange
    */
   protected $columnsToChange = array();

   /**
    * Stores the new indices
    *
    * @var array $indicesToCreate
    */
   protected $indicesToCreate = array();

   /**
    * Stores the removed indices
    *
    * @var array $indicesToDrop
    */
   protected $indicesToDrop = array();

   /**
    * Stores the changed storage engines
    *
    * @var array $storageEngineToChange
    */
   protected $storageEngineToChange = array();

   /**
    * Stores the update statements
    *
    * @var array $updateStatements
    */
   protected $updateStatements = array();

   /**
    * Stores alias used by MySQL to avoid unnecessary update statements
    * TODO Extend with more aliases.
    *
    * @var array $mysqlAlias
    */
   protected $mysqlAlias = array(
         'int'       => 'int(11)',
         'integer'   => 'int(11)',
         'boolean'   => 'tinyint(1)',
         'bool'      => 'tinyint(1)',
         'smallint'  => 'smallint(6)',
         'mediumint' => 'mediumint(9)',
         'bigint'    => 'bigint(20)'
   );

   public function getStorageEngine() {
      return $this->storageEngine;
   }

   /**
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

   public function getIndexColumnDataType() {
      return $this->indexColumnDataType;
   }

   /**
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

   /**
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
    *                               Default is false.
    *
    * @throws GenericORMapperException In case of missing connection name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.10.2009<br />
    */
   public function run($updateInPlace = false) {

      // ID#104: clean up volatile data to allow multiple runs with deterministic results
      $this->tablesFromConfig = array();
      $this->tablesFromDatabase = array();
      $this->tablesToCreate = array();
      $this->tablesToDrop = array();
      $this->columnsToCreate = array();
      $this->columnsToDrop = array();
      $this->columnsToChange = array();
      $this->indicesToCreate = array();
      $this->indicesToDrop = array();
      $this->storageEngineToChange = array();
      $this->updateStatements = array();

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


      $this->getTablesFromConfig();
      $this->getTablesFromDatabase();

      $this->compareTables();

      $this->generateDropTableStatements();
      $this->generateCreateTableStatements();
      $this->generateStorageEngineUpdateStatements();
      $this->generateDropIndexStatements();
      $this->generateDropColumnStatements();
      $this->generateAddColumnStatements();
      $this->generateChangeColumnStatements();
      $this->generateAddIndexStatements();


      // print update statements or execute them immediately in case we have a connection name and
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

   protected function getTablesFromConfig() {

      $attrExceptions = array('ID', 'Table');
      $tables = array();

      foreach ($this->mappingTable as $table => $property) {
         $tableName = $property['Table'];
         $tables[$tableName] = array(
               'Indices'       => $this->getIndices($table),
               'StorageEngine' => (isset($this->mappingStorageEngineTable[$tableName])) ? $this->mappingStorageEngineTable[$tableName] : $this->storageEngine,
               'Columns'       => array(
                     $property['ID'] => $this->indexColumnDataType . ' NOT NULL auto_increment'
               ),
               'autoIncrement' => $property['ID']
         );
         foreach ($property as $key => $value) {
            if (!in_array($key, $attrExceptions)) {
               $value1 = preg_replace(
                     $this->rowTypeMappingFrom,
                     $this->rowTypeMappingTo,
                     $value);
               $value = str_replace('[charset]', $this->getTableCharset(), $value1);
               $tables[$tableName]['Columns'][$key] = $value;
            }
         }
         $tables[$tableName]['Columns']['CreationTimestamp'] = 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP';
         $tables[$tableName]['Columns']['ModificationTimestamp'] = 'timestamp NOT NULL DEFAULT \'0000-00-00 00:00:00\'';

      }

      foreach ($this->relationTable as $property) {
         $tableName = $property['Table'];
         $tables[$tableName] = array(
               'Indices'       => array(
                     $property['TargetID'] . ',' . $property['SourceID'] => array('REVERSEJOIN' => 'INDEX'),
                     $property['SourceID'] . ',' . $property['TargetID'] => array('JOIN' => 'INDEX')
               ),
               'StorageEngine' => (isset($this->relationStorageEngineTable[$tableName])) ? $this->relationStorageEngineTable[$tableName] : $this->storageEngine,
               'Columns'       => array(
                     $property['SourceID'] => $this->indexColumnDataType . ' NOT NULL default 0',
                     $property['TargetID'] => $this->indexColumnDataType . ' NOT NULL default 0',
               )
         );
         if (isset ($property['Timestamps']) && strcasecmp($property['Timestamps'], 'TRUE') === 0) {
            $tables[$tableName]['Columns']['CreationTimestamp'] = 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP';
         }

      }

      $this->tablesFromConfig = $tables;

   }

   protected function getIndices($tableName) {

      $indexDefinition = array();
      $indexDefinition[$tableName . 'ID'] = array('PRIMARY' => 'PRIMARY');
      if (!isset($this->mappingIndexTable[$tableName])) {
         return $indexDefinition;
      }
      foreach (explode('|', $this->mappingIndexTable[$tableName]) as $index) {

         $current = $index;

         // gather type
         $startPos = strpos($current, '(');
         $endPos = strpos($current, ')', $startPos);
         $type = substr($current, $startPos + 1, $endPos - $startPos - 1);

         // replace index type to extract fields
         $current = substr_replace($current, '', $startPos, $endPos + 1 - $startPos);

         $indexDefinition[str_replace(' ', '', $current)][str_replace(array(',', ' '), '', $current) . $type] = $type;
      }

      return $indexDefinition;
   }

   public function getTableCharset() {
      return $this->tableCharset;
   }

   /**
    * Let's you influence the character sets, the tables are created with. By default,
    * utf8 is used to have good compatibility with most of the application cases. If
    * you want to change it for certain reasons, use this method conjunction with an
    * appropriate MySQL character set.
    *
    * @see http://dev.mysql.com/doc/refman/5.0/en/data-types.html
    *
    * @param string $tableCharset The desired charset (e.g. utf8).
    *
    * @since 1.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.04.2010<br />
    */
   public function setTableCharset($tableCharset) {
      $this->tableCharset = $tableCharset;
   }

   protected function getTablesFromDatabase() {

      $getColumns = '(select
                        `character_set_name` as `charset`,
                        `table_name` as `tableName`,
                        `column_name` as `columnName`,
                        `column_default` as `defaultValue`,
                        `is_nullable` as `isNullable` ,
                        `column_type` as `type`,
                        `extra`
                     from information_schema.columns
                     where
                        `table_schema` = database()
                        and (`table_name` like \'ent_%\'
                        or `table_name` like \'ass_%\'
                        or `table_name` like \'cmp_%\'));';
      $resultColumns = $this->dbDriver->executeTextStatement($getColumns);

      while ($columnsData = $this->dbDriver->fetchData($resultColumns)) {
         $tableName = $columnsData['tableName'];
         $columnName = $columnsData['columnName'];
         unset($columnsData['tableName'], $columnsData['columnName']);
         $this->tablesFromDatabase[$tableName]['Columns'][$columnName] = $columnsData;
         $this->tablesFromDatabase[$tableName]['Indices'] = array();
      }

      $getEngine = '(select
                        `table_name` as `TableName`,
                        `Engine` as `StorageEngine`
                     from information_schema.tables
                     where
                        `table_schema` = database()
                        and
                        (`table_name` like \'ent_%\'
                        or `table_name` like \'ass_%\'
                        or `table_name` like \'cmp_%\')
                        );';
      $resultEngine = $this->dbDriver->executeTextStatement($getEngine);

      while ($engineData = $this->dbDriver->fetchData($resultEngine)) {
         $this->tablesFromDatabase[$engineData['TableName']]['StorageEngine'] = $engineData['StorageEngine'];
      }

      $getIndex = 'select
                     `index_name` as indexName ,
                     `table_name` as tableName ,
                     `non_unique` as nonUnique,
                     `index_type` as indexType,
                     group_concat(`column_name` ORDER BY `seq_in_index`) as \'columns\'
                  from (
                     select * from INFORMATION_SCHEMA.statistics
                     where
                        table_schema = database()
                        and (`table_name` like \'ent_%\'
                        or `table_name` like \'ass_%\'
                        or `table_name` like \'cmp_%\')
                  ) as indextable
                  group by
                     `non_unique`,
                     `index_name`,
                     `index_type`,
                     `table_name`;';

      $resultIndex = $this->dbDriver->executeTextStatement($getIndex);

      while ($indexData = $this->dbDriver->fetchData($resultIndex)) {

         if ($indexData['indexName'] == 'PRIMARY') {
            $indexType = 'PRIMARY';
         } elseif ($indexData['indexType'] == 'BTREE') {
            if ($indexData['nonUnique'] == 0) {
               $indexType = 'UNIQUE';
            } else {
               $indexType = 'INDEX';
            }
         } elseif ($indexData['indexType'] == 'FULLTEXT') {
            $indexType = 'FULLTEXT';
         } else
            $indexType = '';
         $this->tablesFromDatabase[$indexData['tableName']]['Indices'][$indexData['columns']][$indexData['indexName']] = $indexType;
      }

   }

   protected function compareTables() {

      $this->tablesToCreate = array_diff_key($this->tablesFromConfig, $this->tablesFromDatabase);
      $this->tablesToDrop = array_diff_key($this->tablesFromDatabase, $this->tablesFromConfig);
      $tablesFromDatabase = array_intersect_key($this->tablesFromDatabase, $this->tablesFromConfig);
      $tablesFromConfig = array_intersect_key($this->tablesFromConfig, $this->tablesFromDatabase);

      foreach ($tablesFromDatabase as $tableName => $property) {

         $this->compareColumns($tableName, $property['Columns'], $tablesFromConfig[$tableName]['Columns']);
         $this->compareStorageEngine($tableName, $property['StorageEngine'], $tablesFromConfig[$tableName]['StorageEngine']);
         $this->compareIndices($tableName, $property['Indices'], $tablesFromConfig[$tableName]['Indices']);

      }

   }

   protected function compareColumns($tableName, $columnsFromDatabase, $columnsFromConfig) {

      // find deleted columns
      $diff = array_diff_key($columnsFromDatabase, $columnsFromConfig);
      if (!empty($diff)) {
         $this->columnsToDrop[$tableName] = $diff;
      }

      // find new columns
      $diff = array_diff_key($columnsFromConfig, $columnsFromDatabase);
      if (!empty($diff)) {
         $this->columnsToCreate[$tableName] = $diff;
      }

      // find changed columns
      $intersect = array_intersect_key($columnsFromConfig, $columnsFromDatabase);

      foreach ($intersect as $columnName => $type) {

         // column property from database are presented as array for better comparison
         // array(
         //      'charset' => null,
         //      'defaultValue' => '0',
         //      'isNullable' => 'NO',
         //      'type' => 'int(5)',
         //      'extra' => '',
         // )
         $columnProperties = $columnsFromDatabase[$columnName];

         if ($columnProperties['charset'] !== null) {
            if (stripos($type, 'character set ' . $columnProperties['charset'] . ' ') === false) {
               $this->columnsToChange[$tableName][$columnName] = $type;
               continue;
            }
         }

         // if "NOT NULL" is not in type definition MySQL assumes that null is allowed
         if ($columnProperties['isNullable'] === 'NO' && stripos($type, 'NOT NULL') === false) {
            // exept of timestamp columns
            if (stripos($type, 'timestamp') !== 0) {
               $this->columnsToChange[$tableName][$columnName] = $type;
               continue;
            }
         }


         if (stripos($type, 'auto_increment') !== false && $columnProperties['extra'] !== 'auto_increment') {

            $this->columnsToChange[$tableName][$columnName] = $type;
            continue;
         }

         // comparing the data type definition is more tricky
         // find the column type from config
         // result examples:
         //       "int" from "int unsigned not null"
         //       "int (4)" from "int (4) unsigned not null"
         //       "varchar(20)" from "varchar(20) not null"
         preg_match('#^[ ]*\w+[ ]*\([0-9]+\)|^[ ]*\w+#iu', $type, $match);

         // prepare the result to fit mysql data type definition

         // strip spaces, convert to lowercase
         $prepared = str_replace(' ', '', strtolower($match[0]));

         // if result is an alias we replace it with the right column type
         $newType = (isset($this->mysqlAlias[$prepared])) ? str_ireplace($match[0], $this->mysqlAlias[$prepared], $type) : $type;

         // now we can compare the data type
         if (stripos($newType, $columnProperties['type']) === false) {
            $this->columnsToChange[$tableName][$columnName] = $type;
            continue;
         }

         // compare the default column values

         if ($columnProperties['defaultValue'] === null) {
            // mysql stores null as default value either if "default null" is in column type statement or no default definition is given
            // so default value is only not NULL if "default ..." is presented but not "default null"
            if (stripos($type, 'default') !== false && stripos($type, 'default null') === false) {
               $this->columnsToChange[$tableName][$columnName] = $type;
               continue;
            }
         } else {

            // prepare the needle for stripos
            switch ($columnProperties['defaultValue']) {
               case '':
                  $defaultStatement = 'DEFAULT \'\'';
                  break;
               case 'CURRENT_TIMESTAMP':
                  $defaultStatement = 'DEFAULT CURRENT_TIMESTAMP';
                  break;
               default:
                  $defaultStatement = 'DEFAULT ';
                  $defaultStatement .= (!is_numeric($columnProperties['defaultValue'])) ? '\'' . $columnProperties['defaultValue'] . '\'' : $columnProperties['defaultValue'];
                  break;
            }
            if (stripos($type, $defaultStatement) === false) {
               $this->columnsToChange[$tableName][$columnName] = $type;
               continue;
            }
         }
      }
   }

   protected function compareStorageEngine($tableName, $storageEngineFromDatabase, $storageEngineFromConfig) {

      if (strcasecmp($storageEngineFromDatabase, $storageEngineFromConfig)) {
         $this->storageEngineToChange[$tableName] = $storageEngineFromConfig;
      }

   }

   protected function compareIndices($tableName, $indicesFromDatabase, $indicesFromConfig) {

      // find removed indices
      $diff = array_diff_key($indicesFromDatabase, $indicesFromConfig);
      if (!empty($diff)) {
         $this->indicesToDrop[$tableName] = $diff;
         // clean the comparison array
         foreach ($diff as $indexColumns => $dummy) {
            unset($indicesFromDatabase[$indexColumns]);
         }
      }

      // find new indices
      $diff = array_diff_key($indicesFromConfig, $indicesFromDatabase);
      if (!empty($diff)) {
         $this->indicesToCreate[$tableName] = $diff;
         // clean the comparison array
         foreach ($diff as $indexColumns => $dummy) {
            unset($indicesFromConfig[$indexColumns]);
         }
      }

      // for the remaining indices we compare the name and type from config with the database
      foreach ($indicesFromConfig as $columns => $indices) {

         foreach ($indices as $indexName => $indexType) {

            if (!isset($indicesFromDatabase[$columns][$indexName])) {
               $this->indicesToCreate[$tableName][$columns][$indexName] = $indexType;
               continue;
            }

            if ($indicesFromDatabase[$columns][$indexName] !== $indexType) {

               // there's no change index statement, so we have to drop it and create a new one
               $this->indicesToDrop[$tableName][$columns][$indexName] = $indicesFromDatabase[$columns][$indexName];
               $this->indicesToCreate[$tableName][$columns][$indexName] = $indexType;
            }
            // remove each index which was presented in config to clean the array
            unset($indicesFromDatabase[$columns][$indexName]);
         }

      }

      // all indices remained in database-array are to be dropped
      foreach ($indicesFromDatabase as $columns => $indices) {
         if (empty($indices)) {
            continue;
         }
         foreach ($indices as $indexName => $indexType) {
            $this->indicesToDrop[$tableName][$columns][$indexName] = $indexType;
         }
      }

   }

   protected function generateDropTableStatements() {

      foreach ($this->tablesToDrop as $tableName => $dummy) {
         $this->updateStatements[] = 'DROP TABLE `' . $tableName . '`';
      }

   }

   protected function generateCreateTableStatements() {

      foreach ($this->tablesToCreate as $tableName => $property) {

         $create = 'CREATE TABLE IF NOT EXISTS `' . $tableName . '` (' . PHP_EOL;
         foreach ($property['Columns'] as $columnName => $columnType) {
            $create .= '  `' . $columnName . '` ' . $columnType . ',' . PHP_EOL;
         }

         $indexDefinition = array();

         foreach ($property['Indices'] as $columns => $indices) {
            foreach ($indices as $indexName => $indexType) {
               $indexColumns = '`' . str_replace(',', '`,`', $columns) . '`';
               switch ($indexType) {
                  case 'PRIMARY':
                     $indexDefinition[] = 'PRIMARY KEY (' . $indexColumns . ')';
                     break;
                  case 'UNIQUE':
                     $indexDefinition[] = 'UNIQUE KEY `' . $indexName . '` (' . $indexColumns . ')';
                     break;
                  case 'INDEX':
                     $indexDefinition[] = 'KEY `' . $indexName . '` (' . $indexColumns . ')';
                     break;
               }
            }
         }

         $create .= implode(',' . PHP_EOL, $indexDefinition) . PHP_EOL;
         $create .= ') ENGINE=' . $property['StorageEngine'] . ' DEFAULT charset=' . $this->tableCharset . PHP_EOL;

         $this->updateStatements[] = $create;

      }
   }

   protected function generateStorageEngineUpdateStatements() {

      foreach ($this->storageEngineToChange as $tableName => $storageEngine) {
         $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` ENGINE=' . $storageEngine;
      }

   }

   protected function generateDropIndexStatements() {

      foreach ($this->indicesToDrop as $tableName => $indices) {
         foreach ($indices as $indexDefinitions) {
            foreach ($indexDefinitions as $indexName => $indexType) {
               $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` DROP INDEX `' . $indexName . '`;';
            }
         }
      }

   }

   protected function generateDropColumnStatements() {

      foreach ($this->columnsToDrop as $tableName => $columns) {
         foreach ($columns as $columnName => $columnType) {
            $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` DROP COLUMN `' . $columnName . '`;';
         }

      }

   }

   protected function generateAddColumnStatements() {

      foreach ($this->columnsToCreate as $tableName => $columns) {
         foreach ($columns as $columnName => $columnType) {
            $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` ADD COLUMN `' . $columnName . '` ' . $columnType . ';';
         }
      }

   }

   protected function generateChangeColumnStatements() {

      foreach ($this->columnsToChange as $tableName => $columns) {
         foreach ($columns as $columnName => $columnType) {
            $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` CHANGE COLUMN `' . $columnName . '` `' . $columnName . '` ' . $columnType . ';';
         }
      }

   }

   protected function generateAddIndexStatements() {

      foreach ($this->indicesToCreate as $tableName => $indices) {
         foreach ($indices as $columns => $indexDefinitions) {
            foreach ($indexDefinitions as $indexName => $indexType) {
               $columns = '`' . str_replace(',', '`,`', $columns) . '`';
               switch ($indexType) {
                  case 'UNIQUE':
                  case 'INDEX':
                     $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` ADD ' . $indexType . ' `' . $indexName . '`  (' . $columns . ');';
                     break;
                  case 'PRIMARY':
                     $this->updateStatements[] = 'ALTER TABLE `' . $tableName . '` ADD PRIMARY KEY  (' . $columns . ');';
                     break;
               }
            }
         }
      }

   }

}
