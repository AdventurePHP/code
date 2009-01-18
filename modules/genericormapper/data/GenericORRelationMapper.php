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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::genericormapper::data','GenericORMapper');


   /**
   *  @namespace modules::genericormapper::data
   *  @class GenericORRelationMapper
   *
   *  Implements the or data mapper, that handles objects and their relations .<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 14.05.2008<br />
   *  Version 0.2, 15.06.2008 (Added ` to the statements due to relation saving bug)<br />
   *  Version 0.3, 21.10.2008 (Improved some of the error messages)<br />
   *  Version 0.4, 25.10.2008 (Added the loadNotRelatedObjects() method)<br />
   */
   class GenericORRelationMapper extends GenericORMapper
   {

      function GenericORRelationMapper(){
      }


      /**
      *  @public
      *
      *  Implements the interface method init() to be able to initialize the mapper with the service manager.
      *
      *  @param array $InitParams list of initialization parameters
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      */
      function init($InitParams){

         // call parent init method
         parent::init($InitParams);

         // create relation table if necessary
         if(count($this->__RelationTable) == 0){
            $this->__createRelationTable();
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Load an object list by a given criterion object.<br />
      *
      *  @param string $objectName name of the desired objects
      *  @param GenericCriterionObject $criterion criterion object
      *  @return array $objectList list of domain objects
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.05.2008<br />
      *  Version 0.2, 21.06.2008 (Sourced out statement creation into an extra method)<br />
      *  Version 0.3, 17.01.2009 (Added a check, if the criterion object is present. Otherwise return null.)<br />
      */
      function loadObjectListByCriterion($objectName,$criterion = null){

         if($criterion === null){
            trigger_error('[GenericORRelationMapper::loadObjectListByCriterion()] No criterion object given as second argument! Please consult the manual.');
            return null;
          // end if
         }
         return $this->loadObjectListByTextStatement($objectName,$this->__buildSelectStatementByCriterion($objectName,$criterion));

       // end function
      }


      /**
      *  @public
      *
      *  Load an object by a given criterion object.<br />
      *
      *  @param string $objectName name of the desired objects
      *  @param GenericCriterionObject $criterion criterion object
      *  @return array $ObjectList list of domain objects
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.06.2008<br />
      *  Version 0.2, 17.01.2009 (Added a check, if the criterion object is present. Otherwise return null.)<br />
      */
      function loadObjectByCriterion($objectName,$criterion){

         if($criterion === null){
            trigger_error('[GenericORRelationMapper::loadObjectByCriterion()] No criterion object given as second argument! Please consult the manual.');
            return null;
          // end if
         }
         return $this->loadObjectByTextStatement($objectName,$this->__buildSelectStatementByCriterion($objectName,$criterion));

       // end function
      }


      /**
      *  @private
      *
      *  Creates a list of WHERE statements by a given object name and a criterion object.<br />
      *
      *  @param string $ObjectName name of the desired objects
      *  @param GenericCriterionObject $Criterion criterion object
      *  @return array $WHERE list of WHERE statements
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
      */
      function __buildWhere($ObjectName,$Criterion){

         // initialize return list
         $WHERE = array();

         // retrieve property indicators
         $Properties = $Criterion->get('Properties');

         if(count($Properties) > 0){

            // add additional where statements
            foreach($Properties as $PropertyName => $PropertyValue){

               if(substr_count($PropertyValue,'%') > 0 || substr_count($PropertyValue,'_') > 0){
                  $WHERE[] = '`'.$this->__MappingTable[$ObjectName]['Table'].'`.`'.$PropertyName.'` LIKE \''.$PropertyValue.'\'';
                // end if
               }
               else{
                  $WHERE[] = '`'.$this->__MappingTable[$ObjectName]['Table'].'`.`'.$PropertyName.'` = \''.$PropertyValue.'\'';
                // end else
               }

             // end foreach
            }

          // end if
         }

         // return list of where clauses
         return $WHERE;

       // end function
      }


      /**
      *  @private
      *
      *  Creates a list of ORDER statements by a given object name and a criterion object.<br />
      *
      *  @param string $ObjectName name of the desired objects
      *  @param GenericCriterionObject $Criterion criterion object
      *  @return array $ORDER list of ORDER statements
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
      */
      function __buildOrder($ObjectName,$Criterion){

         // initialize return list
         $ORDER = array();

         // retrieve order indicators
         $Orders = $Criterion->get('Orders');

         if(count($Orders) > 0){

            // create order list
            foreach($Orders as $PropertyName => $Direction){
               $ORDER[] = '`'.$this->__MappingTable[$ObjectName]['Table'].'`.`'.$PropertyName.'` '.$Direction;
             // end foreach
            }

          // end if
         }

         // return list
         return $ORDER;

       // end function
      }


      /**
      *  @private
      *
      *  Creates a list of properties by a given object name and a criterion object.<br />
      *
      *  @param string $ObjectName name of the desired objects
      *  @param GenericCriterionObject $Criterion criterion object
      *  @return array $Properties list of properties
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.06.2008 (Extracted from __buildSelectStatementByCriterion())<br />
      */
      function __buildProperties($ObjectName,$Criterion){

         // retrieve object properties to load
         $ObjectProperties = $Criterion->get('LoadedProperties');
         if(count($ObjectProperties) > 0){

            $PropertyList = array();

            foreach($ObjectProperties as $ObjectProperty){
               $PropertyList[] = '`'.$this->__MappingTable[$ObjectName]['Table'].'`.`'.$ObjectProperty.'`';
             // end foreach
            }

            $Properties = implode(', ',$PropertyList);

          // end if
         }
         else{
            $Properties = '`'.$this->__MappingTable[$ObjectName]['Table'].'`.*';
          // end else
         }

         // return property list
         return $Properties;

       // end function
      }


      /**
      *  @private
      *
      *  Creates an SQL statement by a given object name and a criterion object.<br />
      *
      *  @param string $ObjectName name of the desired objects
      *  @param GenericCriterionObject $Criterion criterion object
      *  @return string $Statement SQL statement
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.05.2008<br />
      *  Version 0.2, 21.06.2008 (Code completed)<br />
      *  Version 0.3, 25.06.2008 (Added LIKE-Feature. If the property indicator contains a '%' or '_', the resulting statement contains a LIKE clause instead of a = clause)<br />
      */
      function __buildSelectStatementByCriterion($ObjectName,$Criterion){

         // invoke benchmarker
         $T = &Singleton::getInstance('benchmarkTimer');
         $T->start('GenericORRelationMapper::__buildSelectStatementByCriterion()');

         // generate relation joins
         $JOIN = array();
         $WHERE = array();

         $Relations = $Criterion->get('Relations');

         if(count($Relations) > 0){

            foreach($Relations as $RelationName => $RelationObject){

               // gather information about the object relations
               $RelationTable = $this->__RelationTable[$RelationName]['Table'];
               $FromTable = $this->__MappingTable[$ObjectName]['Table'];
               $TargetObjectName = $this->__getRelatedObjectNameByRelationName($ObjectName,$RelationName);
               $ToTable = $this->__MappingTable[$TargetObjectName]['Table'];
               $SoureObjectID = $this->__MappingTable[$ObjectName]['ID'];
               $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];;

               // add statement to join list
               $JOIN[] = 'INNER JOIN `'.$RelationTable.'` ON `'.$FromTable.'`.`'.$SoureObjectID.'` = `'.$RelationTable.'`.`'.$SoureObjectID.'`';
               $JOIN[] = 'INNER JOIN `'.$ToTable.'` ON `'.$RelationTable.'`.`'.$TargetObjectID.'` = `'.$ToTable.'`.`'.$TargetObjectID.'`';

               // add statement to where list
               $WHERE[] = '`'.$ToTable.'`.`'.$TargetObjectID.'` = '.$RelationObject->getProperty($TargetObjectID);

             // end foreach
            }

          // end if
         }

         // build statement
         $select = 'SELECT '.($this->__buildProperties($ObjectName,$Criterion)).' FROM `'.$this->__MappingTable[$ObjectName]['Table'].'`';

         if(count($JOIN) > 0){
            $select .= ' '.implode(' ',$JOIN);
          // end if
         }

         $WHERE = array_merge($WHERE,$this->__buildWhere($ObjectName,$Criterion));
         if(count($WHERE) > 0){
            $select .= ' WHERE '.implode(' AND ',$WHERE);
          // end if
         }

         $ORDER = $this->__buildOrder($ObjectName,$Criterion);
         if(count($ORDER) > 0){
            $select .= ' ORDER BY '.implode(', ',$ORDER);
          // end if
         }

         $Limit = $Criterion->get('Limit');
         if(count($Limit) > 0){
            $select .= ' LIMIT '.implode(',',$Limit);
          // end if
         }

         // stop benchmarker
         $T->stop('GenericORRelationMapper::__buildSelectStatementByCriterion()');

         // return statement
         return $select;

       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of related objects by an object and an relation name.<br />
      *
      *  @param GenericDomainObject $object current object
      *  @param string $relationName name of the desired relation
      *  @param GenericCriterionObject $criterion criterion object
      *  @return array $relatedObjects list of the releated objects
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      *  Version 0.2, 18.05.2008<br />
      *  Version 0.3, 08.06.2008 (Bugfix to the statement)<br />
      *  Version 0.4, 25.06.2008 (Added a third parameter to have influence on the loaded list)<br />
      *  Version 0.4, 26.06.2008 (Some changes to the statement creation)<br />
      *  Version 0.5, 25.10.2008 (Added the additional relation option via the criterion object)<br />
      *  Version 0.6, 29.12.2008 (Added check, if given object is null)<br />
      */
      function loadRelatedObjects(&$object,$relationName,$criterion = null){

         // check if object is present
         if($object === null){
            trigger_error('[GenericORRelationMapper::loadRelatedObjects()] The given object is null. Perhaps the object does not exist in database any more. Please check your implementation!');
            return null;
          // end if
         }

         // gather information about the objects related to each other
         $objectName = $object->get('ObjectName');
         $sourceObject = $this->__MappingTable[$objectName];
         $targetObjectName = $this->__getRelatedObjectNameByRelationName($objectName,$relationName);
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
         $relations = $criterion->get('Relations');
         foreach($relations as $innerRelationName => $DUMMY){

            // gather relation params
            $relationObjectName = $relations[$innerRelationName]->get('ObjectName');
            $relationSourceObject = $this->__MappingTable[$relationObjectName];
            $relationTargetObjectName = $this->__getRelatedObjectNameByRelationName($relations[$innerRelationName]->get('ObjectName'),$innerRelationName);
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
         $limit = $criterion->get('Limit');
         if(count($limit) > 0){
            $select .= ' LIMIT '.implode(',',$limit);
          // end if
         }

         // load target object list
         return $this->loadObjectListByTextStatement($targetObjectName,$select);

       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of *not* related objects by an object and an relation name.
      *
      *  @param GenericDomainObject $object current object
      *  @param string $relationName name of the desired relation
      *  @param GenericCriterionObject $criterion criterion object
      *  @return array $notRelatedObjects list of the *not* releated objects
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.10.2008<br />
      *  Version 0.2, 25.10.2008 (Added additional where and relation clauses. Bugfix to the inner relation statement.)<br />
      *  Version 0.3, 29.12.2008 (Added check, if given object is null)<br />
      */
      function loadNotRelatedObjects(&$object,$relationName,$criterion = null){

         // check if object is present
         if($object === null){
            trigger_error('[GenericORRelationMapper::loadNotRelatedObjects()] The given object is null. Perhaps the object does not exist in database any more. Please check your implementation!');
            return null;
          // end if
         }

         // gather information about the objects *not* related to each other
         $objectName = $object->get('ObjectName');
         $sourceObject = $this->__MappingTable[$objectName];
         $targetObjectName = $this->__getRelatedObjectNameByRelationName($objectName,$relationName);
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
         $relations = $criterion->get('Relations');
         foreach($relations as $innerRelationName => $DUMMY){

            // gather relation params
            $relationObjectName = $relations[$innerRelationName]->get('ObjectName');
            $relationSourceObject = $this->__MappingTable[$relationObjectName];
            $relationTargetObjectName = $this->__getRelatedObjectNameByRelationName($relations[$innerRelationName]->get('ObjectName'),$innerRelationName);
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
         $limit = $criterion->get('Limit');
         if(count($limit) > 0){
            $select .= ' LIMIT '.implode(',',$limit);
          // end if
         }

         // load target object list
         return $this->loadObjectListByTextStatement($targetObjectName,$select);

       // end function
      }


      /**
      *  @public
      *
      *  Loads the multiplicity of a relation defined by one object and the desired relation name.
      *
      *  @param GenericDomainObject $object current object
      *  @param string $relationName relation name
      *  @return int $multiplicity the multiplicity of the relation
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 16.12.2008<br />
      */
      function loadRelationMultiplicity(&$object,$relationName){

         if(!isset($this->__RelationTable[$relationName])){
            trigger_error('[GenericORRelationMapper::loadRelationMultiplicity()] Relation "'.$relationName.'" does not exist in relation table! Hence, the relation multiplicity cannot be loaded! Please check your relation configuration.');
            $multiplicity = 0;
          // end if
         }
         else{

            // gather information about the object and the relation
            $objectName = $object->get('ObjectName');
            $sourceObject = $this->__MappingTable[$objectName];
            $targetObjectName = $this->__getRelatedObjectNameByRelationName($objectName,$relationName);
            $targetObject = $this->__MappingTable[$targetObjectName];

            // load multiplicity
            $relationTable = $this->__RelationTable[$relationName];
            $select = 'SELECT COUNT(`'.$targetObject['ID'].'`) AS multiplicity FROM `'.$relationTable['Table'].'`
                       WHERE `'.$sourceObject['ID'].'` = \''.$object->getProperty($sourceObject['ID']).'\';';
            $result = $this->__DBDriver->executeTextStatement($select);
            $data = $this->__DBDriver->fetchData($result);
            $multiplicity = $data['multiplicity'];

          // end else
         }

         // return multiplicity
         return $multiplicity;

       // end function
      }


      /**
      *  @public
      *
      *  Overwrites the saveObject() method of the parent class. Resolves relations.<br />
      *
      *  @param $relationName $Object current object
      *  @return int $ObjectID; id of the saved object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      *  Version 0.2, 18.05.2008 (Function completed)<br />
      *  Version 0.3, 15.06.2008 (Fixed bug that lead to wrong association saving)<br />
      *  Version 0.4, 15.06.2008 (Fixed bug that relation was not found due to twisted columns)<br />
      *  Version 0.5, 26.10.2008 (Added a check for the object/relation to exist in the objec>t/relation table)<br />
      */
      function saveObject($Object){

         // save the current object (uses parent function with no resolving for relations)
         $ID = parent::saveObject($Object);

         // check if object has related objects in it
         $RelatedObjects = $Object->get('RelatedObjects');
         if(count($RelatedObjects) > 0){

            foreach($RelatedObjects as $RelationKey => $DUMMY){

               // save objects itself
               $count = count($RelatedObjects[$RelationKey]);
               for($i = 0; $i < $count; $i++){

                  // save object itself (recursion, because object can have further related objects!)
                  $RelatedObjectID = $this->saveObject($RelatedObjects[$RelationKey][$i]);

                  // gather information about the current relation
                  //echo 'Related object id: '.$RelatedObjectID;
                  $ObjectID = $this->__MappingTable[$Object->get('ObjectName')]['ID'];
                  if(!isset($this->__MappingTable[$RelatedObjects[$RelationKey][$i]->get('ObjectName')]['ID'])){
                     trigger_error('[GenericORRelationMapper::saveObject()] The object name "'.$RelatedObjects[$RelationKey][$i]->get('ObjectName').'" does not exist in the mapping table! Hence, your object cannot be saved! Please check your object configuration.');
                     break;
                   // end if
                  }
                  $RelObjectID = $this->__MappingTable[$RelatedObjects[$RelationKey][$i]->get('ObjectName')]['ID'];

                  // check for relation
                  if(!isset($this->__RelationTable[$RelationKey]['Table'])){
                     trigger_error('[GenericORRelationMapper::saveObject()] Relation "'.$RelationKey.'" does not exist in the relation table! Hence, your related object cannot be saved! Please check your relation configuration.');
                     break;
                   // end if
                  }

                  // create statement
                  $select = 'SELECT *
                             FROM `'.$this->__RelationTable[$RelationKey]['Table'].'`
                             WHERE `'.$ObjectID.'` = \''.$ID.'\'
                             AND `'.$RelObjectID.'` = \''.$RelatedObjectID.'\';';

                  $result = $this->__DBDriver->executeTextStatement($select);
                  $relationcount = $this->__DBDriver->getNumRows($result);
                  //$data = $this->__DBDriver->fetchData($result);
                  //echo '<br />$data[\'relationcount\']: '.$data['relationcount'].'<br />';

                  // create relation if necessary
                  if($relationcount == 0){

                     //echo '<br />';
                     $insert = 'INSERT INTO `'.$this->__RelationTable[$RelationKey]['Table'].'`
                                (`'.$RelObjectID.'`,`'.$ObjectID.'`) VALUES (\''.$RelatedObjectID.'\',\''.$ID.'\');';
                     $this->__DBDriver->executeTextStatement($insert);
                     //echo '<br />';

                   // end if
                  }

                // end for
               }

             // end foreach
            }

          // end if
         }

         // return object id
         return $ID;

       // end function
      }


      /**
      *  @public
      *
      *  Overwrites the deleteObject() method of the parent class. Resolves relations.
      *
      *  @param object $Object the object to delete
      *  @return int $ID database id of the object or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 11.05.2008<br />
      *  Version 0.2, 30.05.2008 (Completed the method's code)<br />
      */
      function deleteObject($Object){

         // return if given object is null
         if($Object === null){
            return null;
          // end
         }

         // 1. get compositions, where source or target object is the current object
         $ObjectName = $Object->get('ObjectName');
         $ObjectID = $this->__MappingTable[$ObjectName]['ID'];
         $TargetCompositions = $this->__getCompositionsByObjectName($ObjectName,'source');
         //echo printObject($TargetCompositions);

         // 2. test, if the current object has child objects and though can't be deleted
         $targetcmpcount = count($TargetCompositions);
         if($targetcmpcount != 0){

            for($i = 0; $i < $targetcmpcount; $i++){

               $select = 'SELECT * FROM `'.$TargetCompositions[$i]['Table']. '`
                          WHERE `'.$ObjectID.'` = \''.$Object->getProperty($ObjectID).'\';';
               $result = $this->__DBDriver->executeTextStatement($select);
               if($this->__DBDriver->getNumRows($result) > 0){
                  trigger_error('[GenericORRelationMapper::deleteObject()] Domain object "'.$ObjectName.'" with id "'.$Object->getProperty($ObjectID).'" cannot be deleted, because it still has composed child objects!',E_USER_WARNING);
                  return null;
                // end if
               }

             // end for
            }

          // end if
         }

         // 3. check for associations and delete them
         $Associations = $this->__getAssociationsByObjectName($ObjectName);
         //echo printObject($Associations);

         $asscount = count($Associations);
         for($i = 0; $i < $asscount; $i++){

            $delete = 'DELETE FROM `'.$Associations[$i]['Table'].'`
                       WHERE `'.$ObjectID.'` = \''.$Object->getProperty($ObjectID).'\';';
            $this->__DBDriver->executeTextStatement($delete);
            //echo '<br />';

          // end if
         }

         // 4. delete object itself
         $ID = parent::deleteObject($Object);

         // 5. delete composition towards other object
         $SourceCompositions = $this->__getCompositionsByObjectName($ObjectName,'target');
         //echo printObject($SourceCompositions);

         $sourcecmpcount = count($SourceCompositions);
         for($i = 0; $i < $sourcecmpcount; $i++){

            $delete = 'DELETE FROM `'.$SourceCompositions[$i]['Table'].'`
                       WHERE `'.$ObjectID.'` = \''.$Object->getProperty($ObjectID).'\';';
            $this->__DBDriver->executeTextStatement($delete);
            //echo '<br />';

          // end for
         }

         // return id
         return $ID;

       // end function
      }


      /**
      *  @public
      *
      *  Creates an association between two objects.<br />
      *
      *  @param string $RelationName; Name ofthe relation to create
      *  @param GenericDomainObject $SourceObject; Source object for the relation
      *  @param GenericDomainObject $TargetObject; Target object for the relation
      *  @return bool $return; false (relation is not an association) or true (everything's fine)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.05.2008<br />
      *  Version 0.2, 31.05.2008 (Code completed)<br />
      *  Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
      */
      function createAssociation($RelationName,$SourceObject,$TargetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$RelationName])){
            trigger_error('[GenericORRelationMapper::createAssociation()] Relation with name "'.$RelationName.'" is not defined in the mapping table! Please check your relation configuration.',E_USER_WARNING);
            return false;
          // end
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$RelationName]['Type'] == 'COMPOSITION'){
            trigger_error('[GenericORRelationMapper::createAssociation()] Compositions cannot be created with this method! Use saveObject() on the target object to create a composition!',E_USER_WARNING);
            return false;
          // end if
         }

         // create association
         $SourceObjectName = $SourceObject->get('ObjectName');
         $SourceObjectID = $this->__MappingTable[$SourceObjectName]['ID'];
         $TargetObjectName = $TargetObject->get('ObjectName');
         $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

         $insert = 'INSERT INTO `'.$this->__RelationTable[$RelationName]['Table'].'`
                    (`'.$SourceObjectID.'`,`'.$TargetObjectID.'`)
                    VALUES
                    (\''.$SourceObject->getProperty($SourceObjectID).'\',\''.$TargetObject->getProperty($TargetObjectID).'\');';
         $this->__DBDriver->executeTextStatement($insert);

         // return success
         return true;

       // end function
      }


      /**
      *  @public
      *
      *  Delete an association between two objects. Due to data consistency, only associations<br />
      *  can be deleted.<br />
      *
      *  @param string $RelationName Name ofthe relation to create
      *  @param GenericDomainObject $SourceObject Source object for the relation
      *  @param GenericDomainObject $TargetObject Target object for the relation
      *  @return bool $return true (success) or false (error)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.05.2008<br />
      *  Version 0.2, 31.05.2008 (Code completed)<br />
      *  Version 0.3, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
      */
      function deleteAssociation($RelationName,$SourceObject,$TargetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$RelationName])){
            trigger_error('[GenericORRelationMapper::deleteAssociation()] Relation with name "'.$RelationName.'" is not defined in the mapping table! Please check your relation configuration.',E_USER_WARNING);
            return false;
          // end
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$RelationName]['Type'] == 'COMPOSITION'){
            trigger_error('[GenericORRelationMapper::deleteAssociation()] Compositions cannot be deleted! Use deleteObject() on the target object instead!',E_USER_WARNING);
            return false;
          // end if
         }

         // get association and delete it
         $SourceObjectName = $SourceObject->get('ObjectName');
         $SourceObjectID = $this->__MappingTable[$SourceObjectName]['ID'];
         $TargetObjectName = $TargetObject->get('ObjectName');
         $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

         $delete = 'DELETE FROM `'.$this->__RelationTable[$RelationName]['Table'].'`
                    WHERE
                       `'.$SourceObjectID.'` = \''.$SourceObject->getProperty($SourceObjectID).'\'
                       AND
                       `'.$TargetObjectID.'` = \''.$TargetObject->getProperty($TargetObjectID).'\';';
         $this->__DBDriver->executeTextStatement($delete);

         // return success
         return true;

       // end function
      }


      /**
      *  @public
      *
      *  Returns true if an association of the given type exists between the provided objects.<br />
      *
      *  @param string $RelationName Name of the relation to select
      *  @param GenericDomainObject $SourceObject Source object for the relation
      *  @param GenericDomainObject $TargetObject Target object for the relation
      *  @return bool $return true (association exists) or false (objects are not associated)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.05.2008<br />
      *  Version 0.1, 08.06.2008 (Introduced a test to check weather relation exists or not)<br />
      */
      function isAssociated($RelationName,$SourceObject,$TargetObject){

         // test, if relation exists in relation table
         if(!isset($this->__RelationTable[$RelationName])){
            trigger_error('[GenericORRelationMapper::isAssociated()] Relation with name "'.$RelationName.'" is not defined in the relation table! Please check your relation configuration.',E_USER_WARNING);
            return false;
          // end
         }

         // if relation is a composition, return with error
         if($this->__RelationTable[$RelationName]['Type'] == 'COMPOSITION'){
            trigger_error('[GenericORRelationMapper::isAssociated()] The given relation ("'.$RelationName.'") is not an association! Please check your relation configuration.',E_USER_WARNING);
            return false;
          // end if
         }

         // check for association
         $SourceObjectName = $SourceObject->get('ObjectName');
         $SourceObjectID = $this->__MappingTable[$SourceObjectName]['ID'];
         $TargetObjectName = $TargetObject->get('ObjectName');
         $TargetObjectID = $this->__MappingTable[$TargetObjectName]['ID'];

         $delete = 'SELECT * FROM `'.$this->__RelationTable[$RelationName]['Table'].'`
                    WHERE
                       `'.$SourceObjectID.'` = \''.$SourceObject->getProperty($SourceObjectID).'\'
                       AND
                       `'.$TargetObjectID.'` = \''.$TargetObject->getProperty($TargetObjectID).'\';';
         $result = $this->__DBDriver->executeTextStatement($delete);

         // return if objects are associated
         if($this->__DBDriver->getNumRows($result) > 0){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Returns all associations concerning one object.
      *
      *  @param string $objectName name of the current object
      *  @return array $relationList list of relations of the given object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.05.2008<br />
      *  Version 0.2, 28.12.2008 (Bugfix: only associations are returned, where the object is involved)<br />
      */
      function &__getAssociationsByObjectName($objectName){

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

         // return list
         return $relationList;

       // end function
      }


      /**
      *  @private
      *
      *  Returns all compositions concerning one object name.<br />
      *
      *  @param string $ObjectName Name of the current object
      *  @param string $Direction Direction of the relation (legal values: source, target)
      *  @return array $RelationList List of relations of the given type
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.05.2008<br />
      */
      function __getCompositionsByObjectName($ObjectName,$Direction = null){

         // initialize list
         $RelationList = array();

         // declare attribute to indicate the direction of the relation
         if($Direction == 'source'){
            $DirectionAttribute = 'SourceObject';
          // end if
         }
         elseif($Direction == 'target'){
            $DirectionAttribute = 'TargetObject';
          // end else
         }
         else{
            trigger_error('Direction of the composition not specified! Please use "source" or "target" as values!',E_USER_WARNING);
            return $RelationList;
          // end else
         }

         // look for suitable relation entries
         foreach($this->__RelationTable as $SectionName => $Attributes){

            if($Attributes['Type'] == 'COMPOSITION' && $Attributes[$DirectionAttribute] == $ObjectName){
               $RelationList[] = &$this->__RelationTable[$SectionName];
             // end if
            }

          // end foreach
         }

         // return list
         return $RelationList;

       // end function
      }


      /**
      *  @private
      *
      *  Returns the name of the related object concerning the given arguments.<br />
      *
      *  @param string $ObjectName Name of the current object
      *  @param string $RelationName Name of the desired relation
      *  @return string $RelatedObject Name of the releated object or null, in case the object definition was not found
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      */
      function __getRelatedObjectNameByRelationName($ObjectName,$RelationName){

         // look for suitable related object
         foreach($this->__RelationTable as $SectionName => $Attributes){

            if($SectionName == $RelationName){

               if($Attributes['SourceObject'] == $ObjectName){
                  return $Attributes['TargetObject'];
                // end if
               }

               if($Attributes['TargetObject'] == $ObjectName){
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
      *  @public
      *
      *  Implements php's magic __sleep() method to indicate, which class vars have to be serialized.<br />
      *
      *  @return array $Vars2Serialize list of serializable properties
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.05.2008<br />
      *  Version 0.2, 25.06.2008 (Removed "ApplicationID" from sleep list)<br />
      *  Version 0.3, 26.10.2008 (Added "__importedConfigCache")<br />
      */
      function __sleep(){
         return array('__MappingTable','__RelationTable','__Context','__Language','__importedConfigCache');
       // end function
      }

    // end class
   }
?>