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

   import('modules::genericormapper::data','BaseMapper');
   import('modules::genericormapper::data','GenericDomainObject');
   import('modules::genericormapper::data','GenericCriterionObject');

   /**
    * @package modules::genericormapper::data
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
       * Implements the interface method init() to be able to initialize the mapper with the service manager.
       *
       * @param array $initParam list of initialization parameters
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       */
      public function init($initParam){

         // call parent init method
         parent::init($initParam);

         // create mapping table if necessary
         if(count($this->__MappingTable) == 0){
            $this->__createMappingTable();
          // end if
         }

       // end function
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
       * @return GenericDomainObject[] The desired object list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
       */
      public function loadObjectListByStatement($objectName,$namespace,$statementName,$statementParams = array()){
         return $this->__loadObjectListByStatementResult(
            $objectName,
            $this->__DBDriver->executeStatement($namespace,$statementName,$statementParams,$this->__LogStatements)
         );
       // end function
      }

      /**
       * @public
       *
       * Loads an object list by a list of object ids.<br />
       *
       * @param string $objectName name of the object in mapping table
       * @param array $ids list of object ids
       * @return GenericDomainObject[] The desired object list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       */
      public function loadObjectListByIDs($objectName,$ids = array()){

         // initialize return list
         $objects = array();
         $count = count($ids);

         // load objects
         for($i = 0; $i < $count; $i++){
            $objects[] = $this->loadObjectByID($objectName,$ids[$i]);
          // end for
         }

         return $objects;

       // end function
      }

      /**
       * @public
       *
       * Loads an object list by a special statement. The statement must return the desired
       * object properties.
       *
       * @param string $objectName Name of the object in mapping table
       * @param string $statement Sql statement
       * @return GenericDomainObject[] The desired object list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       */
      public function loadObjectListByTextStatement($objectName,$statement){
         return $this->__loadObjectListByStatementResult($objectName,$this->__DBDriver->executeTextStatement($statement,$this->__LogStatements));
       // end function
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
       * @return GenericDomainObject The desired object
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
       */
      public function loadObjectByStatement($objectName,$namespace,$statementName,$statementParams = array()){
         $result = $this->__DBDriver->executeStatement($namespace,$statementName,$statementParams,$this->__LogStatements);
         $data = $this->__DBDriver->fetchData($result);
         return $this->__mapResult2DomainObject($objectName,$data);
       // end function
      }

      /**
       * @public
       *
       * Loads an object by a special statement. The statement must return the desired
       * object properties.
       *
       * @param string $objectName name of the object in mapping table
       * @param string $statement sql statement
       * @return GenericDomainObject The desired object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 25.05.2008 (Corrected the call of the executeTextStatement() method)<br />
       */
      public function loadObjectByTextStatement($objectName,$statement){
         $result = $this->__DBDriver->executeTextStatement($statement,$this->__LogStatements);
         $data = $this->__DBDriver->fetchData($result);
         return $this->__mapResult2DomainObject($objectName,$data);
       // end function
      }

      /**
       * @public
       *
       * Deletes an Object.
       *
       * @param GenericDomainObject $object the object to delete
       * @return int Database id of the object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       */
      public function deleteObject(GenericDomainObject $object){

         // Get information about object to load
         $objectName = $object->getObjectName();
         $objectID = $this->__MappingTable[$objectName]['ID'];
         $ID = $object->getProperty($objectID);

         // Build query
         $delete = 'DELETE FROM `'.$this->__MappingTable[$objectName]['Table'].'`';
         $delete .= ' WHERE `'.$objectID. '` = \''.$ID.'\';';

         $this->__DBDriver->executeTextStatement($delete,$this->__LogStatements);

         return $ID;

       // end function
      }

      /**
       * @public
       *
       * Saves an Object.
       *
       * @param GenericDomainObject $object the object to save.
       * @return int Database id of the object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 26.10.2008 (Added a check, if the desired object name exists in the mapping table.)<br />
       * Version 0.3, 27.12.2008 (Update is now done, if params are located in the params array)<br />
       * Version 0.4, 02.01.2010 (Added ticks for property names to avoid key word issues)<br />
       * Version 0.5, 08.01.2010 (Added property value escaping in order to avoid sql injections)<br />
       */
      public function saveObject(GenericDomainObject &$object){

         // get information about object to load
         $objectName = $object->getObjectName();

         if(!isset($this->__MappingTable[$objectName])){
            throw new GenericORMapperException('[GenericORMapper::saveObject()] The object name "'
                .$objectName.'" does not exist in the mapping table! Hence, your object cannot be saved! '
                .'Please check your object configuration.',E_USER_ERROR);
            return null;
          // end if
         }
         $pkName = $this->__MappingTable[$objectName]['ID'];
         $attrExceptions = array(
                             $pkName,
                             'ModificationTimestamp',
                             'CreationTimestamp'
                            );

         // check if object must be saved or updated
         $id = $object->getProperty($pkName);
         if($id === null){

            // do an INSERT
            $insert = 'INSERT INTO '.$this->__MappingTable[$objectName]['Table'];

            $names = array();
            $values = array();
            foreach($object->getProperties() as $propertyName => $propertyValue){

               if(!in_array($propertyName,$attrExceptions)){

                  // Surround property names with ticks to avoid issues with reserved names!
                  $names[] = '`'.$propertyName.'`';

                  // escape value to avoid SQL injections
                  $propertyValue = $this->__DBDriver->escapeValue($propertyValue);

                  // Check, whether the desired property is a BIT field. If yes, prepend with
                  // the binary marker! Details can be read about under
                  // http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=234.
                  if(stripos($this->__MappingTable[$objectName][$propertyName],self::$BIT_FIELD_IDENTIFIER) === false){

                     // check, whether the field is a null value and translate PHP null values into
                     // MySQL NULL value
                     if(stripos($this->__MappingTable[$objectName][$propertyName],self::$NULL_FIELD_IDENTIFIER) === false){
                        $values[] = '\''.$propertyValue.'\'';
                     }
                     else {
                        if(empty($propertyValue)){
                           $values[] = 'NULL';
                        }
                        else {
                           $values[] = '\''.$propertyValue.'\'';
                        }
                     }
                     
                   // end if
                  }
                  else {
                     $values[] = 'b\''.$propertyValue.'\'';
                   // end else
                  }
                  
                // end if
               }

             // end foreach
            }

            $insert .= ' ('.implode(', ',$names).')';
            $insert .= ' VALUES ('.implode(', ',$values).');';

            $this->__DBDriver->executeTextStatement($insert,$this->__LogStatements);
            $id = $this->__DBDriver->getLastID();

          // end if
         }
         else{

            // UPDATE object in database
            $update = 'UPDATE '.$this->__MappingTable[$objectName]['Table'];

            $queryParams = array();
            foreach($object->getProperties() as $propertyName => $propertyValue){

               if(!in_array($propertyName,$attrExceptions)){

                  // escape value to avoid SQL injections
                  $propertyValue = $this->__DBDriver->escapeValue($propertyValue);

                  // Check, whether the desired property is a BIT field. If yes, prepend with
                  // the binary marker! Details can be read about under
                  // http://forum.adventure-php-framework.org/de/viewtopic.php?f=8&t=234.
                  if(stripos($this->__MappingTable[$objectName][$propertyName],self::$BIT_FIELD_IDENTIFIER) === false){

                     // check, whether the field is a null value and translate PHP null values into
                     // MySQL NULL value
                     $value = (string)'';
                     if(stripos($this->__MappingTable[$objectName][$propertyName],self::$NULL_FIELD_IDENTIFIER) === false){
                        $value = '\''.$propertyValue.'\'';
                     }
                     else {
                        if(empty($propertyValue)){
                           $value = 'NULL';
                           //echo '... null!';
                        }
                        else {
                           $value = '\''.$propertyValue.'\'';
                        }
                     }
                     $queryParams[] = '`'.$propertyName.'` = '.$value;

                   // end if
                  }
                  else {
                     $queryParams[] = '`'.$propertyName.'` = b\''.$propertyValue.'\'';
                   // end else
                  }

                // end if
               }

             // end foreach
            }

            $update .= ' SET '.implode(', ',$queryParams).', ModificationTimestamp = NOW()';
            $update .= ' WHERE '.$pkName. '= \''.$id.'\';';

            // execute update, only if the update is necessary
            if(count($queryParams) > 0){
               $this->__DBDriver->executeTextStatement($update,$this->__LogStatements);
             // end if
            }

          // end else
         }

         // initialize the object id, to enable the developer to directly
         // reuse the object after saving it. (added for release 1.11)
         $object->setProperty($pkName,$id);

         // inject data component to be able to reuse the saved object loading
         // related object or create assocations. (added for release 1.11)
         $object->setDataComponent($this);

         // return the database ID of the object for further usage
         return $id;

       // end function
      }

      /**
       * @public
       *
       * Returns an object by name and id.
       *
       * @param string $objectName The name of the object in mapping table.
       * @param int $objectId The database id of the desired object.
       * @return GenericDomainObject The desired object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 13.09.2010 (Added security check for the given object id)<br />
       */
      public function loadObjectByID($objectName,$objectId){

         // check for invalid ids to avoid SQL injection
         if(!is_numeric($objectId)){
            throw new InvalidArgumentException('[GenericORMapper::loadObjectByID()] Given object '
                    .'id "'.$objectId.'" is not an integer. Thus object with name "'.$objectName.'" '
                    .'cannot be loaded!', E_USER_ERROR);
         }

         $query = 'SELECT * FROM `'.$this->__MappingTable[$objectName]['Table'].'`
                   WHERE `'.$this->__MappingTable[$objectName]['ID'].'` = \''.$objectId.'\';';
         $result = $this->__DBDriver->executeTextStatement($query,$this->__LogStatements);

         return $this->__mapResult2DomainObject($objectName,$this->__DBDriver->fetchData($result));

       // end function
      }

      /**
       * @protected
       *
       * Loads an object list by a statemant resource.<br />
       *
       * @param string $objectName name of the object in mapping table
       * @param string $stmtResult sql statement result
       * @return GenericDomainObject[] The desired object list.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       */
      protected function __loadObjectListByStatementResult($objectName,$stmtResult){

         // Load list
         $objectList = array();
         while($data = $this->__DBDriver->fetchData($stmtResult)){
            $objectList[] = $this->__mapResult2DomainObject($objectName,$data);
          // end while
         }

         return $objectList;

       // end function
      }

      /**
       * @protected
       *
       * Creates an domain object by name and properties.
       *
       * @param string $ObjectName name of the object in mapping table
       * @param array $Properties properties of the object
       * @return GenericDomainObject The desired object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 30.05.2008 (Now returns null, if no properties are available)<br />
       * Version 0.3, 15.06.2008 (Now uses the constructor of GenericDomainObject to set the object name)<br />
       */
      protected function __mapResult2DomainObject($objectName,$properties){

         if($properties !== false){

            // create object
            $object = new GenericDomainObject($objectName);

            // set data component and object name
            $object->setDataComponent($this);

            // map properties into object
            foreach($properties as $propertyName => $propertyValue){

               // re-map empty values for null fields to PHP null values
               if(isset($this->__MappingTable[$objectName][$propertyName])
                       && stripos($this->__MappingTable[$objectName][$propertyName],self::$NULL_FIELD_IDENTIFIER) !== false
                       && empty($propertyValue)){
                  $propertyValue = null;
               }

               $object->setProperty($propertyName,$propertyValue);

             // end foreach
            }

          // end if
         }
         else{
            $object = null;
          // end else
         }

         return $object;

       // end function
      }

    // end class
   }
?>