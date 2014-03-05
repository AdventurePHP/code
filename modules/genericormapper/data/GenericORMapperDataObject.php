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

/**
 * @package APF\modules\genericormapper\data
 * @class GenericORMapperDataObject
 *
 * Defines the Interface which must be implemented by every domain object.
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 15.01.2011<br />
 */
interface GenericORMapperDataObject {

   public function __construct($objectName = null);

   /**
    * @return string The name of the current object.
    */
   public function getObjectName();

   public function getObjectId();

   public function setObjectId($id);

   /**
    * @param GenericORRelationMapper $orm The instance of the o/r mapper to use.
    */
   public function setDataComponent(GenericORRelationMapper &$orm);

   /**
    * @return GenericORRelationMapper The instance of the o/r mapper.
    */
   public function &getDataComponent();

   /**
    * @param string $relationName The name of the relation to load the objects with.
    * @param GenericCriterionObject $criterion The criterion to limit the selection.
    * @return GenericORMapperDataObject The related object.
    */
   public function loadRelatedObject($relationName, GenericCriterionObject $criterion = null);

   /**
    * @param string $relationName The name of the relation to load the objects with.
    * @param GenericCriterionObject $criterion The criterion to limit the selection.
    * @return GenericORMapperDataObject[] A list of related objects.
    */
   public function loadRelatedObjects($relationName, GenericCriterionObject $criterion = null);

   public function createAssociation($relationName, GenericORMapperDataObject $targetObject);

   public function deleteAssociation($relationName, GenericORMapperDataObject $targetObject);

   public function deleteAssociations($relationName);

   /**
    * @param string $relationName The name of the relation to get the related objects with.
    * @return GenericORMapperDataObject[] The list of related objects.
    */
   public function &getRelatedObjects($relationName);

   /**
    * @return GenericORMapperDataObject[]
    */
   public function &getAllRelatedObjects();

   public function addRelatedObject($relationName, GenericORMapperDataObject &$object);

   public function setProperty($name, $value);

   /**
    * @abstract
    * @param $name
    * @return string The value of the desired property.
    */
   public function getProperty($name);

   public function setProperties($properties = array());

   /**
    * @return string[] An associative array of the object's properties.
    */
   public function getProperties();

   public function deleteProperty($name);

   /**
    * Will be called by GORM before object will be saved.
    */
   public function beforeSave();

   /**
    * Will be called by GORM after object was saved.
    */
   public function afterSave();

   /**
    * Will be called by GORM after object was loaded and properties are set.
    */
   public function afterLoad();

}