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
import('modules::genericormapper::data', 'GenericORMapper');

/**
 * @package modules::genericormapper::data
 * @class GenericORRelationMapper
 *
 * Implements the or data mapper, that handles objects and theire relations. Please create
 * this component using the <em>GenericORRelationMapperFactory</em> or the DIServiceManager
 * as described under <a href="http://wiki.adventure-php-framework.org/de/Erzeugen_des_GORM_mit_dem_DIServiceManager">wiki.adventure-php-framework.org</a>.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.05.2008<br />
 * Version 0.2, 15.06.2008 (Added ` to the statements due to relation saving bug)<br />
 * Version 0.3, 21.10.2008 (Improved some of the error messages)<br />
 * Version 0.4, 25.10.2008 (Added the loadNotRelatedObjects() method)<br />
 * Version 0.5, 27.04.2011 (Sourced out criterion statement creation into an
 *                          extra method and used uniqid)<br />
 */
class GenericORRelationMapper extends GenericORMapper {
   const RELATION_SOURCE = 'rel_source';
   const RELATION_TARGET = 'rel_target';

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
   public function loadObjectListByCriterion($objectName, GenericCriterionObject $criterion) {

      if ($criterion === null) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadObjectListByCriterion()] '
                                            . 'No criterion object given as second argument! Please consult the manual.',
            E_USER_ERROR);
      }
      return $this->loadObjectListByTextStatement($objectName, $this->buildSelectStatementByCriterion($objectName, $criterion));
   }

   /**
    * @public
    *
    * Load an object by a given criterion object.
    *
    * @param string $objectName The name of the desired objects.
    * @param GenericCriterionObject $criterion The selection criterion.
    * @return GenericORMapperDataObject The desired domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.06.2008<br />
    * Version 0.2, 17.01.2009 (Added a check, if the criterion object is present. Otherwise return null.)<br />
    */
   public function loadObjectByCriterion($objectName, GenericCriterionObject $criterion) {

      if ($criterion === null) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadObjectByCriterion()] '
                                            . 'No criterion object given as second argument! Please consult the manual.');
      }
      return $this->loadObjectByTextStatement($objectName, $this->buildSelectStatementByCriterion($objectName, $criterion));
   }
   
   /**
    * @public
    *
    * Loads an Hierarchical Object List
    *
    * @param string $objectName The name of the desired object
    * @param string $compositionName The name of the relation
    * @param GenericCriterionObject $criterion The selection criterion
    * @param int $rootObjectId The ID of the root item of the tree
    * @param int $maxDepth The maximum depth of the tree
    * @return TreeItem|TreeItem[]
    * 
    * @author Nicolas Pecher
    * @version 
    * Version 0.1. 23.04.2012
    */
   public function loadObjectTree($objectName, $compositionName, GenericCriterionObject $criterion = null, $rootObjectId = 0, $maxDepth = 0) {
       
       // get the objects which sould be used for the tree
       $treeItems = $this->loadTreeItemList($objectName, $criterion); 
       
       // get compositions
       $compositionTable = $this->relationTable[$compositionName]['Table'];
       $sql = 'SELECT * FROM `' . $compositionTable . '`';
       $resultCursor = $this->dbDriver->executeTextStatement($sql, $this->logStatements);       
       $compositions = array();
       while ($row = $this->dbDriver->fetchData($resultCursor)) {
           $compositions[] = $row;
       }   
       
       // we have to find the root-object(s) of the tree       
       $objectTree = array();
       foreach ($treeItems as $treeItem) {
           $isRootObject = true;
           if ($rootObjectId <= 0) {
               foreach ($compositions as $composition) {                   
                   if ($treeItem->getObjectId() === $composition[$this->relationTable[$compositionName]['TargetID']]) {
                       $isRootObject = false;
                       break;
                   }
               }
           } elseif ($rootObjectId > 0 && $treeItem->getObjectId() !== $rootObjectId) {
               $isRootObject = false;        
           }
           
           if ($isRootObject === true) {
           
               // now we load all children of the actual treeItem
               $childObjects = $this->loadChildTreeItems(
                   $treeItems, 
                   $compositions, 
                   $compositionName, 
                   $treeItem, 
                   $maxDepth
               );
               $treeItem->addChildren($childObjects);
               
               if ($rootObjectId > 0) {
                   return $treeItem;
               }
               
               $objectTree[] = $treeItem;
           }                                    
       }
       return $objectTree; 
   }
   
   /**
    * @protected
    *
    * Loads a list of TreeItems
    *
    * @param string $objectName The name of the objects which shoud be used to build up the tree
    * @param GenericCriterionObject $criterion 
    * @return TreeItem[] A list of TreeItems
    *
    * @author Nicolas Pecher
    * @version 
    * Version 0.1. 23.04.2012
    */
   protected function loadTreeItemList($objectName, GenericCriterionObject $criterion = null) {
   
       // check if the domain object is a subclass of TreeObject     
       import($this->domainObjectsTable[$objectName]['Namespace'], $this->domainObjectsTable[$objectName]['Class']);
       $object = new $this->domainObjectsTable[$objectName]['Class']($objectName); 
       if (!is_subclass_of($object, 'TreeItem') && get_class($object) !== 'TreeItem') {
           throw new GenericORMapperException(
               '[GenericORRelationMapper::loadTreeObjectList()] The object named "'
               . $objectName . '" must be a subclass of "TreeItem".', 
               E_USER_ERROR
           );    
       }
       
       if ($criterion === null) {
           return $this->loadObjectList($objectName);                             
       } 
           
       return $this->loadObjectListByCriterion($objectName, $criterion);       
   }

   /**
    * @protected
    *
    * @param array $treeItems
    * @param array $compositions  
    * @param string $compositionName The name of the relation
    * @param TreeItem $parentObject The parent TreeItem
    * @param int $maxDepth The maximum depth of the tree
    * @param int $depth The actual depth of the tree
    * @return TreeItem[] A list of tree items of the actual node
    *
    * @author Nicolas Pecher
    * @version 
    * Version 0.1. 23.04.2012
    */   
   protected function loadChildTreeItems($treeItems, $compositions, $compositionName, TreeItem $parentItem, $maxDepth, $depth = 0) {
       $layer = array();
       if ($maxDepth === 0 || $depth <= $maxDepth) {
           foreach ($treeItems as $treeItem) {
               foreach ($compositions as $composition) {
                   if ($composition[$this->relationTable[$compositionName]['TargetID']] === $treeItem->getObjectId()  &&
                       $composition[$this->relationTable[$compositionName]['SourceID']] === $parentItem->getObjectId()
                       ) {
                       $cDepth = $depth + 1;
                       $childItems = $this->loadChildTreeItems(
                           $treeItems, 
                           $compositions, 
                           $compositionName, 
                           $treeItem,
                           $maxDepth,
                           $cDepth
                       );
                       $treeItem->setParentItem($parentItem); 
                       $treeItem->addChildren($childItems);
                       $layer[] = $treeItem;
                       break;  
                   }
               }
           }    
       }
       return $layer;
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
    * Version 0.1, 26.06.2008 (Extracted from buildSelectStatementByCriterion())<br />
    * Version 0.2, 16.02.2010 (Added value escaping to avoid SQL injection)<br />
    * Version 0.3, 28.05.2010 (Bugfix: corrected where definition creation)<br />
    */
   protected function buildWhere($objectName, GenericCriterionObject $criterion) {

      $whereList = array();

      // retrieve property indicators
      $properties = $criterion->getPropertyDefinition();

      if (count($properties) > 0) {

         // add additional where statements
         foreach ($properties as $property) {

            $propertyName = $this->dbDriver->escapeValue($property['Name']);

            if (is_object($property['Value']) === TRUE) {
               $whereList[] = '(' . implode('', $this->buildWhere($objectName, $property['Value'])) . ')';
            } else {
               $propertyValue = $this->dbDriver->escapeValue($property['Value']);

               if ((substr_count($propertyValue, '%') > 0 || substr_count($propertyValue, '_') > 0) && $property['ComparisonOperator'] == '=') {
                  $whereList[] = '`' . $this->mappingTable[$objectName]['Table'] . '`.`' . $propertyName . '` LIKE \'' . $propertyValue . '\'';
               } else {
                  $whereList[] = '`' . $this->mappingTable[$objectName]['Table'] . '`.`' . $propertyName . '` ' . $property['ComparisonOperator'] . ' \'' . $propertyValue . '\'';
               }
            }

            $whereList = array(implode(' ' . $property['LogicalOperator'] . ' ', $whereList));
         }
      }

      return $whereList;
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
    * Version 0.1, 26.06.2008 (Extracted from buildSelectStatementByCriterion())<br />
    * Version 0.2, 16.02.2010 (Added value escaping to avoid SQL injection)<br />
    */
   protected function buildOrder($objectName, GenericCriterionObject $criterion) {

      // initialize return list
      $ORDER = array();

      // retrieve order indicators
      $orders = $criterion->getOrderIndicators();

      if (count($orders) > 0) {

         // create order list
         foreach ($orders as $propertyName => $direction) {
            $ORDER[] = '`' . $this->mappingTable[$objectName]['Table'] . '`.`'
                       . $this->dbDriver->escapeValue($propertyName) . '` '
                       . $this->dbDriver->escapeValue($direction);
         }
      }

      return $ORDER;
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
    * Version 0.1, 26.06.2008 (Extracted from buildSelectStatementByCriterion())<br />
    */
   protected function buildProperties($objectName, GenericCriterionObject $criterion) {

      // check for valid object definition
      if (!isset($this->mappingTable[$objectName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::buildProperties()] No '
                                            . 'object with name \'' . $objectName . '\' was found within the mapping table. '
                                            . 'Please double check your mapping configuration file or refresh the mapping table!');
      }

      // retrieve object properties to load
      $objectProperties = $criterion->getLoadedProperties();
      if (count($objectProperties) > 0) {

         $propertyList = array();

         foreach ($objectProperties as $objectProperty) {
            $propertyList[] = '`' . $this->mappingTable[$objectName]['Table'] . '`.`' . $objectProperty . '`';
         }

         $properties = implode(', ', $propertyList);
      } else {
         $properties = '`' . $this->mappingTable[$objectName]['Table'] . '`.*';
      }

      return $properties;
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 17.05.2008<br />
    * Version 0.2, 21.06.2008 (Code completed)<br />
    * Version 0.3, 25.06.2008 (Added LIKE-Feature. If the property indicator contains a '%' or '_', the resulting statement contains a LIKE clause instead of a = clause)<br />
    * Version 0.4, 24.03.2011 (Added support for relations between the same table)<br />
    * Verison 0.5, 27.04.2011 (Sourced out criterion statement creation into an extra method)<br />
    */
   protected function buildSelectStatementByCriterion($objectName, GenericCriterionObject $criterion) {

      // generate relation joins
      $joinList = $this->buildJoinStatementsByCriterion($objectName, $criterion);
      $whereList = $this->buildWhereStatementsByCriterion($objectName, $criterion);

      // build statement
      $select = 'SELECT ' . ($this->buildProperties($objectName, $criterion)) . ' FROM `' . $this->mappingTable[$objectName]['Table'] . '` ';

      if (count($joinList) > 0) {
         $select .= ' ' . implode(' ', $joinList);
      }

      $whereList = array_merge($whereList, $this->buildWhere($objectName, $criterion));
      if (count($whereList) > 0) {
         $select .= ' WHERE ' . implode(' AND ', $whereList);
      }

      $order = $this->buildOrder($objectName, $criterion);
      if (count($order) > 0) {
         $select .= ' ORDER BY ' . implode(', ', $order);
      }

      $limit = $criterion->getLimitDefinition();
      if (count($limit) > 0) {
         $select .= ' LIMIT ' . implode(',', $limit);
      }

      return $select;
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
   public function loadRelatedObject(GenericORMapperDataObject &$object, $relationName, GenericCriterionObject $criterion = null) {
      // create an empty criterion if the argument was null
      if ($criterion === null) {
         $criterion = new GenericCriterionObject();
      }
      $criterion->addCountIndicator(1);
      $objectList = $this->loadRelatedObjects($object, $relationName, $criterion);

      if (count($objectList) === 1) {
         return $objectList[0];
      }
      return null;
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 14.05.2008<br />
    * Version 0.2, 18.05.2008<br />
    * Version 0.3, 08.06.2008 (Bugfix to the statement)<br />
    * Version 0.4, 25.06.2008 (Added a third parameter to have influence on the loaded list)<br />
    * Version 0.4, 26.06.2008 (Some changes to the statement creation)<br />
    * Version 0.5, 25.10.2008 (Added the additional relation option via the criterion object)<br />
    * Version 0.6, 29.12.2008 (Added check, if given object is null)<br />
    * Version 0.7, 24.03.2011 (Added support for relations between the same table)<br />
    * Version 0.8, 27.04.2011 (Sourced out criterion statement creation into an extra method)<br />
    */
   public function loadRelatedObjects(GenericORMapperDataObject &$object, $relationName, GenericCriterionObject $criterion = null) {

      // check if object is present
      if ($object === null) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadRelatedObjects()] '
                                            . 'The given object is null. Perhaps the object does not exist in database any '
                                            . 'more. Please check your implementation!');
      }

      // gather information about the objects related to each other
      $objectName = $object->getObjectName();
      $sourceObject = $this->mappingTable[$objectName];

      // check for null relations to prevent "undefined index" errors.
      $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);
      if ($targetObjectName === null) {
         throw new GenericORMapperException(
            '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "' . $relationName
            . '" found for object "' . $objectName . '"! Please re-check your relation configuration.',
            E_USER_ERROR
         );
      }

      // BUG-142: wrong spelling of source and target object must result in descriptive error!
      if (!isset($this->mappingTable[$targetObjectName])) {
         throw new GenericORMapperException(
            '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "'
            . $targetObjectName . '" found in releation definition "' . $relationName
            . '"! Please re-check your relation configuration.',
            E_USER_ERROR
         );
      }
      $targetObject = $this->mappingTable[$targetObjectName];

      // create an empty criterion if the argument was null
      if ($criterion === null) {
         $criterion = new GenericCriterionObject();
      }

      $relationSourceObjectId = $this->getRelationIdColumn($objectName, $relationName, self::RELATION_SOURCE);
      $relationTargetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      // get 'source' and 'target' uniqid's from criterion
      $uniqueRelationSourceId = $criterion->getUniqueRelationId($relationName, true);
      $uniqueRelationTargetId = $criterion->getUniqueRelationId($relationName, false);

      // build statement
      $select = 'SELECT ' . ($this->buildProperties($targetObjectName, $criterion)) . ' FROM `' . $targetObject['Table'] . '`';

      // JOIN
      $relationTable = $this->relationTable[$relationName]['Table'];

      $select .= 'INNER JOIN `' . $relationTable . '` AS `' . $uniqueRelationSourceId . '_' . $relationTable . '` ON `' . $targetObject['Table'] . '`.`' . $targetObject['ID'] . '` = `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationTargetObjectId . '`';
      $select .= ' INNER JOIN `' . $sourceObject['Table'] . '` AS `' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '` ON `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationSourceObjectId . '` = `' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '`';

      // - add relation joins
      $joinList = $this->buildJoinStatementsByCriterion($targetObjectName, $criterion);
      $whereList = $this->buildWhereStatementsByCriterion($targetObjectName, $criterion);
      $relations = $criterion->getRelations();

      if (count($joinList) > 0) {
         $select .= implode(' ', $joinList);
      }

      // add where statement
      $where = array_merge($whereList, $this->buildWhere($targetObjectName, $criterion));
      $where[] = '`' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '` = \'' . $object->getObjectId() . '\'';
      $select .= ' WHERE ' . implode(' AND ', $where);

      // add order clause
      $order = $this->buildOrder($targetObjectName, $criterion);
      if (count($order) > 0) {
         $select .= ' ORDER BY ' . implode(', ', $order);
      }

      // add limit expression
      $limit = $criterion->getLimitDefinition();
      if (count($limit) > 0) {
         $select .= ' LIMIT ' . implode(',', $limit);
      }

      // load target object list
      return $this->loadObjectListByTextStatement($targetObjectName, $select);
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 23.10.2008<br />
    * Version 0.2, 25.10.2008 (Added additional where and relation clauses. Bugfix to the inner relation statement.)<br />
    * Version 0.3, 29.12.2008 (Added check, if given object is null)<br />
    * Version 0.4, 24.03.2011 (Added support for relations between the same table)<br />
    * Version 0.5, 27.04.2011 (Sourced out criterion statement creation into an extra method)<br />
    */
   public function loadNotRelatedObjects(GenericORMapperDataObject &$object, $relationName, GenericCriterionObject $criterion = null) {

      // check if object is present
      if ($object === null) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadNotRelatedObjects()] '
                                            . 'The given object is null. Perhaps the object does not exist in database any '
                                            . 'more. Please check your implementation!');
      }

      // gather information about the objects *not* related to each other
      $objectName = $object->getObjectName();
      $sourceObject = $this->mappingTable[$objectName];
      $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);

      // check for null relations to prevent "undefined index" errors.
      if ($targetObjectName === null) {
         throw new GenericORMapperException(
            '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "' . $relationName
            . '" found for object "' . $objectName . '"! Please re-check your relation configuration.',
            E_USER_ERROR
         );
      }

      $targetObject = $this->mappingTable[$targetObjectName];

      // create an empty criterion if the argument was null
      if ($criterion === null) {
         $criterion = new GenericCriterionObject();
      }

      // build statement
      $select = 'SELECT ' . ($this->buildProperties($targetObjectName, $criterion)) . ' FROM `' . $targetObject['Table'] . '` ';

      // For the *not* related objects we have to add the join condition
      // a little bit different (target object name instead of object name)!
      $joinList = $this->buildJoinStatementsByCriterion($targetObjectName, $criterion);
      $whereList = $this->buildWhereStatementsByCriterion($targetObjectName, $criterion);

      if (count($joinList) > 0) {
         $select .= implode(' ', $joinList);
      }

      // add where clause
      $where = array_merge($whereList, $this->buildWhere($targetObjectName, $criterion));
      $where[] = '`' . $targetObject['Table'] . '`.`' . $targetObject['ID'] . '` NOT IN ( ';
      $select .= ' WHERE ' . implode(' AND ', $where);

      $relationSourceObjectId = $this->getRelationIdColumn($objectName, $relationName, self::RELATION_SOURCE);
      $relationTargetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      //get 'source' and 'target' uniqid's from criterion
      $uniqueRelationSourceId = $criterion->getUniqueRelationId($relationName, true);
      $uniqueRelationTargetId = $criterion->getUniqueRelationId($relationName, false);

      // inner select
      $select .= ' SELECT `' . $targetObject['Table'] . '`.`' . $targetObject['ID'] . '` FROM `' . $targetObject['Table'] . '`';

      // inner inner join to the target object
      $relationTable = $this->relationTable[$relationName]['Table'];

      $select .= ' INNER JOIN `' . $relationTable . '` AS `' . $uniqueRelationSourceId . '_' . $relationTable . '` ON `' . $targetObject['Table'] . '`.`' . $targetObject['ID'] . '` = `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationTargetObjectId . '`
                      INNER JOIN `' . $sourceObject['Table'] . '` AS `' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '` ON `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationSourceObjectId . '` = `' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '`';

      // add inner where
      $select .= ' WHERE `' . $uniqueRelationTargetId . '_' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '` = \'' . $object->getObjectId() . '\'';

      // indicate end of inner statement
      $select .= ' )';

      // add order clause
      $order = $this->buildOrder($targetObjectName, $criterion);
      if (count($order) > 0) {
         $select .= ' ORDER BY ' . implode(', ', $order);
      }

      // add limit definition
      $limit = $criterion->getLimitDefinition();
      if (count($limit) > 0) {
         $select .= ' LIMIT ' . implode(',', $limit);
      }

      // load target object list
      return $this->loadObjectListByTextStatement($targetObjectName, $select);
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 16.12.2008<br />
    * Version 0.2, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function loadRelationMultiplicity(GenericORMapperDataObject &$object, $relationName) {

      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadRelationMultiplicity()] '
                                            . 'Relation "' . $relationName . '" does not exist in relation table! Hence, the '
                                            . 'relation multiplicity cannot be loaded! Please check your relation configuration.');
      }

      // gather information about the object and the relation
      $objectName = $object->getObjectName();
      $sourceObject = $this->mappingTable[$objectName];
      $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);
      $targetObject = $this->mappingTable[$targetObjectName];

      // load multiplicity
      $relationTable = $this->relationTable[$relationName];
      if ($relationTable['SourceObject'] === $objectName) {
         $select = 'SELECT COUNT(`Target_' . $targetObject['ID'] . '`) AS multiplicity FROM `' . $relationTable['Table'] . '`
                        WHERE `Source_' . $sourceObject['ID'] . '` = \'' . $object->getObjectId() . '\';';
      } else {
         $select = 'SELECT COUNT(`Source_' . $targetObject['ID'] . '`) AS multiplicity FROM `' . $relationTable['Table'] . '`
                        WHERE `Target_' . $sourceObject['ID'] . '` = \'' . $object->getObjectId() . '\';';
      }
      $result = $this->dbDriver->executeTextStatement($select, $this->logStatements);
      $data = $this->dbDriver->fetchData($result);

      // bug 453: explicitly cast to integer to avoid type save check errors.
      return intval($data['multiplicity']);
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 14.05.2008<br />
    * Version 0.2, 18.05.2008 (Function completed)<br />
    * Version 0.3, 15.06.2008 (Fixed bug that lead to wrong association saving)<br />
    * Version 0.4, 15.06.2008 (Fixed bug that relation was not found due to twisted columns)<br />
    * Version 0.5, 26.10.2008 (Added a check for the object/relation to exist in the object/relation table)<br />
    * Version 0.6, 15.02.2011 (Moved event handler calls from parent function to this one, because afterSave() was called before whole tree was saved)<br />
    * Version 0.7, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function saveObject(GenericORMapperDataObject &$object, $saveEntireTree = true) {

      // inject o/r mapper into the object to be able
      // to execute custom operations (e.g. create/remove relations)!
      $object->setDataComponent($this);

      // call event handler
      $object->beforeSave();

      // save the current object (uses parent function with no resolving for relations)
      $id = parent::saveObject($object);

      // in case the user likes to only save this object, the id is returned for further usage.
      if ($saveEntireTree === false) {
         // call event handler
         $object->afterSave();

         return $id;
      }

      // check if object has related objects in it
      $relatedObjects = &$object->getAllRelatedObjects();
      if (count($relatedObjects) > 0) {

         foreach ($relatedObjects as $relationKey => $DUMMY) {

            // save objects itself
            $count = count($relatedObjects[$relationKey]);
            for ($i = 0; $i < $count; $i++) {

               // save object itself (recursion, because object can have further related objects!)
               $relatedObjectID = $this->saveObject($relatedObjects[$relationKey][$i]);

               // gather information about the current relation
               $objectID = $this->getRelationIdColumn($object->getObjectName(), $relationKey, self::RELATION_SOURCE);
               if (!isset($this->mappingTable[$relatedObjects[$relationKey][$i]->getObjectName()]['ID'])) {
                  throw new GenericORMapperException('[GenericORRelationMapper::saveObject()] '
                                                     . 'The object name "' . $relatedObjects[$relationKey][$i]->getObjectName()
                                                     . '" does not exist in the mapping table! Hence, your object cannot be '
                                                     . 'saved! Please check your object configuration.');
               }
               $relObjectIdPkName = $this->getRelationIdColumn($relatedObjects[$relationKey][$i]->getObjectName(), $relationKey, self::RELATION_TARGET);

               // check for relation
               if (!isset($this->relationTable[$relationKey]['Table'])) {
                  throw new GenericORMapperException('[GenericORRelationMapper::saveObject()] '
                                                     . 'Relation "' . $relationKey . '" does not exist in the relation table! '
                                                     . 'Hence, your related object cannot be saved! Please check your '
                                                     . 'relation configuration.');
               }

               // create statement
               $select = 'SELECT *
                             FROM `' . $this->relationTable[$relationKey]['Table'] . '`
                             WHERE `' . $objectID . '` = \'' . $id . '\'
                             AND `' . $relObjectIdPkName . '` = \'' . $relatedObjectID . '\';';

               $result = $this->dbDriver->executeTextStatement($select, $this->logStatements);
               $relationcount = $this->dbDriver->getNumRows($result);

               // create relation if necessary
               if ($relationcount == 0) {
                  $insert = 'INSERT INTO `' . $this->relationTable[$relationKey]['Table'] . '`
                                (`' . $relObjectIdPkName . '`,`' . $objectID . '`) VALUES (\'' . $relatedObjectID . '\',\'' . $id . '\');';
                  $this->dbDriver->executeTextStatement($insert, $this->logStatements);
               }
            }
         }
      }

      // call event handler
      $object->afterSave();

      // return object id for further usage
      return $id;
   }

   /**
    * @public
    *
    * Overwrites the deleteObject() method of the parent class. Resolves relations.
    *
    * @param GenericORMapperDataObject $object the object to delete
    * @return int Database id of the object or null.
    *
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 11.05.2008<br />
    * Version 0.2, 30.05.2008 (Completed the method's code)<br />
    * Version 0.3, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function deleteObject(GenericORMapperDataObject $object) {

      // return if given object is null
      if ($object === null) {
         return null;
      }

      // 1. get compositions, where source or target object is the current object
      $objectName = $object->getObjectName();
      $objectID = $this->mappingTable[$objectName]['ID'];
      $targetCompositions = $this->getCompositionsByObjectName($objectName, 'source');

      // 2. test, if the current object has child objects and though can't be deleted
      $targetcmpcount = count($targetCompositions);
      if ($targetcmpcount != 0) {

         for ($i = 0; $i < $targetcmpcount; $i++) {

            $select = 'SELECT * FROM `' . $targetCompositions[$i]['Table'] . '`
                          WHERE `Source_' . $objectID . '` = \'' . $object->getObjectId() . '\';';
            $result = $this->dbDriver->executeTextStatement($select, $this->logStatements);

            if ($this->dbDriver->getNumRows($result) > 0) {
               throw new GenericORMapperException('[GenericORRelationMapper::deleteObject()] '
                                                  . 'Domain object "' . $objectName . '" with id "' . $object->getObjectId()
                                                  . '" cannot be deleted, because it still has composed child objects!',
                  E_USER_WARNING);
            }
         }
      }

      // 3. check for associations and delete them
      $associations = $this->getAssociationsByObjectName($objectName);

      $asscount = count($associations);
      for ($i = 0; $i < $asscount; $i++) {
         $SourceOrTarget = 'Source';
         if ($associations[$i]['SourceObject'] !== $object->getObjectName()) {
            $SourceOrTarget = 'Target';
         }

         $delete = 'DELETE FROM `' . $associations[$i]['Table'] . '`
                       WHERE `' . $SourceOrTarget . '_' . $objectID . '` = \'' . $object->getObjectId() . '\';';
         $this->dbDriver->executeTextStatement($delete, $this->logStatements);
      }

      // 4. delete object itself
      $ID = parent::deleteObject($object);

      // 5. delete composition towards other object
      $sourceCompositions = $this->getCompositionsByObjectName($objectName, 'target');

      $sourcecmpcount = count($sourceCompositions);
      for ($i = 0; $i < $sourcecmpcount; $i++) {

         $delete = 'DELETE FROM `' . $sourceCompositions[$i]['Table'] . '`
                       WHERE `Target_' . $objectID . '` = \'' . $object->getObjectId() . '\';';
         $this->dbDriver->executeTextStatement($delete, $this->logStatements);
      }

      return $ID;
   }

   /**
    * @public
    *
    * Creates an association between two objects.
    *
    * @param string $relationName Name of the relation to create.
    * @param GenericORMapperDataObject $sourceObject Source object for the relation.
    * @param GenericORMapperDataObject $targetObject Target object for the relation.
    * @return boolean true in case everything's fine.
    * @throws GenericORMapperException In case the relation is not an association or the relation does not exist.
    *
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 30.05.2008<br />
    * Version 0.2, 31.05.2008 (Code completed)<br />
    * Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
    * Version 0.4, 24.03.2011 (Added support for relations between the same table)<br />
    * Version 0.5, 12.04.2011 (Throw a exception if sourceObject or targetObject not saved)<br />
    */
   public function createAssociation($relationName, GenericORMapperDataObject $sourceObject, GenericORMapperDataObject $targetObject) {

      // test, if sourceObject and targetObject are saved
      if ($sourceObject->getObjectId() === null || $targetObject->getObjectId() === null) {
         throw new GenericORMapperException('[GenericORRelationMapper::createAssociation()] '
                                            . 'SourceObject or targetObject not saved. Please save the objects first.',
            E_USER_WARNING);
      }

      // test, if relation exists in relation table to avoid NPEs
      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::createAssociation()] '
                                            . 'Relation with name "' . $relationName . '" is not defined in the mapping table! '
                                            . 'Please check your relation configuration.', E_USER_WARNING);
      }

      // if relation is a composition, return with error to avoid NPEs
      if ($this->relationTable[$relationName]['Type'] == 'COMPOSITION') {
         throw new GenericORMapperException('[GenericORRelationMapper::createAssociation()] '
                                            . 'Compositions cannot be created with this method! Use saveObject() on the '
                                            . 'target object to create a composition!', E_USER_WARNING);
      }

      // create association
      $sourceObjectName = $sourceObject->getObjectName();
      $sourceObjectId = $this->getRelationIdColumn($sourceObjectName, $relationName, self::RELATION_SOURCE);
      $targetObjectName = $targetObject->getObjectName();
      $targetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      $insert = 'INSERT INTO `' . $this->relationTable[$relationName]['Table'] . '`
                    (`' . $sourceObjectId . '`,`' . $targetObjectId . '`)
                    VALUES
                    (\'' . $sourceObject->getObjectId() . '\',\'' . $targetObject->getObjectId() . '\');';
      $this->dbDriver->executeTextStatement($insert, $this->logStatements);

      return true;
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 30.05.2008<br />
    * Version 0.2, 31.05.2008 (Code completed)<br />
    * Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
    * Version 0.4, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function deleteAssociation($relationName, GenericORMapperDataObject $sourceObject, GenericORMapperDataObject $targetObject) {

      // test, if relation exists in relation table
      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociation()] '
                                            . 'Relation with name "' . $relationName . '" is not defined in the mapping table! '
                                            . 'Please check your relation configuration.', E_USER_WARNING);
      }

      // if relation is a composition, return with error
      if ($this->relationTable[$relationName]['Type'] == 'COMPOSITION') {
         throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociation()] '
                                            . 'Compositions cannot be deleted! Use deleteObject() on the target object instead!',
            E_USER_WARNING);
      }

      // get association and delete it
      $sourceObjectName = $sourceObject->getObjectName();
      $sourceObjectId = $this->getRelationIdColumn($sourceObjectName, $relationName, self::RELATION_SOURCE);
      $targetObjectName = $targetObject->getObjectName();
      $targetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      $delete = 'DELETE FROM `' . $this->relationTable[$relationName]['Table'] . '`
                    WHERE
                       `' . $sourceObjectId . '` = \'' . $sourceObject->getObjectId() . '\'
                       AND
                       `' . $targetObjectId . '` = \'' . $targetObject->getObjectId() . '\';';
      $this->dbDriver->executeTextStatement($delete, $this->logStatements);

      return true;
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
    * @author Christian Achatz, Ralf Schubert, Tobias Lückel
    * @version
    * Version 0.1, 30.10.2010<br />
    * Version 0.2, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function deleteAssociations($relationName, GenericORMapperDataObject $sourceObject) {

      // test, if relation exists in relation table
      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociations()] Relation '
                                            . 'with name "' . $relationName . '" is not defined in the relation table! Please '
                                            . 'check your relation configuration.', E_USER_WARNING);
      }

      // if relation is a composition, return with error
      if ($this->relationTable[$relationName]['Type'] == 'COMPOSITION') {
         throw new GenericORMapperException('[GenericORRelationMapper::deleteAssociations()] The given '
                                            . 'relation ("' . $relationName . '") is not an association! Please check your '
                                            . 'relation configuration.', E_USER_WARNING);
      }

      $sourceObjectName = $sourceObject->getObjectName();
      $sourceObjectId = $this->getRelationIdColumn($sourceObjectName, $relationName, self::RELATION_SOURCE);

      $delete = 'DELETE FROM `' . $this->relationTable[$relationName]['Table'] . '`
                    WHERE
                       `' . $sourceObjectId . '` = \'' . $sourceObject->getObjectId() . '\';';
      $this->dbDriver->executeTextStatement($delete, $this->logStatements);
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 31.05.2008<br />
    * Version 0.2, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
    * Version 0.3, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function isAssociated($relationName, GenericORMapperDataObject $sourceObject, GenericORMapperDataObject $targetObject) {

      // test, if relation exists in relation table
      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::isAssociated()] Relation '
                                            . 'with name "' . $relationName . '" is not defined in the relation table! Please '
                                            . 'check your relation configuration.', E_USER_WARNING);
      }

      // if relation is a composition, return with error
      if ($this->relationTable[$relationName]['Type'] == 'COMPOSITION') {
         throw new GenericORMapperException('[GenericORRelationMapper::isAssociated()] The given '
                                            . 'relation ("' . $relationName . '") is not an association! Please check your '
                                            . 'relation configuration.', E_USER_WARNING);
      }

      // check for association
      $sourceObjectName = $sourceObject->getObjectName();
      $sourceObjectId = $this->getRelationIdColumn($sourceObjectName, $relationName, self::RELATION_SOURCE);
      $targetObjectName = $targetObject->getObjectName();
      $targetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      $select = 'SELECT * FROM `' . $this->relationTable[$relationName]['Table'] . '`
                    WHERE
                       `' . $sourceObjectId . '` = \'' . $sourceObject->getObjectId() . '\'
                       AND
                       `' . $targetObjectId . '` = \'' . $targetObject->getObjectId() . '\';';
      $result = $this->dbDriver->executeTextStatement($select, $this->logStatements);

      // return if objects are associated
      return ($this->dbDriver->getNumRows($result) > 0) ? true : false;
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
    * @author Christian Achatz, Tobias Lückel
    * @version
    * Version 0.1, 09.10.2010<br />
    * Version 0.2, 24.03.2011 (Added support for relations between the same table)<br />
    */
   public function isComposed($relationName, GenericORMapperDataObject $child, GenericORMapperDataObject $father) {

      // test, if relation exists in relation table
      if (!isset($this->relationTable[$relationName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] Relation '
                                            . 'with name "' . $relationName . '" is not defined in the relation table! Please '
                                            . 'check your relation configuration.', E_USER_ERROR);
      }

      // if relation is an association, return with error
      if ($this->relationTable[$relationName]['Type'] == 'ASSOCIATION') {
         throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] The given '
                                            . 'relation ("' . $relationName . '") is not a composition! Please check your '
                                            . 'relation configuration.', E_USER_ERROR);
      }

      $fatherObjectName = $father->getObjectName();
      $childObjectName = $child->getObjectName();

      // check relation configuration to have type-safe and directed results
      if ($this->relationTable[$relationName]['SourceObject'] != $fatherObjectName
          || $this->relationTable[$relationName]['TargetObject'] != $childObjectName
      ) {
         throw new GenericORMapperException('[GenericORRelationMapper::isComposed()] Given '
                                            . 'child object with name "' . $childObjectName . '" is not composable under '
                                            . 'object with name "' . $fatherObjectName . '". Hence, requesting relation state '
                                            . 'for relation with name "' . $relationName . '" invoking the applied objects is '
                                            . 'not allowed due to configuration! Please double-check your code and configuration.',
            E_USER_ERROR);
      }

      $fatherObjectId = $this->getRelationIdColumn($fatherObjectName, $relationName, self::RELATION_SOURCE);
      $childObjectId = $this->getRelationIdColumn($childObjectName, $relationName, self::RELATION_TARGET);

      $select = 'SELECT * FROM `' . $this->relationTable[$relationName]['Table'] . '`
                    WHERE
                       `' . $fatherObjectId . '` = \'' . $father->getObjectId() . '\'
                       AND
                       `' . $childObjectId . '` = \'' . $child->getObjectId() . '\';';
      $result = $this->dbDriver->executeTextStatement($select, $this->logStatements);

      // return if objects are composed
      return ($this->dbDriver->getNumRows($result) > 0) ? true : false;
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
   protected function &getAssociationsByObjectName($objectName) {

      // initialize list
      $relationList = array();

      // look for suitable relation entries
      foreach ($this->relationTable as $sectionName => $attributes) {

         // only allow associations
         if ($attributes['Type'] == 'ASSOCIATION') {

            // only add, if the current object is involved in the association
            if ($attributes['SourceObject'] === $objectName || $attributes['TargetObject'] === $objectName) {
               $relationList[] = &$this->relationTable[$sectionName];
            }
         }
      }

      return $relationList;
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
   protected function getCompositionsByObjectName($objectName, $direction = null) {

      // initialize list
      $relationList = array();

      // declare attribute to indicate the direction of the relation
      if ($direction == 'source') {
         $directionAttribute = 'SourceObject';
      } elseif ($direction == 'target') {
         $directionAttribute = 'TargetObject';
      } else {
         throw new GenericORMapperException('Direction of the composition not specified! Please '
                                            . 'use "source" or "target" as values!', E_USER_WARNING);
      }

      // look for suitable relation entries
      foreach ($this->relationTable as $sectionName => $attributes) {

         if ($attributes['Type'] == 'COMPOSITION' && $attributes[$directionAttribute] == $objectName) {
            $relationList[] = &$this->relationTable[$sectionName];
         }
      }

      return $relationList;
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
   protected function getRelatedObjectNameByRelationName($objectName, $relationName) {

      // look for suitable related object
      foreach ($this->relationTable as $sectionName => $attributes) {

         if ($sectionName == $relationName) {

            if ($attributes['SourceObject'] == $objectName) {
               return $attributes['TargetObject'];
            }

            if ($attributes['TargetObject'] == $objectName) {
               return $attributes['SourceObject'];
            }
         }
      }

      // return null to indicate, that the desired object was not found or has no relations
      return null;
   }

   /**
    * @protected
    *
    * Returns the name of the ID Column concerning the given arguments.
    *
    * @param string $objectName Name of the current object.
    * @param string $relationName Name of the desired relation.
    * @param string $startPoint The desired relation start point in case of self references.
    * @return string Name of the ID Column
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 24.03.2011<br />
    */
   protected function getRelationIdColumn($objectName, $relationName, $startPoint) {

      if (isset($this->relationTable[$relationName])) {

         // in case of self relations, the source and target column name must be
         // evaluated by the desired point type
         if ($this->relationTable[$relationName]['SourceObject'] === $this->relationTable[$relationName]['TargetObject']) {
            return $startPoint === self::RELATION_SOURCE ? $this->relationTable[$relationName]['SourceID']
                  : $this->relationTable[$relationName]['TargetID'];
         }

         // look for suitable related object
         return $this->relationTable[$relationName]['SourceObject'] == $objectName
               ? $this->relationTable[$relationName]['SourceID'] : $this->relationTable[$relationName]['TargetID'];
      }

      throw new GenericORMapperException('[GenericORRelationMapper::getRelationIdColumn()] '
                                         . 'The given relation "' . $relationName . '" is not defined within the current relation '
                                         . 'table! Please revise your configuration.');
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
    * Version 0.3, 26.10.2008 (Added "importedConfigCache")<br />
    * Version 0.4, 16.03.2010 (Added missing attributes due to bug 299)<br />
    */
   public function __sleep() {
      return array(
         'mappingTable',
         'relationTable',
         'domainObjectsTable',
         '__Context',
         '__Language',
         'serviceType',
         'isInitialized',
         'importedConfigCache',
         'connectionName',
         'logStatements',
         'configNamespace',
         'configNameAffix');
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
   public function __wakeup() {
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
   public function loadObjectCount($objectName, GenericCriterionObject $criterion = null) {

      // avoid SQL errors for invalid object names
      if (!isset($this->mappingTable[$objectName])) {
         throw new GenericORMapperException('[GenericORRelationMapper::loadObjectCount()] '
                                            . 'Object with name "' . $objectName . '" is not existent in the current mapping '
                                            . 'table. Please check your mapping configuration!', E_USER_ERROR);
      }

      // create statement
      $countColumnName = 'objectcount';
      $select = 'SELECT COUNT(`' . $this->mappingTable[$objectName]['ID'] . '`) AS '
                . $countColumnName . ' FROM `' . $this->mappingTable[$objectName]['Table'] . '`';

      // add limitations
      if ($criterion !== null) {
         $where = $this->buildWhere($objectName, $criterion);
         if (count($where) > 0) {
            $select .= ' WHERE ' . implode(' AND ', $where);
         }
      }

      // load count
      $data = $this->dbDriver->fetchData(
         $this->dbDriver->executeTextStatement($select, $this->logStatements)
      );
      return (int)$data[$countColumnName];
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
    * Version 0.2, 24.03.2011 (Added support for relations between the same table)<br />
    * Version 0.3, 27.04.2011 (BUGFIX: Removed explicit type 'GenericCriterionObject' of the parameter list, because
    *                          'loadObjectsWithRelation' and 'loadObjectsWithoutRelation' should pass null value<br />
    *                          BUGFIX: Corrected the where statement because of relations between the same table)<br />
    * Version 0.4, 21.11.2011 (BUGFIX: The CriterionObject now works with the sourceTable, not with the targetTable)<br />
    */
   private function loadObjects4RelationName($objectName, $relationName, $criterion, $relationCondition) {

      // gather information about the objects related to each other
      $sourceObject = $this->mappingTable[$objectName];

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
      if (!isset($this->mappingTable[$targetObjectName])) {
         throw new GenericORMapperException(
            '[GenericORRelationMapper::loadRelatedObjects()] No relation with name "'
            . $targetObjectName . '" found in releation definition "' . $relationName
            . '"! Please re-check your relation configuration.',
            E_USER_ERROR
         );
      }

      if ($criterion == null) {
         $criterion = new GenericCriterionObject();
      }

      $relationSourceObjectId = $this->getRelationIdColumn($objectName, $relationName, self::RELATION_SOURCE);
      $relationTargetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

      //get 'source' and 'target' uniqid's from criterion
      $uniqueRelationSourceId = $criterion->getUniqueRelationId($relationName, true);

      // build statement
      $select = 'SELECT DISTINCT ' . ($this->buildProperties($objectName, $criterion)) . ' FROM `' . $sourceObject['Table'] . '`';

      // JOIN
      $relationTable = $this->relationTable[$relationName]['Table'];

      $select .= ' LEFT OUTER JOIN `' . $relationTable . '` AS `' . $uniqueRelationSourceId . '_' . $relationTable . '` ON `' . $sourceObject['Table'] . '`.`' . $sourceObject['ID'] . '` = `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationSourceObjectId . '`';

      // - add relation joins
      $joinList = $this->buildJoinStatementsByCriterion($objectName, $criterion);
      $whereList = $this->buildWhereStatementsByCriterion($objectName, $criterion);

      if (count($joinList) > 0) {
         $select .= implode(' ', $joinList);
      }

      // add where statement
      $where = array_merge($whereList, $this->buildWhere($objectName, $criterion));

      // - add relation joins
      $where[] = '`' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationTargetObjectId . '` ' . $relationCondition;

      $select .= ' WHERE ' . implode(' AND ', $where);

      // add order clause
      $order = $this->buildOrder($objectName, $criterion);
      if (count($order) > 0) {
         $select .= ' ORDER BY ' . implode(', ', $order);
      }

      // add limit expression
      $limit = $criterion->getLimitDefinition();
      if (count($limit) > 0) {
         $select .= ' LIMIT ' . implode(',', $limit);
      }

      // load target object list
      return $this->loadObjectListByTextStatement($objectName, $select);
   }

   /**
    * @protected
    *
    * Creates JOIN statements by a given object name and criterion<br />
    *
    * @param string $objectName The given object name
    * @param GenericCriterionObject $criterion criterion object
    * @return string[] JOIN statements.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 27.04.2011<br />
    */
   protected function buildJoinStatementsByCriterion($objectName, GenericCriterionObject $criterion) {
      $joinList = array();

      $relations = $criterion->getRelations();

      foreach ($relations as $relationName => $relatedObject) {
         // gets the 'source' and 'target' uniqid from criterion to avoid conflicts with other tables
         $uniqueRelationSourceId = $criterion->getUniqueRelationId($relationName, true);
         $uniqueRelationTargetId = $criterion->getUniqueRelationId($relationName, false);
         // gather information about the object relations
         $relationTable = $this->relationTable[$relationName]['Table'];
         $fromTable = $this->mappingTable[$objectName]['Table'];
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);

         // avoid "undefined index" errors
         if ($targetObjectName === null) {
            throw new GenericORMapperException('There is no relation defined with name "' . $relationName
                                               . '" for object "' . $objectName . '"! Please re-check your criterion definition.');
         }

         $toTable = $this->mappingTable[$targetObjectName]['Table'];
         $sourceObjectId = $this->mappingTable[$objectName]['ID'];
         $targetObjectId = $this->mappingTable[$targetObjectName]['ID'];

         $relationSourceObjectId = $this->getRelationIdColumn($objectName, $relationName, self::RELATION_SOURCE);
         $relationTargetObjectId = $this->getRelationIdColumn($targetObjectName, $relationName, self::RELATION_TARGET);

         // add statement to join list
         $joinList[] = 'INNER JOIN `' . $relationTable . '` AS `' . $uniqueRelationSourceId . '_' . $relationTable . '` ON `' . $fromTable . '`.`' . $sourceObjectId . '` = `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationSourceObjectId . '`';
         $joinList[] = 'INNER JOIN `' . $toTable . '` AS `' . $uniqueRelationTargetId . '_' . $toTable . '` ON `' . $uniqueRelationSourceId . '_' . $relationTable . '`.`' . $relationTargetObjectId . '` = `' . $uniqueRelationTargetId . '_' . $toTable . '`.`' . $targetObjectId . '`';
      }
      return $joinList;
   }

   /**
    * @protected
    *
    * Creates WHERE statements by a given object name and criterion<br />
    *
    * @param string $objectName The given object name
    * @param GenericCriterionObject $criterion criterion object
    * @return string[] WHERE statements.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 27.04.2011<br />
    */
   protected function buildWhereStatementsByCriterion($objectName, GenericCriterionObject $criterion) {
      $whereList = array();

      $relations = $criterion->getRelations();

      foreach ($relations as $relationName => $relatedObject) {
         /* @var $relatedObject GenericORMapperDataObject */
         // gets the 'target' uniqid from criterion to avoid conflicts with other tables
         $uniqueRelationTargetId = $criterion->getUniqueRelationId($relationName, false);
         // gather information about the object relations
         $targetObjectName = $this->getRelatedObjectNameByRelationName($objectName, $relationName);
         $toTable = $this->mappingTable[$targetObjectName]['Table'];
         $targetObjectId = $this->mappingTable[$targetObjectName]['ID'];

         // add statement to where list
         $whereList[] = '`' . $uniqueRelationTargetId . '_' . $toTable . '`.`' . $targetObjectId . '` = ' . $relatedObject->getObjectId();
      }
      return $whereList;
   }

}

?>
