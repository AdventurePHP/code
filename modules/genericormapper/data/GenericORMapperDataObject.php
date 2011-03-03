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

   /**
    * @package modules::genericormapper::data
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

       public function getObjectName();

       public function getObjectId();

       public function setObjectId($id);

       /**
        * @param GenericORRelationMapper $orm
        */
       public function setDataComponent(GenericORRelationMapper &$orm);

       /**
        * @return GenericORRelationMapper
        */
       public function &getDataComponent();

       /**
        * @return GenericORMapperDataObject
        */
       public function loadRelatedObject($relationName, GenericCriterionObject $criterion = null);

       /**
        * @return GenericORMapperDataObject[]
        */
       public function loadRelatedObjects($relationName, GenericCriterionObject $criterion = null);

       public function createAssociation($relationName, GenericORMapperDataObject $targetObject);

       public function deleteAssociation($relationName, GenericORMapperDataObject $targetObject);

       public function deleteAssociations($relationName);

       /**
        * @return GenericORMapperDataObject[]
        */
       public function &getRelatedObjects($relationName);

       /**
        * @return string[GenericORMapperDataObject[]]
        */
       public function &getAllRelatedObjects();

       public function addRelatedObject($relationName, GenericORMapperDataObject &$object);

       function setProperty($name, $value);

       function getProperty($name);

       public function setProperties($properties = array());

       /**
        * @return string[]
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

       // end interface
    }