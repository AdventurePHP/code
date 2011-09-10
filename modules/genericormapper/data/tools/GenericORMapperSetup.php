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

import('modules::genericormapper::data', 'BaseMapper');

/**
 * @package modules::genericormapper::data
 * @class GenericORMapperSetup
 *
 * This tool allows you to setup a database for use with the generic or mapper. It enables
 * you to generate the table layout from a given couple of configuration files (objects and
 * relations). In order to adapt the automatic
 *
 * In order to adapt the automatically generated changeset, please ensure the last param
 * to be <em>false</em>. This results in displaying the change statements rather to execute
 * them agains the given database.
 * <p/>
 * Changes to the database layout can be applied using the <strong>GenericORMapperUpdate</strong>
 * utility. Please refer to the documentation of this tool for update details!
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.05.2008<br />
 */
class GenericORMapperSetup extends BaseMapper {

   /**
    * @protected
    * Indicators for the datatype mapping.
    */
   protected $RowTypeMappingFrom = array(
      '/^VARCHAR\(([0-9]+)\)$/i',
      '/^TEXT$/i',
      '/^DATE$/i'
   );

   /**
    * @protected
    * Replace strings for the datatype mapping.
    */
   protected $RowTypeMappingTo = array(
      'VARCHAR($1) character set [charset] NOT NULL default \'\'',
      'TEXT character set [charset] NOT NULL',
      'DATE NOT NULL default \'0000-00-00\''
   );

   /**
    * @protected
    * @var string Stores the MySQL storage engine type.
    */
   protected $StorageEngine = 'MyISAM';

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

   public function setStorageEngine($engine) {
      $this->StorageEngine = $engine;
   }

   public function getStorageEngine() {
      return $this->StorageEngine;
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
    * Setups the database. Uses the ConnectionManager, hence a valid database connection is required.
    * If the third parameter ($ConnectionName) is left blank, the statements are displayed only.
    *
    * @param string $configNamespace namespace, where the desired mapper configuration is located
    * @param string $configNameAffix name affix of the object and relation definition files
    * @param string $connectionName name of the connection, that the mapper should use to access the database
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.05.2008<br />
    * Version 0.2, 23.06.2008 (Improvements (configurable storage engine, ...) and addition of display only feature)<br />
    */
   public function setupDatabase($configNamespace, $configNameAffix, $connectionName = null) {

      // set the config namespace
      $this->configNamespace = $configNamespace;

      // set the config name affix
      $this->configNameAffix = $configNameAffix;

      // setup object layout
      $objects = $this->generateObjectLayout();

      // setup relation layout
      $relations = $this->generateRelationLayout();

      // display only
      if ($connectionName === null) {

         // display object structure
         echo '<pre>';
         foreach ($objects as $object) {
            echo PHP_EOL . PHP_EOL . $object;
         }

         // display relation structure
         foreach ($relations as $relation) {
            echo PHP_EOL . PHP_EOL . $relation;
         }
         echo '</pre>';

      } else {

         // get connection manager
         $cM = &$this->getServiceObject('core::database', 'ConnectionManager');

         // initialize connection
         $this->dbDriver = &$cM->getConnection($connectionName);

         // create object structure
         foreach ($objects as $object) {
            $this->dbDriver->executeTextStatement($object);
         }

         // create relation structure
         foreach ($relations as $relation) {
            $this->dbDriver->executeTextStatement($relation);
         }

      }

   }

   /**
    * @protected
    *
    * Creates the setup statements for the object persistance.
    *
    * @return string Sql setup script
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 31.05.2008 (Code completed and refactored due to changes on the mapping table)<br />
    * Version 0.3, 09.12.2008 (Replaced TINYINT by INT)<br />
    * Version 0.4, 18.05.2009 (Changed primary key columns to UNSIGNED)<br />
    * Version 0.5, 11.10.2009 (Outsorced table statement creation due to introduction of the update feature)<br />
    */
   protected function generateObjectLayout() {

      // create mapping table
      $this->createMappingTable();

      // generate tables for objects
      $setup = array();
      foreach ($this->mappingTable as $name => $attributes) {
         $setup[] =
               $this->generateMappingTableLayout($name, $this->mappingTable[$name])
               . PHP_EOL . PHP_EOL;
      }

      return $setup;

   }

   /**
    * @protected
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
   protected function generateMappingTableLayout($objectName, $tableAttributes) {

      // header
      $create = 'CREATE TABLE IF NOT EXISTS `' . $tableAttributes['Table'] . '` (' . PHP_EOL;

      // id row
      $create .= '  `' . $tableAttributes['ID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL auto_increment,' . PHP_EOL;

      // object properties
      foreach ($tableAttributes as $key => $value) {
         if ($key != 'ID' && $key != 'Table') {
            $value = preg_replace($this->RowTypeMappingFrom, $this->RowTypeMappingTo, $value);

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
    * @protected
    *
    * Creates the setup statements for the relation persistence.
    *
    * @return string Sql setup script.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 31.05.2008<br />
    * Version 0.2, 09.12.2008 (Replaced TINYINT by INT)<br />
    * Version 0.3, 18.05.2009 (Changed primary key columns to UNSIGNED)<br />
    * Version 0.4, 30.07.2009 (Changed foreign key columns to UNSIGNED)<br />
    */
   protected function generateRelationLayout() {

      // create relation table
      $this->createRelationTable();

      // generate tables for objects
      $setup = array();
      foreach ($this->relationTable as $name => $attributes) {
         $setup[] =
               $this->generateRelationTableLayout(
                  $this->relationTable[$name]
               ) . PHP_EOL . PHP_EOL;
      }

      return $setup;

   }

   /**
    * @protected
    *
    * Generates a create statement for the relation table.
    *
    * @param string[] $tableAttributes The relation's attributes.
    * @return string The create statement.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    */
   protected function generateRelationTableLayout($tableAttributes) {

      // header
      $create = 'CREATE TABLE IF NOT EXISTS `' . $tableAttributes['Table'] . '` (' . PHP_EOL;

      // source id
      $create .= '  `' . $tableAttributes['SourceID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\',' . PHP_EOL;

      // target id
      $create .= '  `' . $tableAttributes['TargetID'] . '` ' . $this->getIndexColumnDataType() . ' NOT NULL default \'0\',' . PHP_EOL;

      // key for all forward JOINs
      $create .= '  KEY `JOIN` (`' . $tableAttributes['SourceID'] . '`, `' . $tableAttributes['TargetID'] . '`),' . PHP_EOL;

      // key for all reverse JOINs (see http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=548)
      $create .= '  KEY `REVERSEJOIN` (`' . $tableAttributes['TargetID'] . '`, `' . $tableAttributes['SourceID'] . '`)' . PHP_EOL;

      // footer
      return $create .= ') ENGINE=' . $this->getStorageEngine() . ' DEFAULT CHARSET=' . $this->getTableCharset() . ';';

   }

   protected function getAdditionalIndexDefinition($objectName) {

      // exit early returning null to indicate that no additional index definition is available
      if (!isset($this->mappingIndexTable[$objectName])) {
         return null;
      }

      $indices = array();

      foreach (explode('|', $this->mappingIndexTable[$objectName]) as $index) {

         $current = $index;
         $type = (string)'';

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

}

?>