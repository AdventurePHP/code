<?php
   import('modules::usermanagement::data','umgtBaseRelationMapper');


   /**
   *  @package modules::abstractormapper::data
   *  @module umgtMapper
   *
   *  Implements an abstract OR mapper, that can map any objects defined in the<br />
   *  object configuration file into a domain object. The type of the object is therefore<br />
   *  not defined by it's class name, but by the "ObjectName" attribute.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 11.05.2008<br />
   */
   class umgtMapper extends umgtBaseRelationMapper
   {

      function umgtMapper(){
      }


      /**
      *  @module loadObjectListByStatement()
      *  @public
      *
      *  Loads an object list by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param string $Namespace; namespace of the statement
      *  @param string $StatementName; name of the statement file
      *  @return array $ObjectList; the desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectListByStatement($ObjectName,$Namespace,$StatementName){
         return $this->__loadObjectListByStatementResult($ObjectName,$this->__DBDriver->executeStatement($Namespace,$StatementName));
       // end function
      }


      /**
      *  @module loadObjectListByIDs()
      *  @public
      *
      *  Loads an object list by a list of object ids.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param array $IDs; list of object ids
      *  @return array $ObjectList; the desired object list
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
            $Objects[] =$this->loadObjectByID($ObjectName,$IDs[$i]);
          // end for
         }

         // return list
         return $Objects;

       // end function
      }


      /**
      *  @module loadObjectListByTextStatement()
      *  @public
      *
      *  Loads an object list by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param string $Statement; sql statement
      *  @return array $ObjectList; the desired object list
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectListByTextStatement($ObjectName,$Statement){
         return $this->__loadObjectListByStatementResult($ObjectName,$this->__DBDriver->executeTextStatement($Statement));
       // end function
      }


      /**
      *  @module loadObjectByStatement()
      *  @public
      *
      *  Loads an object by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param string $Namespace; namespace of the statement
      *  @param string $StatementName; name of the statement file
      *  @return object $Object; the desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectByStatement($ObjectName,$Namespace,$StatementName){

         // Execute statement
         $result = $this->__DBDriver->executeStatement($Namespace,$StatementName);
         $data = $this->__DBDriver->fetchData($result);

         // Return object
         return $this->__mapResult2DomainObject($ObjectName,$data);

       // end function
      }


      /**
      *  @module loadObjectByTextStatement()
      *  @public
      *
      *  Loads an object by a special statement. The statement must return the desired<br />
      *  object properties.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param string $Statement; sql statement
      *  @return object $Object; the desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectByTextStatement($ObjectName,$Statement){

         // Execute statement
         $result = $this->__DBDriver->executeTextStatement($Namespace,$Statement);
         $data = $this->__DBDriver->fetchData($result);

         // Return object
         return $this->__mapResult2DomainObject($ObjectName,$data);

       // end function
      }


      /**
      *  @module deleteObject()
      *  @public
      *
      *  Deletes an Object.<br />
      *
      *  @param object $Object; the object to save
      *  @return int $ID; database id of the object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function deleteObject($Object){

         // Get information about object to load
         $ObjectName = $Object->get('ObjectName');
         $IDName = $this->__MappingTable[$ObjectName]['ID'];
         $ID = $Object->getProperty($IDName);

         // Build query
         $delete = 'DELETE FROM '.$this->__MappingTable[$ObjectName]['Table'];
         $delete .= ' WHERE '.$IDName. ' = \''.$ID.'\'';

         // Execute delete
         $this->__DBDriver->executeTextStatement($delete);

         // Return the database ID of the object
         return $ID;

       // end function
      }


      /**
      *  @module saveObject()
      *  @public
      *
      *  Saves an Object.<br />
      *
      *  @param object $Object; the object to save
      *  @return int $ID; database id of the object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function saveObject($Object){

         // Get information about object to load
         $ObjectName = $Object->get('ObjectName');

         // Build subquery
         $QueryParams = array();
         $IDName = $this->__MappingTable[$ObjectName]['ID'];
         $Exceptions = array(
                             $IDName,
                             'ModificationTimestamp',
                             'CreationTimestamp'
                            );

         // Check if object must be saved or updated
         $ID = $Object->getProperty($IDName);
         if($ID === null){

            // Do an INSERT
            $insert = 'INSERT INTO '.$this->__MappingTable[$ObjectName]['Table'];

            $Names = array();
            $Values = array();
            foreach($Object->getProperties() as $PropertyName => $PropertyValue){

               if(!in_array($PropertyName,$Exceptions)){
                  $Names[] = $PropertyName;
                  $Values[] = '\''.$PropertyValue.'\'';
                // end if
               }

             // end foreach
            }

            $insert .= ' ('.implode(', ',$Names).')';
            $insert .= ' VALUES ('.implode(', ',$Values).')';

            // Execute insert
            $this->__DBDriver->executeTextStatement($insert);

            // Get ID
            $ID = $this->__DBDriver->getLastID();

          // end if
         }
         else{

            // UPDATE object in database
            $update = 'UPDATE '.$this->__MappingTable[$ObjectName]['Table'];

            foreach($Object->getProperties() as $PropertyName => $PropertyValue){

               if(!in_array($PropertyName,$Exceptions)){
                  $QueryParams[] = $PropertyName.' = \''.$PropertyValue.'\'';
                // end if
               }

             // end foreach
            }

            $update .= ' SET '.implode(', ',$QueryParams).', ModificationTimestamp = NOW()';
            $update .= 'WHERE '.$IDName. '= \''.$ID.'\'';

            // Execute update
            $result = $this->__DBDriver->executeTextStatement($update);

          // end else
         }

         // Return the database ID of the object
         return $ID;

       // end function
      }


      /**
      *  @module loadObjectByID()
      *  @public
      *
      *  Returns an object by name and id.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param int $ObjectID; database id of the desired object
      *  @return object $Object; the desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function loadObjectByID($ObjectName,$ObjectID){

         // Get information about object to load
         $ObjectInfo = $this->__MappingTable[$ObjectName];

         // Load properties
         $query = 'SELECT * FROM '.$this->__MappingTable[$ObjectName]['Table'].'
                   WHERE '.$this->__MappingTable[$ObjectName]['ID'].' = \''.$ObjectID.'\'';
         $result = $this->__DBDriver->executeTextStatement($query);

         // Return desired object
         return $this->__mapResult2DomainObject($ObjectName,$this->__DBDriver->fetchData($result));

       // end function
      }


      /**
      *  @module __loadObjectListByStatementResult()
      *  @private
      *
      *  Loads an object list by a statemant resource.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param string $StmtResult; sql statement result
      *  @return array $ObjectList; the desired object list
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
      *  @module __mapResult2DomainObject()
      *  @privat
      *
      *  Creates an domain object by name and properties.<br />
      *
      *  @param string $ObjectName; name of the object in mapping table
      *  @param array $Properties; properties of the object
      *  @return object $Object; the desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      */
      function __mapResult2DomainObject($ObjectName,$Properties){

         // Create object
         $Object = new umgtBase();

         // Set data component and object name
         $Object->setByReference('DataComponent',$this);
         $Object->set('ObjectName',$ObjectName);

         // Map properties into object
         foreach($Properties as $PropertyName => $PropertyValue){
            $Object->setProperty($PropertyName,$PropertyValue);
          // end foreach
         }

         // Return object
         return $Object;

       // end function
      }

    // end class
   }
?>