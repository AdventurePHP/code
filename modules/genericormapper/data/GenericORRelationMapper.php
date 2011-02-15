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

   import('modules::genericormapper::data','GenericORMapper');

   /**
    * @package modules::genericormapper::data
    * @class GenericORRelationMapper
    *
    * Implements the or data mapper, that handles objects and their relations .<br />
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.05.2008<br />
    * Version 0.2, 15.06.2008 (Added ` to the statements due to relation saving bug)<br />
    * Version 0.3, 21.10.2008 (Improved some of the error messages)<br />
    * Version 0.4, 25.10.2008 (Added the loadNotRelatedObjects() method)<br />
    */
   class GenericORRelationMapper extends GenericORMapper {

      /**
       * @public
       *
       * Implements the interface method init() to be able to initialize the mapper with the service manager.
       *
       * @param string[] $initParam list of initialization parameters
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       */
      public function init($initParam){
      
         // call parent init method
         parent::init($initParam);

         // create relation table if necessary
         if(count($this->__RelationTable) == 0){
            $this->__createRelationTable();
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Implements the initializer method to use the mapper with the DI service manager. This
       * method replaces the initialization using the <em>GenericORMapperFactory</em>. See
       * documentation of the <em>GenericORMapperDIConfiguration</em> class on configuration
       * definition.
       *
       * @param GenericORMapperDIConfiguration $config The DI service configuration to initialize the GORM with.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.06.2010<br />
       */
      public function initDI(GenericORMapperDIConfiguration $config){
         $this->init(array('ConfigNamespace' => $config->getConfigNamespace(),
                           'ConfigNameAffix' => $config->getConfigAffix(),
                           'ConnectionName' => $config->getConnectionName(),
                           'LogStatements' => $config->getDebugMode()
                          )
         );
      }

      /**
       * @public
       *
       * Load an object list by a given criterion object.
       *
       * @param string $objectName name of the desired objects.
       * @param GenericCriterionObject $criterion criterion object.
       * @return GenericORMapperDataObject[] List of domain objects.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.05.2008<br />
       * Version 0.2, 21.06.2008 (Sourced out statement creation into an extra method)<br />
       * Version 0.3, 17.01.2009 (Added a check, if the criterion object is present. Otherwise return null.)<br />
       */
      public function loadObjectListByCriterion($objectName,GenericCriterionObject $criterion){

         if($criterion === null){
            throw new GenericORMapperException('[GenericORRelationMapper::loadObjectListByCriterion()] '
                    .'No criterion object given as second argument! Please consult the manual.',
                    E_USER_ERROR);
         }
         return $this->loadObjectListByTextStatement($objectName,$this->__buildSelectStatementByCriterion($objectName,$criterion));

       // end function
      }

      /**
       * @public
       *
       * Load an object by a given criterion object.<br />
       *
       * @param string $objectName name of the desired objects
       * @param GenericCriterionObject $criterion criterion object
       * @return GenericORMapperDataObject[] List of domain objects.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.06.2008<br />
       * Version 0.2, 17.01.2009 (Added a check, if the criterion object is present. Otherwise return null.)<br />
       */
      public function loadObjectByCriterion($objectName,GenericCriterionObject $criterion){

         if($criterion === null){
            throw new GenericORMapperException('[GenericORRelationMapper::loadObjectByCriterion()] '
                    .'No criterion object given as second argument! Please consult the manual.');
            return null;
          // end if
         }
         return $this->loadObjectByTextStatement($objectName,$this->__buildSelectStatementByCriterion($objectName,$criterion));

       // end function
      }

      /**
       * @protected
       *
       * Creates a list of WHERE statements by a given object name and a criterion object.<br />
       *
       * @param string $objectName name of the desired objects
       * @param GenericCriterionObject $criterion criterion object
       * @return string[] List of WHERE statements.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
       * Version 0.2, 16.02.2010 (Added value escaping to avoid SQL injection)<br />
       * Version 0.3, 28.05.2010 (Bugfix: corrected where definition creation)<br />
       */
      protected function __buildWhere($objectName,GenericCriterionObject $criterion){

         $whereList = array();

         // retrieve property indicators
         $properties = $criterion->getPropertyDefinition();

         if(count($properties) > 0){

            // add additional where statements
            foreach($properties as $propertyName => $propertyValue){

               $propertyName = $this->__DBDriver->escapeValue($propertyName);
               $propertyValue = $this->__DBDriver->escapeValue($propertyValue);
               
               if(substr_count($propertyValue,'%') > 0 || substr_count($propertyValue,'_') > 0){
                  $whereList[] = '`'.$this->__MappingTable[$objectName]['Table'].'`.`'.$propertyName.'` LIKE \''.$propertyValue.'\'';
                // end if
               }
               else{
                  $whereList[] = '`'.$this->__MappingTable[$objectName]['Table'].'`.`'.$propertyName.'` = \''.$propertyValue.'\'';
                // end else
               }

             // end foreach
            }

          // end if
         }

         return $whereList;

       // end function
      }

      /**
       * @protected
       *
       * Creates a list of ORDER statements by a given object name and a criterion object.<br />
       *
       * @param string $objectName name of the desired objects
       * @param GenericCriterionObject $criterion criterion object
       * @return array List of ORDER statements.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
       * Version 0.2, 16.02.2010 (Added value escaping to avoid SQL injection)<br />
       */
      protected function __buildOrder($objectName,GenericCriterionObject $criterion){

         // initialize return list
         $ORDER = array();

         // retrieve order indicators
         $orders = $criterion->getOrderIndicators();

         if(count($orders) > 0){

            // create order list
            foreach($orders as $propertyName => $direction){
               $ORDER[] = '`'.$this->__MappingTable[$objectName]['Table'].'`.`'
                       .$this->__DBDriver->escapeValue($propertyName).'` '
                       .$this->__DBDriver->escapeValue($direction);
             // end foreach
            }

          // end if
         }

         return $ORDER;

       // end function
      }

      /**
       * @protected
       *
       * Creates a list of properties by a given object name and a criterion object.<br />
       *
       * @param string $objectName Name of the desired objects.
       * @param GenericCriterionObject $criterion Criterion object.
       * @return string List of properties.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
       */
      protected function __buildProperties($objectName,GenericCriterionObject $criterion){

         // check for valid object definition
         if(!isset($this->__MappingTable[$objectName])){
            throw new GenericORMapperException('[GenericORRelationMapper::__buildProperties()] No '
                    .'object with name \''.$objectName.'\' was found within the mapping table. '
                    .'Please double check your mapping configuration file or refresh the mapping table!');
         }

         // retrieve object properties to load
         $objectProperties = $criterion->getLoadedProperties();
         if(count($objectProperties) > 0){

            $propertyList = array();

            foreach($objectProperties as $objectProperty){
               $propertyList[] = '`'.$this->__MappingTable[$objectName]['Table'].'`.`'.$objectProperty.'`';
             // end foreach
            }

            $properties = implode(', ',$propertyList);

          // end if
         }
         else{
            $properties = '`'.$this->__MappingTable[$objectName]['Table'].'`.*';
          // end else
         }

         return $properties;

       // end function
      }

      /**
       * @protected
       *
       * Creates an SQL statement by a given object name and a criterion object.<br />
       *
       * @param string $objectName name of the desired objects
       * @param GenericCriterionObject $criterion criterion object
       * @return string SQL statement.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.05.2008<br />
       * Version 0.2, 21.06.2008 (Code completed)<br />
       * Version 0.3, 25.06.2008 (Added LIKE-Feature. If the property indicator contains a '%' or '_', the resulting statement contains a LIKE clause instead of a = clause)<br />
       */
      protected function __buildSelectStatementByCriterion($objectName,GenericCriterionObject $criterion){

         // invoke benchmarker
         $t = &Singleton::getInstance('BenchmarkTimer');
         $id = 'GenericORRelationMapper::__buildSelectStatementByCriterion()';
         $t->start($id);

         // generate relation joins
         $joinList = array();
         $whereList = array();

         $relations = $criterion->getRelations();

         if(count($relations) > 0){

            foreach($relations as $relationName => $relatedObject){

               // gather information about the object relations
               $RelationTable = $this->__RelationTable[$relationName]['Table'];
               $FromTable = $this->__MappingTable[$objectName]['Table'];
               $TargetObjectName = $this->getRelatedObjectNameByRelationName($objectName,$relationName);
               $ToTable = $this->__MappingTable[$TargetObjectName]['Table'];
               $SoureObjectID = $this->__MappingTable[$objectName]['ID'];
               $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

               // add statement to join list
               $joinList[] = 'INNER JOIN `'.$RelationTable.'` ON `'.$FromTable.'`.`'.$SoureObjectID.'` = `'.$RelationTable.'`.`'.$SoureObjectID.'`';
               $joinList[] = 'INNER JOIN `'.$ToTable.'` ON `'.$RelationTable.'`.`'.$TargetObjectID.'` = `'.$ToTable.'`.`'.$TargetObjectID.'`';

               // add statement to where list
               $whereList[] = '`'.$ToTable.'`.`'.$TargetObjectID.'` = '.$relatedObject->getProperty($TargetObjectID);

             // end foreach
            }

          // end if
         }

         // build statement
         $select = 'SELECT '.($this->__buildProperties($objectName,$criterion)).' FROM `'.$this->__MappingTable[$objectName]['Table'].'` ';

         if(count($joinList) > 0){
            $select .= ' '.implode(' ',$joinList);
          // end if
         }

         $whereList = array_merge($whereList,$this->__buildWhere($objectName,$criterion));
         if(count($whereList) > 0){
            $select .= ' WHERE '.implode(' AND ',$whereList);
          // end if
         }

         $order = $this->__buildOrder($objectName,$criterion);
         if(count($order) > 0){
            $select .= ' ORDER BY '.implode(', ',$order);
          // end if
         }

         $limit = $criterion->getLimitDefinition();
         if(count($limit) > 0){
            $select .= ' LIMIT '.implode(',',$limit);
          // end if
         }

         $t->stop($id);
         return $select;

       // end function
      }

       /**
       * @public
       *
       * Loads a related object by an object and an relation name.<br />
       *
       * @param GenericORMapperDataObject $object current object
       * @param string $relationName name of the desired relation
       * @param GenericCriterionObject $criterion criterion object
       * @return GenericORMapperDataObject related object.
       *
       * @author Tobias Lückel
       * @version
       * Version 0.1, 09.09.2010<br />
       */
      public function loadRelatedObject(GenericORMapperDataObject &$object,$relationName,GenericCriterionObject $criterion = null){
        // create an empty criterion if the argument was null
         if($criterion === null){
            $criterion = new GenericCriterionObject();
          // end if
         }
         $criterion->addCountIndicator(1);
         $objectList = $this->loadRelatedObjects($object,$relationName,$criterion);

         if(count($objectList) === 1) {
            return $objectList[0];
         }
         return null;

       // end function
      }


      /**
       * @public
       *
       * Loads a list of related objects by an object and an relation name.<br />
       *
       * @param GenericORMapperDataObject $object current object
       * @param string $relationName name of the desired relation
       * @param GenericCriterionObject $criterion criterion object
       * @return GenericORMapperDataObject[] List of the releated objects.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       * Version 0.2, 18.05.2008<br />
       * Version 0.3, 08.06.2008 (Bugfix to the statement)<br />
       * Version 0.4, 25.06.2008 (Added a third parameter to have influence on the loaded list)<br />
       * Version 0.4, 26.06.2008 (Some changes to the statement creation)<br />
       * Version 0.5, 25.10.2008 (Added the additional relation option via the criterion object)<br />
       * Version 0.6, 29.12.2008 (Added check, if given object is null)<br />
       */
      public function loadRelatedObjects(GenericORMapperDataObject &$object,$relationName,GenericCriterionObject $criterion = null){

         // check if object is present
         if($object === null){
            throw new GenericORMapperException('[GenericORRelationMapper::loadRelatedObjects()] '
                    .'The given object is null. Perhaps the object does not exist in database any '
                    .'more. Please check your implementation!');
         }

         // gather information about the objects related to each other
         $objectName = $object->getObjectName();
         $sourceObject = $this->__MappingTable[$objectName];
         
         // check for null relations to prevent "undefined index" errors.
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName,$relationName);
         if($targetObjectName === null){
            throw new GenericORMapperException(
               '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "'.$relationName
               .'" found! Please re-check your relation configuration.',
               E_USER_ERROR
            );
         }

         // BUG-142: wrong spelling of source and target object must result in descriptive error!
         if(!isset($this->__MappingTable[$targetObjectName])){
            throw new GenericORMapperException(
               '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "'
               .$targetObjectName.'" found in releation definition "'.$relationName
               .'"! Please re-check your relation configuration.',
               E_USER_ERROR
            );
         }
         $targetObject = $this->__MappingTable[$targetObjectName];

         // create an empty criterion if the argument was null
         if($criterion === null){
            $criterion = new GenericCriterionObject();
          // end if
         }

         // build statement
         $select = 'SELECT '.($this->__buildProperties($targetObjectName,$criterion)).' FROM `'.$targetObject['Table'].'`';

         // JOIN
         $select .= 'INNER JOIN `'.$this->__RelationTable[$relationName]['Table'].'` ON `'.$targetObject['Table'].'`.`'.$targetObject['ID'].'` = `'.$this->__RelationTable[$relationName]['Table'].'`.`'.$targetObject['ID'].'`
                     INNER JOIN `'.$sourceObject['Table'].'` ON `'.$this->__RelationTable[$relationName]['Table'].'`.`'.$sourceObject['ID'].'` = `'.$sourceObject['Table'].'`.`'.$sourceObject['ID'].'`';

         // - add relation joins
         $where = array();
         $joins = (string)'';
         $relations = $criterion->getRelations();
         foreach($relations as $innerRelationName => $DUMMY){

            // gather relation params
            $relationObjectName = $relations[$innerRelationName]->getObjectName();
            $relationSourceObject = $this->__MappingTable[$relationObjectName];
            $relationTargetObjectName = $this->getRelatedObjectNameByRelationName($relations[$innerRelationName]->getObjectName(),$innerRelationName);
            $relationTargetObject = $this->__MappingTable[$relationTargetObjectName];

            // finally build join
            $joins .= ' INNER JOIN `'.$this->__RelationTable[$innerRelationName]['Table'].'` ON `'.$relationTargetObject['Table'].'`.`'.$relationTargetObject['ID'].'` = `'.$this->__RelationTable[$innerRelationName]['Table'].'`.`'.$relationTargetObject['ID'].'`
                        INNER JOIN `'.$relationSourceObject['Table'].'` ON `'.$this->__RelationTable[$innerRelationName]['Table'].'`.`'.$relationSourceObject['ID'].'` = `'.$relationSourceObject['Table'].'`.`'.$relationSourceObject['ID'].'`';

            // add a where for each join
            $where[] = '`'.$relationSourceObject['Table'].'`.`'.$relationSourceObject['ID'].'` = \''.$relations[$innerRelationName]->getProperty($relationSourceObject['ID']).'\'';

          // end foreach
         }
         $select .= $joins;

         // add where statement
         $where = array_merge($where,$this->__buildWhere($targetObjectName,$criterion));
         $where[] = '`'.$sourceObject['Table'].'`.`'.$sourceObject['ID'].'` = \''.$object->getProperty($sourceObject['ID']).'\'';
         $select .= ' WHERE '.implode(' AND ',$where);

         // add order clause
         $order = $this->__buildOrder($targetObjectName,$criterion);
         if(count($order) > 0){
            $select .= ' ORDER BY '.implode(', ',$order);
          // end if
         }

         // add limit expression
         $limit = $criterion->getLimitDefinition();
         if(count($limit) > 0){
            $select .= ' LIMIT '.implode(',',$limit);
          // end if
         }

         // load target object list
         return $this->loadObjectListByTextStatement($targetObjectName,$select);

       // end function
      }

      /**
       * @public
       *
       * Loads a list of *not* related objects by an object and an relation name.
       *
       * @param GenericORMapperDataObject $object current object
       * @param string $relationName name of the desired relation
       * @param GenericCriterionObject $criterion criterion object
       * @return GenericORMapperDataObject[] List of the *not* releated objects.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.10.2008<br />
       * Version 0.2, 25.10.2008 (Added additional where and relation clauses. Bugfix to the inner relation statement.)<br />
       * Version 0.3, 29.12.2008 (Added check, if given object is null)<br />
       */
      public function loadNotRelatedObjects(GenericORMapperDataObject &$object,$relationName,GenericCriterionObject $criterion = null){

         // check if object is present
         if($object === null){
            throw new GenericORMapperException('[GenericORRelationMapper::loadNotRelatedObjects()] '
                    .'The given object is null. Perhaps the object does not exist in database any '
                    .'more. Please check your implementation!');
         }

         // gather information about the objects *not* related to each other
         $objectName = $object->getObjectName();
         $sourceObject = $this->__MappingTable[$objectName];
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName,$relationName);
         $targetObject = $this->__MappingTable[$targetObjectName];

         // create an empty criterion if the argument was null
         if($criterion === null){
            $criterion = new GenericCriterionObject();
          // end if
         }

         // build statement
         $select = 'SELECT '.($this->__buildProperties($targetObjectName,$criterion)).' FROM `'.$targetObject['Table'].'`';

         // add relation joins
         $where = array();
         $joins = (string)'';
         $relations = $criterion->getRelations();
         foreach($relations as $innerRelationName => $DUMMY){

            // gather relation params
            $relationObjectName = $relations[$innerRelationName]->getObjectName();
            $relationSourceObject = $this->__MappingTable[$relationObjectName];
            $relationTargetObjectName = $this->getRelatedObjectNameByRelationName($relations[$innerRelationName]->getObjectName(),$innerRelationName);
            $relationTargetObject = $this->__MappingTable[$relationTargetObjectName];

            // finally build join
            $joins .= ' INNER JOIN `'.$this->__RelationTable[$innerRelationName]['Table'].'` ON `'.$relationTargetObject['Table'].'`.`'.$relationTargetObject['ID'].'` = `'.$this->__RelationTable[$innerRelationName]['Table'].'`.`'.$relationTargetObject['ID'].'`
                        INNER JOIN `'.$relationSourceObject['Table'].'` ON `'.$this->__RelationTable[$innerRelationName]['Table'].'`.`'.$relationSourceObject['ID'].'` = `'.$relationSourceObject['Table'].'`.`'.$relationSourceObject['ID'].'`';

            // add a where for each join
            $where[] = '`'.$relationSourceObject['Table'].'`.`'.$relationSourceObject['ID'].'` = \''.$relations[$innerRelationName]->getProperty($relationSourceObject['ID']).'\'';

          // end foreach
         }
         $select .= $joins;

         // add where clause
         $where = array_merge($where,$this->__buildWhere($targetObjectName,$criterion));
         $where[] = '`'.$targetObject['Table'].'`.`'.$targetObject['ID'].'` NOT IN ( ';
         $select .= ' WHERE '.implode(' AND ',$where);

         // inner select
         $select .= ' SELECT `'.$targetObject['Table'].'`.`'.$targetObject['ID'].'` FROM `'.$targetObject['Table'].'`';

         // inner inner join to the target object
         $select .= ' INNER JOIN `'.$this->__RelationTable[$relationName]['Table'].'` ON `'.$targetObject['Table'].'`.`'.$targetObject['ID'].'` = `'.$this->__RelationTable[$relationName]['Table'].'`.`'.$targetObject['ID'].'`
                      INNER JOIN `'.$sourceObject['Table'].'` ON `'.$this->__RelationTable[$relationName]['Table'].'`.`'.$sourceObject['ID'].'` = `'.$sourceObject['Table'].'`.`'.$sourceObject['ID'].'`';

         // add inner where
         $select .= ' WHERE `'.$sourceObject['Table'].'`.`'.$sourceObject['ID'].'` = \''.$object->getProperty($sourceObject['ID']).'\'';

         // indicate end of inner statement
         $select .= ' )';

         // add order clause
         $order = $this->__buildOrder($targetObjectName,$criterion);
         if(count($order) > 0){
            $select .= ' ORDER BY '.implode(', ',$order);
          // end if
         }

         // add limit definition
         $limit = $criterion->getLimitDefinition();
         if(count($limit) > 0){
            $select .= ' LIMIT '.implode(',',$limit);
          // end if
         }

         // load target object list
         return $this->loadObjectListByTextStatement($targetObjectName,$select);

       // end function
      }

      /**
       * @public
       *
       * Loads the multiplicity of a relation defined by one object and the desired relation name.
       *
       * @param GenericORMapperDataObject $object current object
       * @param string $relationName relation name
       * @return int The multiplicity of the relation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.12.2008<br />
       */
      public function loadRelationMultiplicity(GenericORMapperDataObject &$object,$relationName){

         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::loadRelationMultiplicity()] '
                    .'Relation "'.$relationName.'" does not exist in relation table! Hence, the '
                    .'relation multiplicity cannot be loaded! Please check your relation configuration.');
         }

         // gather information about the object and the relation
         $objectName = $object->getObjectName();
         $sourceObject = $this->__MappingTable[$objectName];
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName,$relationName);
         $targetObject = $this->__MappingTable[$targetObjectName];

         // load multiplicity
         $relationTable = $this->__RelationTable[$relationName];
         $select = 'SELECT COUNT(`'.$targetObject['ID'].'`) AS multiplicity FROM `'.$relationTable['Table'].'`
                    WHERE `'.$sourceObject['ID'].'` = \''.$object->getProperty($sourceObject['ID']).'\';';
         $result = $this->__DBDriver->executeTextStatement($select,$this->__LogStatements);
         $data = $this->__DBDriver->fetchData($result);

         // bug 453: explicitly cast to integer to avoid type save check errors.
         return intval($data['multiplicity']);

       // end function
      }

      /**
       * @public
       *
       * Overwrites the saveObject() method of the parent class. Resolves relations.<br />
       *
       * @param GenericORMapperDataObject $object The current object.
       * @param boolean $saveEntireTree Indicates, whether the mapper saves the entire object
       *                                tree (true) or only the root node (false).
       * @return int Id of the saved object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       * Version 0.2, 18.05.2008 (Function completed)<br />
       * Version 0.3, 15.06.2008 (Fixed bug that lead to wrong association saving)<br />
       * Version 0.4, 15.06.2008 (Fixed bug that relation was not found due to twisted columns)<br />
       * Version 0.5, 26.10.2008 (Added a check for the object/relation to exist in the object/relation table)<br />
       * Version 0.6, 15.02.2011 (Moved eventhandler calls from parent function to this one, because afterSave() was called before whole tree was saved)<br />
       */
      public function saveObject(GenericORMapperDataObject &$object, $saveEntireTree = true) {
         //call event handler
         $object->beforeSave();

         // save the current object (uses parent function with no resolving for relations)
         $id = parent::saveObject($object);

         // in case the user likes to only save this object, the id is returned for further usage.
         if($saveEntireTree === false){
            return $id;
         }

         // check if object has related objects in it
         $relatedObjects = &$object->getAllRelatedObjects();
         if(count($relatedObjects) > 0){

            foreach($relatedObjects as $relationKey => $DUMMY){

               // save objects itself
               $count = count($relatedObjects[$relationKey]);
               for($i = 0; $i < $count; $i++){

                  // save object itself (recursion, because object can have further related objects!)
                  $relatedObjectID = $this->saveObject($relatedObjects[$relationKey][$i]);

                  // gather information about the current relation
                  $objectID = $this->__MappingTable[$object->getObjectName()]['ID'];
                  if(!isset($this->__MappingTable[$relatedObjects[$relationKey][$i]->getObjectName()]['ID'])){
                     throw new GenericORMapperException('[GenericORRelationMapper::saveObject()] '
                             .'The object name "'.$relatedObjects[$relationKey][$i]->getObjectName()
                             .'" does not exist in the mapping table! Hence, your object cannot be '
                             .'saved! Please check your object configuration.');
                  }
                  $relObjectIdPkName = $this->__MappingTable[$relatedObjects[$relationKey][$i]->getObjectName()]['ID'];

                  // check for relation
                  if(!isset($this->__RelationTable[$relationKey]['Table'])){
                     throw new GenericORMapperException('[GenericORRelationMapper::saveObject()] '
                             .'Relation "'.$relationKey.'" does not exist in the relation table! '
                             .'Hence, your related object cannot be saved! Please check your '
                             .'relation configuration.');
                  }

                  // create statement
                  $select = 'SELECT *
                             FROM `'.$this->__RelationTable[$relationKey]['Table'].'`
                             WHERE `'.$objectID.'` = \''.$id.'\'
                             AND `'.$relObjectIdPkName.'` = \''.$relatedObjectID.'\';';

                  $result = $this->__DBDriver->executeTextStatement($select,$this->__LogStatements);
                  $relationcount = $this->__DBDriver->getNumRows($result);

                  // create relation if necessary
                  if($relationcount == 0){
                     $insert = 'INSERT INTO `'.$this->__RelationTable[$relationKey]['Table'].'`
                                (`'.$relObjectIdPkName.'`,`'.$objectID.'`) VALUES (\''.$relatedObjectID.'\',\''.$id.'\');';
                     $this->__DBDriver->executeTextStatement($insert,$this->__LogStatements);
                   // end if
                  }

                // end for
               }

             // end foreach
            }

          // end if
         }

         // call event handler
         $object->afterSave();

         // return object id for further usage
         return $id;

       // end function
      }

      /**
       * @public
       *
       * Overwrites the deleteObject() method of the parent class. Resolves relations.
       *
       * @param GenericORMapperDataObject $object the object to delete
       * @return int Database id of the object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.05.2008<br />
       * Version 0.2, 30.05.2008 (Completed the method's code)<br />
       */
      public function deleteObject(GenericORMapperDataObject $object){

         // return if given object is null
         if($object === null){
            return null;
          // end
         }

         // 1. get compositions, where source or target object is the current object
         $objectName = $object->getObjectName();
         $objectID = $this->__MappingTable[$objectName]['ID'];
         $targetCompositions = $this->__getCompositionsByObjectName($objectName,'source');

         // 2. test, if the current object has child objects and though can't be deleted
         $targetcmpcount = count($targetCompositions);
         if($targetcmpcount != 0){

            for($i = 0; $i < $targetcmpcount; $i++){

               $select = 'SELECT * FROM `'.$targetCompositions[$i]['Table']. '`
                          WHERE `'.$objectID.'` = \''.$object->getProperty($objectID).'\';';
               $result = $this->__DBDriver->executeTextStatement($select,$this->__LogStatements);

               if($this->__DBDriver->getNumRows($result) > 0){
                  throw new GenericORMapperException('[GenericORRelationMapper::deleteObject()] '
                          .'Domain object "'.$objectName.'" with id "'.$object->getProperty($objectID)
                          .'" cannot be deleted, because it still has composed child objects!',
                          E_USER_WARNING);
               }

             // end for
            }

          // end if
         }

         // 3. check for associations and delete them
         $associations = $this->__getAssociationsByObjectName($objectName);

         $asscount = count($associations);
         for($i = 0; $i < $asscount; $i++){
            $delete = 'DELETE FROM `'.$associations[$i]['Table'].'`
                       WHERE `'.$objectID.'` = \''.$object->getProperty($objectID).'\';';
            $this->__DBDriver->executeTextStatement($delete,$this->__LogStatements);
          // end if
         }

         // 4. delete object itself
         $ID = parent::deleteObject($object);

         // 5. delete composition towards other object
         $sourceCompositions = $this->__getCompositionsByObjectName($objectName,'target');

         $sourcecmpcount = count($sourceCompositions);
         for($i = 0; $i < $sourcecmpcount; $i++){

            $delete = 'DELETE FROM `'.$sourceCompositions[$i]['Table'].'`
                       WHERE `'.$objectID.'` = \''.$object->getProperty($objectID).'\';';
            $this->__DBDriver->executeTextStatement($delete,$this->__LogStatements);

          // end for
         }

         return $ID;

       // end function
      }

      /**
       * @public
       *
       * Creates an association between two objects.<br />
       *
       * @param string $RelationName; Name ofthe relation to create
       * @param GenericORMapperDataObject $SourceObject; Source object for the relation
       * @param GenericORMapperDataObject $TargetObject; Target object for the relation
       * @return bool False (relation is not an association) or true (everything's fine)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.05.2008<br />
       * Version 0.2, 31.05.2008 (Code completed)<br />
       * Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
       */
      public function createAssociation($relationName,GenericORMapperDataObject $sourceObject,GenericORMapperDataObject $targetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::createAssociation()] '
                    .'Relation with name "'.$relationName.'" is not defined in the mapping table! '
                    .'Please check your relation configuration.',E_USER_WARNING);
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$relationName]['Type'] == 'COMPOSITION'){
            throw new GenericORMapperException('[GenericORRelationMapper::createAssociation()] '
                    .'Compositions cannot be created with this method! Use saveObject() on the '
                    .'target object to create a composition!',E_USER_WARNING);
         }

         // create association
         $SourceObjectName = $sourceObject->getObjectName();
         $SourceObjectID = $this->__MappingTable[$SourceObjectName]['ID'];
         $TargetObjectName = $targetObject->getObjectName();
         $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

         $insert = 'INSERT INTO `'.$this->__RelationTable[$relationName]['Table'].'`
                    (`'.$SourceObjectID.'`,`'.$TargetObjectID.'`)
                    VALUES
                    (\''.$sourceObject->getProperty($SourceObjectID).'\',\''.$targetObject->getProperty($TargetObjectID).'\');';
         $this->__DBDriver->executeTextStatement($insert,$this->__LogStatements);

         return true;

       // end function
      }

      /**
       * @public
       *
       * Delete an association between two objects. Due to data consistency, only associations<br />
       * can be deleted.<br />
       *
       * @param string $relationName Name ofthe relation to create
       * @param GenericORMapperDataObject $sourceObject Source object for the relation
       * @param GenericORMapperDataObject $targetObject Target object for the relation
       * @return bool True (success) or false (error).
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.05.2008<br />
       * Version 0.2, 31.05.2008 (Code completed)<br />
       * Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
       */
      public function deleteAssociation($relationName,GenericORMapperDataObject $sourceObject,GenericORMapperDataObject $targetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociation()] '
                    .'Relation with name "'.$relationName.'" is not defined in the mapping table! '
                    .'Please check your relation configuration.',E_USER_WARNING);
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$relationName]['Type'] == 'COMPOSITION'){
            throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociation()] '
                    .'Compositions cannot be deleted! Use deleteObject() on the target object instead!',
                    E_USER_WARNING);
         }

         // get association and delete it
         $SourceObjectName = $sourceObject->getObjectName();
         $SourceObjectID = $this->__MappingTable[$SourceObjectName]['ID'];
         $TargetObjectName = $targetObject->getObjectName();
         $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

         $delete = 'DELETE FROM `'.$this->__RelationTable[$relationName]['Table'].'`
                    WHERE
                       `'.$SourceObjectID.'` = \''.$sourceObject->getProperty($SourceObjectID).'\'
                       AND
                       `'.$TargetObjectID.'` = \''.$targetObject->getProperty($TargetObjectID).'\';';
         $this->__DBDriver->executeTextStatement($delete,$this->__LogStatements);

         return true;

       // end function
      }

      /**
       * @public
       *
       * This method enables you to delete all associations the given <em>$sourceObject</em>
       * has to any other object concerning the relation definition.
       * <p/>
       * Please note, that the associations cannot be restored after this operation and that
       * no exception can be defined at the moment.
       *
       * @param string $relationName The name of the relation that is uses as a selector.
       * @param GenericORMapperDataObject $sourceObject The source object that limits the deletion.
       *
       * @author Christian Achatz, Ralf Schubert
       * @version
       * Version 0.1, 30.10.2010<br />
       */
      public function deleteAssociations($relationName, GenericORMapperDataObject $sourceObject) {

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociations()] Relation '
                    .'with name "'.$relationName.'" is not defined in the relation table! Please '
                    .'check your relation configuration.',E_USER_WARNING);
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$relationName]['Type'] == 'COMPOSITION'){
            throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociations()] The given '
                    .'relation ("'.$relationName.'") is not an association! Please check your '
                    .'relation configuration.', E_USER_WARNING);
         }

         $sourceObjectName = $sourceObject->getObjectName();
         $sourceObjectID = $this->__MappingTable[$sourceObjectName]['ID'];

         $delete = 'DELETE FROM `'.$this->__RelationTable[$relationName]['Table'].'`
                    WHERE
                       `'.$sourceObjectID.'` = \''.$sourceObject->getObjectId().'\';';
         $this->__DBDriver->executeTextStatement($delete, $this->__LogStatements);
         
      }

      /**
       * @public
       *
       * Returns true if an association of the given type exists between the provided objects.
       *
       * @param string $relationName Name of the relation to select
       * @param GenericORMapperDataObject $sourceObject Source object for the relation
       * @param GenericORMapperDataObject $targetObject Target object for the relation
       * @return bool True (association exists) or false (objects are not associated).
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 31.05.2008<br />
       * Version 0.1, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
       */
      public function isAssociated($relationName,GenericORMapperDataObject $sourceObject,GenericORMapperDataObject $targetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::isAssociated()] Relation '
                    .'with name "'.$relationName.'" is not defined in the relation table! Please '
                    .'check your relation configuration.',E_USER_WARNING);
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$relationName]['Type'] == 'COMPOSITION'){
            throw new GenericORMapperException('[GenericORRelationMapper::isAssociated()] The given '
                    .'relation ("'.$relationName.'") is not an association! Please check your '
                    .'relation configuration.', E_USER_WARNING);
         }

         // check for association
         $sourceObjectName = $sourceObject->getObjectName();
         $sourceObjectID = $this->__MappingTable[$sourceObjectName]['ID'];
         $targetObjectName = $targetObject->getObjectName();
         $targetObjectID = $this->__MappingTable[$targetObjectName]['ID'];

         $select = 'SELECT * FROM `'.$this->__RelationTable[$relationName]['Table'].'`
                    WHERE
                       `'.$sourceObjectID.'` = \''.$sourceObject->getObjectId().'\'
                       AND
                       `'.$targetObjectID.'` = \''.$targetObject->getObjectId().'\';';
         $result = $this->__DBDriver->executeTextStatement($select,$this->__LogStatements);

         // return if objects are associated
         return ($this->__DBDriver->getNumRows($result) > 0) ? true : false;

       // end function
      }

      /**
       * @public
       *
       * Evaluates, whether the applied objects are connected by the given relation name. Please
       * note, that relations of type COMPOSITION are directed. This means, that the method will
       * return false, in case child and father are changed mixed.
       *
       * @param string $relationName The name of the relation between <em>$father</em> and <em>$child</em>.
       * @param GenericORMapperDataObject $child The object, that is composed under the <em>$father</em> object.
       * @param GenericORMapperDataObject $father The father object composing <em>$child</em>.
       * @return true in case <em>$father</em> composes <em>$child</em> using the given relation name or false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.10.2010<br />
       */
      public function isComposed($relationName, GenericORMapperDataObject $child, GenericORMapperDataObject $father){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$relationName])){
            throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] Relation '
                    .'with name "'.$relationName.'" is not defined in the relation table! Please '
                    .'check your relation configuration.', E_USER_ERROR);
         }

         // if relation is an association, return with error
         if($this->__RelationTable[$relationName]['Type'] == 'ASSOCIATION'){
            throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] The given '
                    .'relation ("'.$relationName.'") is not a composition! Please check your '
                    .'relation configuration.',E_USER_ERROR);
         }

         $fatherObjectName = $father->getObjectName();
         $fatherObjectId = $this->__MappingTable[$fatherObjectName]['ID'];
         $childObjectName = $child->getObjectName();
         $childObjectId = $this->__MappingTable[$childObjectName]['ID'];

         // check relation configuration to have type-safe and directed results
         if ($this->__RelationTable[$relationName]['SourceObject'] != $fatherObjectName
                 || $this->__RelationTable[$relationName]['TargetObject'] != $childObjectName) {
            throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] Given '
                    .'child object with name "'.$childObjectName.'" is not composable under '
                    .'object with name "'.$fatherObjectName.'". Hence, requesting relation state '
                    .'for relation with name "'.$relationName.'" invoking the applied objects is '
                    .'not allowed due to configuration! Please double-check your code and configuration.',
                    E_USER_ERROR);
         }

         $select = 'SELECT * FROM `'.$this->__RelationTable[$relationName]['Table'].'`
                    WHERE
                       `'.$fatherObjectId.'` = \''.$father->getObjectId().'\'
                       AND
                       `'.$childObjectId.'` = \''.$child->getObjectId().'\';';
         $result = $this->__DBDriver->executeTextStatement($select,$this->__LogStatements);

         // return if objects are associated
         return ($this->__DBDriver->getNumRows($result) > 0) ? true : false;

      }

      /**
       * @protected
       *
       * Returns all associations concerning one object.
       *
       * @param string $objectName name of the current object
       * @return string[] List of relations of the given object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.05.2008<br />
       * Version 0.2, 28.12.2008 (Bugfix: only associations are returned, where the object is involved)<br />
       */
      protected function &__getAssociationsByObjectName($objectName){

         // initialize list
         $relationList = array();

         // look for suitable relation entries
         foreach($this->__RelationTable as $sectionName => $attributes){

            // only allow associations
            if($attributes['Type'] == 'ASSOCIATION'){

               // only add, if the current object is involved in the association
               if($attributes['SourceObject'] === $objectName || $attributes['TargetObject'] === $objectName){
                  $relationList[] = &$this->__RelationTable[$sectionName];
                // end if
               }

             // end if
            }

          // end foreach
         }

         return $relationList;

       // end function
      }

      /**
       * @protected
       *
       * Returns all compositions concerning one object name.<br />
       *
       * @param string $objectName Name of the current object
       * @param string $direction Direction of the relation (legal values: source, target)
       * @return string[] List of relations of the given type.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.05.2008<br />
       */
      protected function __getCompositionsByObjectName($objectName,$direction = null){

         // initialize list
         $relationList = array();

         // declare attribute to indicate the direction of the relation
         if($direction == 'source'){
            $directionAttribute = 'SourceObject';
          // end if
         }
         elseif($direction == 'target'){
            $directionAttribute = 'TargetObject';
          // end else
         }
         else{
            throw new GenericORMapperException('Direction of the composition not specified! Please '
                    .'use "source" or "target" as values!',E_USER_WARNING);
            return $relationList;
          // end else
         }

         // look for suitable relation entries
         foreach($this->__RelationTable as $sectionName => $attributes){

            if($attributes['Type'] == 'COMPOSITION' && $attributes[$directionAttribute] == $objectName){
               $relationList[] = &$this->__RelationTable[$sectionName];
             // end if
            }

          // end foreach
         }

         return $relationList;

       // end function
      }

      /**
       * @protected
       *
       * Returns the name of the related object concerning the given arguments.<br />
       *
       * @param string $objectName Name of the current object
       * @param string $relationName Name of the desired relation
       * @return string Name of the releated object or null, in case the object definition was not found.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       */
      protected function getRelatedObjectNameByRelationName($objectName,$relationName){

         // look for suitable related object
         foreach($this->__RelationTable as $SectionName => $Attributes){

            if($SectionName == $relationName){

               if($Attributes['SourceObject'] == $objectName){
                  return $Attributes['TargetObject'];
                // end if
               }

               if($Attributes['TargetObject'] == $objectName){
                  return $Attributes['SourceObject'];
                // end if
               }

             // end if
            }

          // end foreach
         }

         // return null to indicate, that the desired object was not found or has no relations
         return null;

       // end function
      }

      /**
       * @public
       *
       * Implements php's magic __sleep() method to indicate, which class vars have to be serialized.<br />
       *
       * @return string[] List of serializable properties.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.05.2008<br />
       * Version 0.2, 25.06.2008 (Removed "ApplicationID" from sleep list)<br />
       * Version 0.3, 26.10.2008 (Added "__importedConfigCache")<br />
       * Version 0.4, 16.03.2010 (Added missing attributes due to bug 299)<br />
       */
      public function __sleep(){
         return array('__MappingTable',
            '__RelationTable',
            '__ServiceObjectsTable',
            '__Context',
            '__Language',
            '__importedConfigCache',
            '__DBConnectionName',
            '__LogStatements',
            '__ConfigNamespace',
            '__ConfigNameAffix');
      }

      /**
       * @public
       *
       * Implements the wakeup function to re-initialize the database connection after
       * de-serialization.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.03.2010<br />
       */
      public function __wakeup(){
         $this->createDatabaseConnection();
      }

      /**
       * @public
       *
       * Loads the amount of objects stored in the database. Additionally, the result can be
       * influenced by the applied criterion.
       * <p/>
       * Please note, that this method ignores relation declarations within the criterion
       * object. If you intend to load the amount of related objects, please use
       * <em>loadRelationMultiplicity()</em>!
       *
       * @param string $objectName The name of object to load.
       * @param GenericCriterionObject $criterion
       * @return int The amount of objects
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 18.02.2010<br />
       */
      public function loadObjectCount($objectName, GenericCriterionObject $criterion = null){

         // avoid SQL errors for invalid object names
         if(!isset($this->__MappingTable[$objectName])){
            throw new GenericORMapperException('[GenericORRelationMapper::loadObjectCount()] '
                    .'Object with name "'.$objectName.'" is not existent in the current mapping '
                    .'table. Please check your mapping configuration!',E_USER_ERROR);
         }

         // create statement
         $countColumnName = 'objectcount';
         $select = 'SELECT COUNT(`'.$this->__MappingTable[$objectName]['ID'].'`) AS '
                 .$countColumnName.' FROM `'.$this->__MappingTable[$objectName]['Table'].'`';

         // add limitations
         if($criterion !== null){
            $where = $this->__buildWhere($objectName,$criterion);
            if(count($where) > 0){
               $select .= ' WHERE '.implode(' AND ',$where);
            }
         }

         // load count
         $data = $this->__DBDriver->fetchData(
                 $this->__DBDriver->executeTextStatement($select,$this->__LogStatements)
         );
         return (int)$data[$countColumnName];

       // end function
      }

      /**
       * @public
       *
       * Loads a list of objects specified by the applied object type (object name as
       * noted within the configuration) and the relation it should have.
       *
       * @param string $objectName The type of the objects to load.
       * @param string $relationName The name of relation, the object should have to or not.
       * @param GenericCriterionObject $criterion An additional criterion to specify custom limitations.
       * @return GenericORMapperDataObject[] The desired list of domain objects.
       *
       * @author Tobias Lückel
       * @version
       * Version 0.1, 01.09.2010<br />
       */
      public function loadObjectsWithRelation($objectName, $relationName, GenericCriterionObject $criterion = null) {
         return $this->loadObjects4RelationName($objectName, $relationName, $criterion, 'IS NOT NULL');
      }

      /**
       * @public
       *
       * Loads a list of objects specified by the applied object type (object name as
       * noted within the configuration) and the relation it should *not* have.
       *
       * @param string $objectName The type of the objects to load.
       * @param string $relationName The name of relation, the object should have to or not.
       * @param GenericCriterionObject $criterion An additional criterion to specify custom limitations.
       * @return GenericORMapperDataObject[] The desired list of domain objects.
       *
       * @author Tobias Lückel
       * @version
       * Version 0.1, 01.09.2010<br />
       */
      public function loadObjectsWithoutRelation($objectName, $relationName, GenericCriterionObject $criterion = null) {
         return $this->loadObjects4RelationName($objectName, $relationName, $criterion, 'IS NULL');
      }

      /**
       * @private
       *
       * Loads a list of objects specified by the applied object type (object name as
       * noted within the configuration) and the relation it should have.
       * <p/>
       * Further, an indicator is applied to decide whether the object should have a
       * relation or should not.
       *
       * @param string $objectName The type of the objects to load.
       * @param string $relationName The name of relation, the object should have to or not.
       * @param GenericCriterionObject $criterion An additional criterion to specify custom limitations.
       * @param string $relationCondition The relation condition (has relation or has none).
       * @return GenericORMapperDataObject[] The desired list of domain objects.
       *
       * @author Tobias Lückel
       * @version
       * Version 0.1, 01.09.2010<br />
       */
      private function loadObjects4RelationName($objectName, $relationName, GenericCriterionObject $criterion, $relationCondition) {

         // gather information about the objects related to each other
         $sourceObject = $this->__MappingTable[$objectName];

         // check for null relations to prevent "undefined index" errors.
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);
         if ($targetObjectName === null) {
            throw new GenericORMapperException(
                    '[GenericORRelationMapper::loadObjects4RelationName()] No relation with name "'
                    . $relationName . '" found! Please re-check your relation configuration.',
                    E_USER_ERROR
            );
         }

         // BUG-142: wrong spelling of source and target object must result in descriptive error!
         if (!isset($this->__MappingTable[$targetObjectName])) {
            throw new GenericORMapperException(
                    '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "'
                    . $targetObjectName . '" found in releation definition "' . $relationName
                    . '"! Please re-check your relation configuration.',
                    E_USER_ERROR
            );
         }
         $targetObject = $this->__MappingTable[$targetObjectName];

         if ($criterion == null) {
            $criterion = new GenericCriterionObject();
         }

         // build statement
         $select = 'SELECT DISTINCT ' . ($this->__buildProperties($objectName, $criterion)) . ' FROM `' . $sourceObject['Table'] . '`';

         // JOIN
         $select .= ' LEFT OUTER JOIN `' . $this->__RelationTable[$relationName]['Table'] . '` ON `' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '` = `' . $this->__RelationTable[$relationName]['Table'] . '`.`' . $sourceObject['ID'] . '`';

         // - add relation joins
         $where = array();
         $joins = (string) '';
         $relations = $criterion->getRelations();
         foreach ($relations as $innerRelationName => $DUMMY) {
            // gather relation params
            $relationObjectName = $relations[$innerRelationName]->getObjectName();
            $relationSourceObject = $this->__MappingTable[$relationObjectName];
            $relationTargetObjectName = $this->getRelatedObjectNameByRelationName($relations[$innerRelationName]->getObjectName(), $innerRelationName);
            $relationTargetObject = $this->__MappingTable[$relationTargetObjectName];

            // finally build join
            $joins .= ' INNER JOIN `' . $this->__RelationTable[$innerRelationName]['Table'] . '` ON `' . $relationTargetObject['Table'] . '`.`' . $relationTargetObject['ID'] . '` = `' . $this->__RelationTable[$innerRelationName]['Table'] . '`.`' . $relationTargetObject['ID'] . '`
                               INNER JOIN `' . $relationSourceObject['Table'] . '` ON `' . $this->__RelationTable[$innerRelationName]['Table'] . '`.`' . $relationSourceObject['ID'] . '` = `' . $relationSourceObject['Table'] . '`.`' . $relationSourceObject['ID'] . '`';

            // add a where for each join
            $where[] = '`' . $relationSourceObject['Table'] . '`.`' . $relationSourceObject['ID'] . '` = \'' . $relations[$innerRelationName]->getProperty($relationSourceObject['ID']) . '\'';

            // end foreach
         }

         $select .= $joins;

         // add where statement
         $where = array_merge($where, $this->__buildWhere($targetObjectName, $criterion));

         // - add relation joins
         $where[] = '`' . $this->__RelationTable[$relationName]['Table'] . '`.`' . $targetObject['ID'] . '` ' . $relationCondition;

         $select .= ' WHERE ' . implode(' AND ', $where);

         // add order clause
         $order = $this->__buildOrder($targetObjectName, $criterion);
         if (count($order) > 0) {
            $select .= ' ORDER BY ' . implode(', ', $order);
            // end if
         }

         // add limit expression
         $limit = $criterion->getLimitDefinition();
         if (count($limit) > 0) {
            $select .= ' LIMIT ' . implode(',', $limit);
            // end if
         }

         // load target object list
         return $this->loadObjectListByTextStatement($objectName, $select);
      }

    // end class
   }
?>