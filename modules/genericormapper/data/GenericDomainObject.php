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
import('modules::genericormapper::data', 'GenericORMapperDataObject');
import('modules::genericormapper::data', 'GenericORMapperException');

/**
 * @package modules::genericormapper::data
 * @class GenericDomainObject
 *
 * Generic class for all domain objects handled by the abstract or mapper.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.04.2008<br />
 * Version 0.2, 04.09.2009 (Added serialization support)<br />
 * Version 0.3, 05.06.2010 (Added convenience methods for object name and id handling)<br />
 * Version 0.4, 15.01.2011 (Added usage of GenericORMapperDataObject-interface and removed dependence on APFObject)<br />
 */
class GenericDomainObject implements GenericORMapperDataObject {

   /**
    * @private
    * @var GenericORRelationMapper Data component, that can be used to lazy load attributes.
    * To set the member, use setDataComponent().
    */
   private $dataComponent = null;

   /**
    * private
    * @var string Name of the object (see mapping table!).
    */
   protected $objectName = null;

   /**
    * @private
    * @var string[] Properties of a domain object.
    */
   protected $properties = array();

   /**
    * @private
    * @var GenericORMapperDataObject[] Objects related to the current object. Sorted by composition or association key.
    */
   protected $relatedObjects = array();

   /**
    * @private
    * @var string Timestamp value of relation-creation.
    */
   private $relationCreationTimestamp = null;

   /**
    * @public
    *
    * Constructor of the generic domain object. Sets the object name if desired.
    *
    * @param string $objectName name of the domain object.
    * @throws InvalidArgumentException In case the constructor argument is no valid object name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.04.2008<br />
    */
   public function __construct($objectName = null) {
      if (empty($objectName)) {
         throw new InvalidArgumentException('[GenericDomainObject::__construct()] Creating a '
               . 'GenericDomainObject must include an object name specification. Otherwise, '
               . 'the GenericORMapper cannot handle this instance.', E_USER_ERROR);
      }
      $this->objectName = $objectName;
   }

   /**
    * @public
    *
    * Returns the name of the object as given during creation of the object
    * or loading of the object by the GORM.
    *
    * @return string The name of the object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2010<br />
    */
   public function getObjectName() {
      return $this->objectName;
   }

   /**
    * @public
    *
    * Convenience function to retrieve the object id depending on the object type.
    *
    * @return int The object's internal id (id of the object in database).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function getObjectId() {
      return $this->getProperty($this->objectName . 'ID');
   }

   /**
    * @public
    *
    * Convenience function to set the object id depending on the object type.
    *
    * @param int $id The object's internal id (id of the object in database).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function setObjectId($id) {
      $this->setProperty($this->objectName . 'ID', $id);
   }

   /**
    * @public
    *
    * Injects the current mapper instance for further usage (load related objects).
    *
    * @param GenericORRelationMapper $orm The current mapper instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    */
   public function setDataComponent(GenericORRelationMapper &$orm) {
      $this->dataComponent = &$orm;
   }

   /**
    * @public
    *
    * Deletes the current mapper instance (usefull for debugging).
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 18.05.2012<br />
    */
   public function deleteDataComponent() {
      $this->dataComponent = null;
   }

   /**
    * @public
    *
    * Returns the current mapper instance for further usage (load related objects).
    *
    * @return GenericORRelationMapper The current mapper instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.10.2009<br />
    */
   public function &getDataComponent() {
      return $this->dataComponent;
   }

   /**
    * @public
    *
    * Load one related object.
    *
    * @param string $relationName name of the desired relation
    * @param GenericCriterionObject $criterion criterion object
    * @return GenericORMapperDataObject object that is related with the current object or null.
    * @throws GenericORMapperException In case the data component is not initialized.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 09.09.2010<br />
    */
   public function loadRelatedObject($relationName, GenericCriterionObject $criterion = null) {

      // check weather data component is there
      if ($this->dataComponent === null) {
         throw new GenericORMapperException('[GenericDomainObject::loadRelatedObject()] '
               . 'The data component is not initialized, so related object cannot be loaded! '
               . 'Please use the or mapper\'s loadRelatedObject() method or call '
               . 'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
               . 'object first!', E_USER_ERROR);
      }

      // return objects that are related to the current object
      return $this->dataComponent->loadRelatedObject($this, $relationName, $criterion);
   }

   /**
    * @public
    *
    * Loads a list of related objects.
    *
    * @param string $relationName name of the desired relation
    * @param GenericCriterionObject $criterion criterion object
    * @return GenericORMapperDataObject[] List of objects that are related with the current object or null.
    * @throws GenericORMapperException In case the data component is not initialized.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.04.2008<br />
    * Version 0.2, 15.06.2008 (If data component is not initialized, the method now returns null)<br />
    * Version 0.3, 16.06.2008 (Caching of objects disabled, due to recursion errors)<br />
    * Version 0.4, 25.06.2008 (Added a second parameter to have influence on the loaded list)<br />
    */
   public function loadRelatedObjects($relationName, GenericCriterionObject $criterion = null) {

      // check weather data component is there
      if ($this->dataComponent === null) {
         throw new GenericORMapperException('[GenericDomainObject::loadRelatedObjects()] '
               . 'The data component is not initialized, so related objects cannot be loaded! '
               . 'Please use the or mapper\'s loadRelatedObjects() method or call '
               . 'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
               . 'object first!', E_USER_ERROR);
      }

      // return objects that are related to the current object
      return $this->dataComponent->loadRelatedObjects($this, $relationName, $criterion);
   }

   /**
    * @public
    *
    * Convenience method to create an association to another object without invoking the
    * GenericORMapper directly.
    *
    * @param string $relationName The relation to create.
    * @param GenericORMapperDataObject $targetObject The object to relate the current domain object to.
    * @throws GenericORMapperException In case the data component is not initialized.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 30.10.2010<br />
    */
   public function createAssociation($relationName, GenericORMapperDataObject $targetObject) {

      // check weather data component is there
      if ($this->dataComponent === null) {
         throw new GenericORMapperException('[GenericDomainObject::createAssociation()] '
               . 'The data component is not initialized, so related objects cannot be loaded! '
               . 'Please use the or mapper\'s createAssociation() method or call '
               . 'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
               . 'object first!', E_USER_ERROR);
      }

      // create association as desired
      $this->dataComponent->createAssociation($relationName, $this, $targetObject);
   }

   /**
    * @public
    *
    * Convenience method to delete an association to another object without invoking the
    * GenericORMapper directly.
    *
    * @param string $relationName The relation to delete.
    * @param GenericORMapperDataObject $targetObject The object to delete the current domain object's relation from.
    * @throws GenericORMapperException In case the data component is not initialized.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 30.10.2010<br />
    */
   public function deleteAssociation($relationName, GenericORMapperDataObject $targetObject) {

      // check weather data component is there
      if ($this->dataComponent === null) {
         throw new GenericORMapperException('[GenericDomainObject::deleteAssociation()] '
               . 'The data component is not initialized, so related objects cannot be loaded! '
               . 'Please use the or mapper\'s createAssociation() method or call '
               . 'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
               . 'object first!', E_USER_ERROR);
      }

      // delete association as desired
      $this->dataComponent->deleteAssociation($relationName, $this, $targetObject);
   }

   /**
    * @public
    *
    * Convenience method to delete all association to another object without invoking the
    * GenericORMapper directly.
    *
    * @param string $relationName The relation to create.
    * @throws GenericORMapperException In case the data component is not initialized.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 30.10.2010<br />
    */
   public function deleteAssociations($relationName) {

      // check weather data component is there
      if ($this->dataComponent === null) {
         throw new GenericORMapperException('[GenericDomainObject::deleteAssociations()] '
               . 'The data component is not initialized, so related objects cannot be loaded! '
               . 'Please use the or mapper\'s createAssociation() method or call '
               . 'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
               . 'object first!', E_USER_ERROR);
      }

      // delete associations as desired
      $this->dataComponent->deleteAssociations($relationName, $this);
   }

   /**
    * @public
    *
    * Returns the reference on the list of related objects manually added to the object.
    *
    * @param string $relationName name of the desired relation
    * @return GenericORMapperDataObject[] A list of referenced objects that are related with the current object or null
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2008<br />
    */
   public function &getRelatedObjects($relationName) {

      if (isset($this->relatedObjects[$relationName])) {
         return $this->relatedObjects[$relationName];
      } else {
         $null = null;
         return $null;
      }
   }

   /**
    * @public
    *
    * Returns the entire relation structur of the current domain object. This method
    * *should* only be used internally. Unfortunately, PHP does not provide a package
    * view visibility setting.
    *
    * @return string[GenericORMapperDataObject[]] A list of generic domain objects with their respective relation name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function &getAllRelatedObjects() {
      return $this->relatedObjects;
   }

   /**
    * @public
    *
    * Add a related object.
    *
    * @param string $relationName name of the desired relation
    * @param GenericORMapperDataObject $object Object that is related with the current object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.04.2008<br />
    * Version 0.2, 03.05.2009 (Added check for null objects. In null cases, the object is not added.)<br />
    */
   public function addRelatedObject($relationName, GenericORMapperDataObject &$object) {
      if ($object !== null) {
         $this->relatedObjects[$relationName][] = $object;
      }
   }

   /**
    * @public
    *
    * Abstract method to set a domain object's simple property.
    *
    * @param string $name name of the specified domain object property
    * @param string $value value of the specified domain object property
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    */
   public function setProperty($name, $value) {
      $this->properties[$name] = $value;
   }

   /**
    * @public
    *
    * Abstract method to get a domain object's simple property.
    *
    * @param string $name name of the specified domain object property
    * @return string Value of the specified domain object property
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    */
   public function getProperty($name) {

      if (isset($this->properties[$name])) {
         return $this->properties[$name];
      } else {
         return null;
      }
   }

   /**
    * @public
    *
    * Abstract method to set all domain object's simple properties.
    *
    * @param string[] $properties list of defined properties to apply to the domain object
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    */
   public function setProperties($properties = array()) {
      if (count($properties) > 0) {
         $this->properties = $properties;
      }
   }

   /**
    * @public
    *
    * Abstract method to get all domain object's simple properties.
    *
    * @return string[] List of defined domain object properties.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    */
   public function getProperties() {
      return $this->properties;
   }

   /**
    * @public
    *
    * Removes an attribute from the list.
    *
    * @param string $name The name of the property to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.09.2009 (Introduces because of bug 202)<br />
    */
   public function deleteProperty($name) {
      unset($this->properties[$name]);
   }

   /**
    * @public
    *
    * Implements the toString() method for the generic domain object.
    *
    * @return string The domain object's string representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 18.08.2008<br />
    * Version 0.2, 09.08.2009 (Changed output to a more simple one.)<br />
    * Version 0.3, 01.03.2010 (Added mapping to display PHP null values als MySQL null values)<br />
    */
   public function toString() {

      $stringRep = (string)'[' . get_class($this) . ' ';

      $properties = array_merge(array('ObjectName' => $this->getObjectName()), $this->properties);

      $propCount = count($properties);
      $current = (int)1;

      foreach ($properties as $name => $value) {

         // map PHP null to MySQL null
         if ($value == null) {
            $value = 'NULL';
         }
         $stringRep .= $name . '="' . $value . '"';

         if ($current < $propCount) {
            $stringRep .= ', ';
         }

         $current++;
      }
      return $stringRep . ']';
   }

   /**
    * @public
    *
    * Implements the PHP wrapper for generating the string
    * representation of the current domain object.
    *
    * @return string The string representation of the current domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function __toString() {
      return $this->toString();
   }

   /**
    * @public
    *
    * Implements php's magic __sleep() method to indicate, which class vars have to be serialized.
    *
    * @return string[] List of serializable properties.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.09.2009<br />
    * Version 0.2, 10.06.2010 (Bug-fix: corrected serialization)<br />
    */
   public function __sleep() {
      return array('objectName', 'properties', 'relatedObjects');
   }

   /**
    * @public
    *
    * Implement event functions
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function beforeSave() {

   }

   /**
    * @public
    *
    * Implement event functions
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function afterSave() {

   }

   /**
    * @public
    *
    * Implement event functions
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 15.01.2011<br />
    */
   public function afterLoad() {

   }

   /**
    * @public
    *
    * Extract relation-timestamps
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 27.07.2012<br />
    */
   public function extractRelationTimestamps($prefix) {
      $creationTimestamp = $prefix . '_CreationTimestamp';

      $this->relationCreationTimestamp = $this->properties[$creationTimestamp];
      $this->deleteProperty($creationTimestamp);
   }

   /**
    * @public
    *
    * Abstract method to get object's relation creation-timestamp.
    *
    * @return string Creation-timestamp of the relation.
    *
    * @author Lutz Mahlstedt
    * @version
    * Version 0.1, 27.07.2012<br />
    */
   public function getRelationCreationTimestamp() {
      return $this->relationCreationTimestamp;
   }

}
