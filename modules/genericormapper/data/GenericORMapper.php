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

   import('modules::genericormapper::data','BaseMapper');
   import('modules::genericormapper::data','GenericDomainObject');
   import('modules::genericormapper::data','GenericCriterionObject');


   /**
   *  @namespace modules::genericormapper::data
   *  @class GenericORMapper
   *
   *  Implements an abstract OR mapper, that can map any objects defined in the object <br />
   *  configuration file into a domain object. The type of the object is therefore not defined <br />
   *  by it's class name, but by the "ObjectName" attribute.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.05.2008<br />
   *  Version 0.2, 15.06.2008 (Added ` to the statements due to relation saving bug)<br />
   */
   class GenericORMapper extends BaseMapper
   {

      function GenericORMapper(){
      }


      /**
      *  @public
      *
      *  Implements the interface method init() to be able to initialize the mapper with the service manager.
      *
      *  @param array $initParams list of initialization parameters
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      */
      function init($initParams){

         // call parent init method
         parent::init($initParams);

         // create mapping table if necessary
         if(count($this->__MappingTable) == 0){
            $this->__createMappingTable();
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Loads an object list by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $ObjectName name of the object in mapping table
      *  @param string $Namespace namespace of the statement
      *  @param string $StatementName name of the statement file
      *  @param array $StatementParams a list of statement parameters
      *  @return array $ObjectList the desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
      */
      function loadObjectListByStatement($ObjectName,$Namespace,$StatementName,$StatementParams = array()){
         return $this->__loadObjectListByStatementResult($ObjectName,$this->__DBDriver->executeStatement($Namespace,$StatementName,$StatementParams));
       // end function
      }


      /**
      *  @public
      *
      *  Loads an object list by a list of object ids.<br />
      *
      *  @param string $ObjectName name of the object in mapping table
      *  @param array $IDs list of object ids
      *  @return array $ObjectList the desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      */
      function loadObjectListByIDs($ObjectName,$IDs = array()){

         // initialize return list
         $Objects = array();
         $count = count($IDs);

         // load objects
         for($i = 0; $i < $count; $i++){
            $Objects[] = $this->loadObjectByID($ObjectName,$IDs[$i]);
          // end for
         }

         // return list
         return $Objects;

       // end function
      }


      /**
      *  @public
      *
      *  Loads an object list by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $objectName Name of the object in mapping table
      *  @param string $statement Sql statement
      *  @return GenericDomainObject[] The desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectListByTextStatement($objectName,$statement){
         return $this->__loadObjectListByStatementResult($objectName,$this->__DBDriver->executeTextStatement($statement,$this->__LogStatements));
       // end function
      }


      /**
      *  @public
      *
      *  Loads an object by a special statement. The statement must return the desired object properties.
      *
      *  @param string $objectName name of the object in mapping table
      *  @param string $namespace namespace of the statement
      *  @param string $statementName name of the statement file
      *  @param array $statementParams a list of statement parameters
      *  @return GenericDomainObject The desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 25.06.2008 (Added the $StatementParams parameter)<br />
      */
      function loadObjectByStatement($objectName,$namespace,$statementName,$statementParams = array()){
         $result = $this->__DBDriver->executeStatement($namespace,$statementName,$statementParams,$this->__LogStatements);
         $data = $this->__DBDriver->fetchData($result);
         return $this->__mapResult2DomainObject($objectName,$data);
       // end function
      }


      /**
      *  @public
      *
      *  Loads an object by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $objectName name of the object in mapping table
      *  @param string $statement sql statement
      *  @return GenericDomainObject The desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 25.05.2008 (Corrected the call of the executeTextStatement() method)<br />
      */
      function loadObjectByTextStatement($objectName,$statement){
         $result = $this->__DBDriver->executeTextStatement($statement,$this->__LogStatements);
         $data = $this->__DBDriver->fetchData($result);
         return $this->__mapResult2DomainObject($objectName,$data);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes an Object.<br />
      *
      *  @param object $Object the object to delete
      *  @return int $ID database id of the object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function deleteObject($Object){

         // Get information about object to load
         $ObjectName = $Object->get('ObjectName');
         $ObjectID = $this->__MappingTable[$ObjectName]['ID'];
         $ID = $Object->getProperty($ObjectID);

         // Build query
         $delete = 'DELETE FROM `'.$this->__MappingTable[$ObjectName]['Table'].'`';
         $delete .= ' WHERE `'.$ObjectID. '` = \''.$ID.'\';';

         $this->__DBDriver->executeTextStatement($delete,$this->__LogStatements);

         return $ID;

       // end function
      }


      /**
      *  @public
      *
      *  Saves an Object.<br />
      *
      *  @param object $Object the object to save
      *  @return int $ID database id of the object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 26.10.2008 (Added a check, if the desired object name exists in the mapping table.)<br />
      *  Version 0.3, 27.12.2008 (Update is now done, if params are located in the params array)<br />
      */
      function saveObject($Object){

         // Get information about object to load
         $objectName = $Object->get('ObjectName');

         if(!isset($this->__MappingTable[$objectName])){
            trigger_error('[GenericORMapper::saveObject()] The object name "'.$objectName.'" does not exist in the mapping table! Hence, your object cannot be saved! Please check your object configuration.');
            return null;
          // end if
         }
         $pkName = $this->__MappingTable[$objectName]['ID'];
         $attrExceptions = array(
                             $pkName,
                             'ModificationTimestamp',
                             'CreationTimestamp'
                            );

         // Check if object must be saved or updated
         $ID = $Object->getProperty($pkName);
         if($ID === null){

            // Do an INSERT
            $insert = 'INSERT INTO '.$this->__MappingTable[$objectName]['Table'];

            $names = array();
            $values = array();
            foreach($Object->getProperties() as $propertyName => $propertyValue){

               if(!in_array($propertyName,$attrExceptions)){
                  $names[] = $propertyName;
                  $values[] = '\''.$propertyValue.'\'';
                // end if
               }

             // end foreach
            }

            $insert .= ' ('.implode(', ',$names).')';
            $insert .= ' VALUES ('.implode(', ',$values).');';

            $this->__DBDriver->executeTextStatement($insert,$this->__LogStatements);
            $ID = $this->__DBDriver->getLastID();

          // end if
         }
         else{

            // UPDATE object in database
            $update = 'UPDATE '.$this->__MappingTable[$objectName]['Table'];

            $queryParams = array();
            foreach($Object->getProperties() as $propertyName => $propertyValue){

               if(!in_array($propertyName,$attrExceptions)){
                  $queryParams[] = '`'.$propertyName.'` = \''.$propertyValue.'\'';
                // end if
               }

             // end foreach
            }

            $update .= ' SET '.implode(', ',$queryParams).', ModificationTimestamp = NOW()';
            $update .= ' WHERE '.$pkName. '= \''.$ID.'\';';

            // execute update, only if the update is necessary
            if(count($queryParams) > 0){
               $this->__DBDriver->executeTextStatement($update,$this->__LogStatements);
             // end if
            }

          // end else
         }

         // return the database ID of the object for further usage
         return $ID;

       // end function
      }


      /**
      *  @public
      *
      *  Returns an object by name and id.<br />
      *
      *  @param string $ObjectName name of the object in mapping table
      *  @param int $ObjectID database id of the desired object
      *  @return object $Object the desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectByID($ObjectName,$ObjectID){

         // Get information about object to load
         $ObjectInfo = $this->__MappingTable[$ObjectName];

         // Load properties
         $query = 'SELECT * FROM `'.$this->__MappingTable[$ObjectName]['Table'].'`
                   WHERE `'.$this->__MappingTable[$ObjectName]['ID'].'` = \''.$ObjectID.'\';';
         $result = $this->__DBDriver->executeTextStatement($query,$this->__LogStatements);

         // Return desired object
         return $this->__mapResult2DomainObject($ObjectName,$this->__DBDriver->fetchData($result));

       // end function
      }


      /**
      *  @private
      *
      *  Loads an object list by a statemant resource.<br />
      *
      *  @param string $ObjectName name of the object in mapping table
      *  @param string $StmtResult sql statement result
      *  @return array $ObjectList the desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function __loadObjectListByStatementResult($ObjectName,$StmtResult){

         // Load list
         $ObjectList = array();
         while($data = $this->__DBDriver->fetchData($StmtResult)){
            $ObjectList[] = $this->__mapResult2DomainObject($ObjectName,$data);
          // end while
         }

         // Return list
         return $ObjectList;

       // end function
      }


      /**
      *  @private
      *
      *  Creates an domain object by name and properties.
      *
      *  @param string $ObjectName name of the object in mapping table
      *  @param array $Properties properties of the object
      *  @return object $Object the desired object or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 30.05.2008 (Now returns null, if no properties are available)<br />
      *  Version 0.3, 15.06.2008 (Now uses the constructor of GenericDomainObject to set the object name)<br />
      */
      function __mapResult2DomainObject($objectName,$properties){

         if($properties !== false){

            // create object
            $object = new GenericDomainObject($objectName);

            // set data component and object name
            $object->setByReference('DataComponent',$this);

            // map properties into object
            foreach($properties as $propertyName => $propertyValue){
               $object->setProperty($propertyName,$propertyValue);
             // end foreach
            }

          // end if
         }
         else{
            $object = null;
          // end else
         }

         // return object
         return $object;

       // end function
      }

    // end class
   }
?>