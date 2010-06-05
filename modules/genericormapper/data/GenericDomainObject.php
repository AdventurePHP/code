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
    * @package modules::genericormapper::biz
    * @class GenericDomainObject
    *
    * Generic class for all domain objects handled by the abstract or mapper.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    * Version 0.2, 04.09.2009 (Added serialization support)<br />
    * Version 0.3, 05.06.2010 (Added convenience methods for object name and id handling)<br />
    */
   final class GenericDomainObject extends APFObject {

      /**
       * @private
       * @var GenericORRelationMapper Data component, that can be used to lazy load attributes.
       * To set the member, use setDataComponent().
       */
      private $dataComponent = null;

      /**
       * @private
       * @var string Name of the object (see mapping table!).
       */
      private $objectName = null;

      /**
       * @private
       * @var string[] Properties of a domain object.
       */
      private $properties = array();

      /**
       * @private
       * @var GenericDomainObject[] Objects related to the current object. Sorted by composition or association key.
       */
      private $relatedObjects = array();

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
      public function GenericDomainObject($objectName){
         if(empty($objectName)){
            throw new InvalidArgumentException('[GenericDomainObject::__constructor()] Creating a '
                    .'GenericDomainObject must include an object name specification. Otherwise, '
                    .'the GenericORMapper cannot handle this instance.',E_USER_ERROR);
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
      public function getObjectName(){
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
      public function getObjectId(){
         return $this->getProperty($this->objectName.'ID');
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
      public function setObjectId($id){
         $this->setProperty($this->objectName.'ID',$id);
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
      public function setDataComponent(&$orm){
         $this->dataComponent = &$orm;
      }

      /**
       * @public
       *
       * Returns the current mapper instance for further usage (load related objects).
       *
       * @param GenericORRelationMapper $orm The current mapper instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.10.2009<br />
       */
      public function &getDataComponent(){
         return $this->dataComponent;
      }

      /**
       * @public
       *
       * Loads a list of related objects.
       *
       * @param string $relationName name of the desired relation
       * @param GenericCriterionObject $criterion criterion object
       * @return GenericDomainObject[] List of objects that are related with the current object or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.04.2008<br />
       * Version 0.2, 15.06.2008 (If data component is not initialized, the method now returns null)<br />
       * Version 0.3, 16.06.2008 (Caching of objects disabled, due to recursion errors)<br />
       * Version 0.4, 25.06.2008 (Added a second parameter to have influence on the loaded list)<br />
       */
      public function loadRelatedObjects($relationName,GenericCriterionObject $criterion = null){

         // check weather data component is there
         if($this->dataComponent === null){
            throw new GenericORMapperException('[GenericDomainObject::loadRelatedObjects()] '
                    .'The data component is not initialized, so related objects cannot be loaded! '
                    .'Please use the or mapper\'s loadRelatedObjects() method or call '
                    .'setDataComponent($orm), where $orm is an instance of the or mapper, on this '
                    .'object first!',E_USER_ERROR);
            return null;
          // end if
         }

         // return objects that are related to the current object
         return $this->dataComponent->loadRelatedObjects($this,$relationName,$criterion);

       // end function
      }

      /**
       * @public
       *
       * Returns the reference on the list of related objects manually added to the object.
       *
       * @param string $relationName name of the desired relation
       * @return GenericDomainObject[] A list of referenced objects that are related with the current object or null
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2008<br />
       */
      public function &getRelatedObjects($relationName){

         if(isset($this->relatedObjects[$relationName])){
            return $this->relatedObjects[$relationName];
         }
         else{
            $null = null;
            return $null;
         }

       // end function
      }

      /**
       * @public
       *
       * Returns the entire relation structur of the current domain object. This method
       * *should* only be used internally. Unfortunately, PHP does not provide a package
       * view visibility setting.
       *
       * @return string[GenericDomainObject[]] A list of generic domain objects with their respective relation name.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.06.2010<br />
       */
      public function &getAllRelatedObjects(){
         return $this->relatedObjects;
      }

      /**
       * @public
       *
       * Add a related object.
       *
       * @param string $relationName name of the desired relation
       * @param GenericDomainObject $object Object that is related with the current object.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.04.2008<br />
       * Version 0.2, 03.05.2009 (Added check for null objects. In null cases, the object is not added.)<br />
       */
      public function addRelatedObject($relationName,&$object){
         
         if($object !== null){
            $this->relatedObjects[$relationName][] = &$object;
         }

       // end function
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
      public function setProperty($name,$value){
         $this->properties[$name] = $value;
       // end function
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
      public function getProperty($name){

         if(isset($this->properties[$name])){
            return $this->properties[$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
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
      public function setProperties($properties = array()){

         if(count($properties) > 0){
            $this->properties = $properties;
          // end if
         }

       // end function
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
      public function getProperties(){
         return $this->properties;
       // end function
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
      public function deleteProperty($name){
         unset($this->properties[$name]);
       // end function
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
      public function toString(){

         $stringRep = (string)'[GenericDomainObject ';

         $properties = array_merge(array('ObjectName' => $this->getObjectName()),$this->properties);

         $propCount = count($properties);
         $current = (int) 1;

         foreach($properties as $name => $value){

            // map PHP null to MySQL null
            if($value == null){
               $value = 'NULL';   
            }
            $stringRep .= $name.'="'.$value.'"';

            if($current < $propCount) {
               $stringRep .= ', ';
             // end if
            }

            $current++;
         
          // end foreach
         }
         return $stringRep.']';

       // end function
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
       */
      public function __sleep(){
         return array('__ObjectName','__Properties','__RelatedObjects');
       // end function
      }

    // end class
   }
?>