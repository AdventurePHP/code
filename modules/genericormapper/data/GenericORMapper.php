<?php
namespace APF\modules\genericormapper\data;

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
use APF\modules\genericormapper\data\GenericORMapperDataObject;
use APF\modules\genericormapper\data\BaseMapper;
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * @package APF\modules\genericormapper\data
 * @class GenericORMapper
 *
 * Implements an abstract OR mapper, that can map any objects defined in the object
 * configuration file into a domain object. The type of the object is therefore not defined
 * by it's class name, but by the "ObjectName" attribute.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.05.2008<br />
 * Version 0.2, 15.06.2008 (Added ` to the statements due to relation saving bug)<br />
 * Version 0.3, 15.01.2011 (Added support for own domain objects)<br />
 */
class GenericORMapper extends BaseMapper {

   /**
    * @var string The identifier, that marks a property as BIT type.
    */
   private static $BIT_FIELD_IDENTIFIER = 'BIT';

   /**
    * Bug 289: This identifier is used to distinguish between fiels, that can
    * contain null values. This is necessary, because the MySQL client libs
    * map MySQL NULL values to empty PHP strings.
    * @var string Identifies fields, that can contain null values.
    */
   private static $NULL_FIELD_IDENTIFIER = 'NULL DEFAULT NULL';

   /**
    * @public
    *
    * Loads an object list.
    *
    * @param string $objectName The Name of the object in mapping table
    * @return GenericORMapperDataObject[] The desired object list
    *
    * @author Nicolas Pecher
    * @version
    * Version 0.1, 18.03.2012
    */
   public function loadObjectList($objectName) {
      $statement = 'SELECT * FROM `' . $this->mappingTable[$objectName]['Table'] . '`';
      $result = $this->dbDriver->executeTextStatement($statement, $this->logStatements);
      return $this->loadObjectListByStatementResult($objectName, $result);
   }

   /**
    * @public
    *
    * Loads an object list by a special statement. The statement must return the desired
    * object properties.
    *
    * @param string $objectName name of the object in mapping table
    * @param string $namespace namespace of the statement
    * @param string $statementName name of the statement file
    * @param array $statementParams a list of statement parameters
    * @return GenericORMapperDataObject[] The desired object list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
    */
   public function loadObjectListByStatement($objectName, $namespace, $statementName, $statementParams = array()) {
      return $this->loadObjectListByStatementResult(
         $objectName,
         $this->dbDriver->executeStatement($namespace, $statementName, $statementParams, $this->logStatements)
      );
   }

   /**
    * @public
    *
    * Loads an object list by a list of object ids.<br />
    *
    * @param string $objectName name of the object in mapping table
    * @param array $ids list of object ids
    * @return GenericORMapperDataObject[] The desired object list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.05.2008<br />
    */
   public function loadObjectListByIDs($objectName, $ids = array()) {

      // initialize return list
      $objects = array();
      $count = count($ids);

      // load objects
      for ($i = 0; $i < $count; $i++) {
         $objects[] = $this->loadObjectByID($objectName, $ids[$i]);
      }

      return $objects;

   }

   /**
    * @public
    *
    * Loads an object list by a special statement. The statement must return the desired
    * object properties.
    *
    * @param string $objectName Name of the object in mapping table
    * @param string $statement Sql statement
    * @return GenericORMapperDataObject[] The desired object list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    */
   public function loadObjectListByTextStatement($objectName, $statement) {
      return $this->loadObjectListByStatementResult($objectName, $this->dbDriver->executeTextStatement($statement, $this->logStatements));
   }

   /**
    * @public
    *
    * Loads an object by a special statement. The statement must return the desired object properties.
    *
    * @param string $objectName name of the object in mapping table
    * @param string $namespace namespace of the statement
    * @param string $statementName name of the statement file
    * @param array $statementParams a list of statement parameters
    * @return GenericORMapperDataObject The desired object
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
    */
   public function loadObjectByStatement($objectName, $namespace, $statementName, $statementParams = array()) {
      $result = $this->dbDriver->executeStatement($namespace, $statementName, $statementParams, $this->logStatements);
      $data = $this->dbDriver->fetchData($result);
      return $this->mapResult2DomainObject($objectName, $data);
   }

   /**
    * @public
    *
    * Loads an object by a special statement. The statement must return the desired
    * object properties.
    *
    * @param string $objectName name of the object in mapping table
    * @param string $statement sql statement
    * @return GenericORMapperDataObject The desired object or null if the object has not been found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 25.05.2008 (Corrected the call of the executeTextStatement() method)<br />
    */
   public function loadObjectByTextStatement($objectName, $statement) {
      $result = $this->dbDriver->executeTextStatement($statement, $this->logStatements);
      $data = $this->dbDriver->fetchData($result);
      if ($data === false) {
         return null;
      }
      return $this->mapResult2DomainObject($objectName, $data);
   }

   /**
    * @public
    *
    * Deletes an Object.
    *
    * @param GenericORMapperDataObject $object the object to delete
    * @return int Database id of the object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    */
   public function deleteObject(GenericORMapperDataObject $object) {

      // Get information about object to load
      $objectName = $object->getObjectName();
      $objectID = $this->mappingTable[$objectName]['ID'];
      $ID = $object->getProperty($objectID);

      // Build query
      $delete = 'DELETE FROM `' . $this->mappingTable[$objectName]['Table'] . '`';
      $delete .= ' WHERE `' . $objectID . '` = \'' . $ID . '\';';

      $this->dbDriver->executeTextStatement($delete, $this->logStatements);

      return $ID;

   }

   /**
    * @public
    *
    * Saves an Object.
    *
    * @param GenericORMapperDataObject $object the object to save.
    * @return int Database id of the object.
    * @throws GenericORMapperException In case the object name cannot be found within the mapping table.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 26.10.2008 (Added a check, if the desired object name exists in the mapping table.)<br />
    * Version 0.3, 27.12.2008 (Update is now done, if params are located in the params array)<br />
    * Version 0.4, 02.01.2010 (Added ticks for property names to avoid key word issues)<br />
    * Version 0.5, 08.01.2010 (Added property value escaping in order to avoid sql injections)<br />
    * Version 0.6, 15.01.2011 (Added event handler calls)<br />
    * Version 0.7, 15.02.2011 (Moved eventhandler calls to GORM-function, because afterSave() was called before whole tree was saved)<br />
    */
   public function saveObject(GenericORMapperDataObject &$object) {

      // get information about object to load
      $objectName = $object->getObjectName();

      if (!isset($this->mappingTable[$objectName])) {
         throw new GenericORMapperException('[GenericORMapper::saveObject()] The object name "'
               . $objectName . '" does not exist in the mapping table! Hence, your object cannot be saved! '
               . 'Please check your object configuration.', E_USER_ERROR);
      }
      $pkName = $this->mappingTable[$objectName]['ID'];
      $attrExceptions = array(
         $pkName,
         'ModificationTimestamp',
         'CreationTimestamp'
      );


      // check if object must be saved or updated
      $id = $object->getProperty($pkName);
      if ($id === null) {

         // do an INSERT
         $insert = 'INSERT INTO ' . $this->mappingTable[$objectName]['Table'];

         $names = array();
         $values = array();
         foreach ($object->getProperties() as $propertyName => $propertyValue) {

            if (!in_array($propertyName, $attrExceptions)) {

               // Surround property names with ticks to avoid issues with reserved names!
               $names[] = '`' . $propertyName . '`';

               // escape value to avoid SQL injections
               $propertyValue = $this->dbDriver->escapeValue($propertyValue);

               // Check, whether the desired property is a BIT field. If yes, prepend with
               // the binary marker! Details can be read about under
               // http://forum.adventure-php-framework.org/viewtopic.php?f=8&t=234.
               if (stripos($this->mappingTable[$objectName][$propertyName], self::$BIT_FIELD_IDENTIFIER) === false) {

                  // check, whether the field is a null value and translate PHP null values into
                  // MySQL NULL value
                  if (stripos($this->mappingTable[$objectName][$propertyName], self::$NULL_FIELD_IDENTIFIER) === false) {
                     $values[] = '\'' . $propertyValue . '\'';
                  } else {
                     if (empty($propertyValue)) {
                        $values[] = 'NULL';
                     } else {
                        $values[] = '\'' . $propertyValue . '\'';
                     }
                  }

               } else {
                  $values[] = 'b\'' . $propertyValue . '\'';
               }

            }

         }

         $insert .= ' (' . implode(', ', $names) . ')';
         $insert .= ' VALUES (' . implode(', ', $values) . ');';

         $this->dbDriver->executeTextStatement($insert, $this->logStatements);
         $id = $this->dbDriver->getLastID();

      } else {

         // UPDATE object in database
         $update = 'UPDATE ' . $this->mappingTable[$objectName]['Table'];

         $queryParams = array();
         foreach ($object->getProperties() as $propertyName => $propertyValue) {

            if (!in_array($propertyName, $attrExceptions)) {

               // escape value to avoid SQL injections
               $propertyValue = $this->dbDriver->escapeValue($propertyValue);

               // Check, whether the desired property is a BIT field. If yes, prepend with
               // the binary marker! Details can be read about under
               // http://forum.adventure-php-framework.org/viewtopic.php?f=8&t=234.
               if (stripos($this->mappingTable[$objectName][$propertyName], self::$BIT_FIELD_IDENTIFIER) === false) {

                  // check, whether the field is a null value and translate PHP null values into
                  // MySQL NULL value
                  if (stripos($this->mappingTable[$objectName][$propertyName], self::$NULL_FIELD_IDENTIFIER) === false) {
                     $value = '\'' . $propertyValue . '\'';
                  } else {
                     if (empty($propertyValue)) {
                        $value = 'NULL';
                     } else {
                        $value = '\'' . $propertyValue . '\'';
                     }
                  }
                  $queryParams[] = '`' . $propertyName . '` = ' . $value;

               } else {
                  $queryParams[] = '`' . $propertyName . '` = b\'' . $propertyValue . '\'';
               }

            }

         }

         $update .= ' SET ' . implode(', ', $queryParams) . ', ModificationTimestamp = NOW()';
         $update .= ' WHERE ' . $pkName . '= \'' . $id . '\';';

         // execute update, only if the update is necessary
         if (count($queryParams) > 0) {
            $this->dbDriver->executeTextStatement($update, $this->logStatements);
         }

      }

      // initialize the object id, to enable the developer to directly
      // reuse the object after saving it. (added for release 1.11)
      $object->setProperty($pkName, $id);

      // inject data component to be able to reuse the saved object loading
      // related object or create associations. (added for release 1.11)
      $object->setDataComponent($this);

      // return the database ID of the object for further usage
      return $id;

   }

   /**
    * @public
    *
    * Returns an object by name and id.
    *
    * @param string $objectName The name of the object in mapping table.
    * @param int $objectId The database id of the desired object.
    * @return GenericORMapperDataObject The desired object.
    * @throws \InvalidArgumentException In case the applied object id is not numeric.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 13.09.2010 (Added security check for the given object id)<br />
    */
   public function loadObjectByID($objectName, $objectId) {

      // check for invalid ids to avoid SQL injection
      if (!is_numeric($objectId)) {
         throw new \InvalidArgumentException('[GenericORMapper::loadObjectByID()] Given object '
               . 'id "' . $objectId . '" is not an integer. Thus object with name "' . $objectName . '" '
               . 'cannot be loaded!', E_USER_ERROR);
      }

      $query = 'SELECT * FROM `' . $this->mappingTable[$objectName]['Table'] . '`
                   WHERE `' . $this->mappingTable[$objectName]['ID'] . '` = \'' . $objectId . '\';';
      $result = $this->dbDriver->executeTextStatement($query, $this->logStatements);

      return $this->mapResult2DomainObject($objectName, $this->dbDriver->fetchData($result));

   }

   /**
    * @protected
    *
    * Loads an object list by a statement resource.
    *
    * @param string $objectName name of the object in mapping table
    * @param resource $stmtResult sql statement result
    * @return GenericORMapperDataObject[] The desired object list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    */
   protected function loadObjectListByStatementResult($objectName, $stmtResult) {

      $objectList = array();
      while ($data = $this->dbDriver->fetchData($stmtResult)) {
         $objectList[] = $this->mapResult2DomainObject($objectName, $data);
      }

      return $objectList;

   }

   /**
    * @protected
    *
    * Creates an domain object by name and properties.
    *
    * @param string $objectName Name of the object in mapping table.
    * @param array $properties Properties of the object.
    * @return GenericORMapperDataObject The desired object or null.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 30.05.2008 (Now returns null, if no properties are available)<br />
    * Version 0.3, 15.06.2008 (Now uses the constructor of GenericDomainObject to set the object name)<br />
    * Version 0.4, 15.01.2011 (Added support for own domain objects and event handler)<br />
    */
   protected function mapResult2DomainObject($objectName, $properties) {

      if ($properties !== false) {

         // create service object if needed
         if (isset($this->domainObjectsTable[$objectName])) {
            $class = $this->domainObjectsTable[$objectName]['Class'];
            $object = new $class($objectName);
         } else {
            $object = new GenericDomainObject($objectName);
         }

         // set data component and object name
         $object->setDataComponent($this);

         // map properties into object
         foreach ($properties as $propertyName => $propertyValue) {

            // re-map empty values for null fields to PHP null values
            if (isset($this->mappingTable[$objectName][$propertyName])
                  && stripos($this->mappingTable[$objectName][$propertyName], self::$NULL_FIELD_IDENTIFIER) !== false
                  && empty($propertyValue)
            ) {
               $propertyValue = null;
            }

            $object->setProperty($propertyName, $propertyValue);

         }

         // call event handler
         $object->afterLoad();

      } else {
         $object = null;
      }

      return $object;

   }

}
