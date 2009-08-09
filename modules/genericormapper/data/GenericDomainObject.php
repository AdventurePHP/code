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

   /**
    * @namespace modules::genericormapper::biz
    * @class GenericDomainObject
    *
    * Generic class for all domain objects handled by the abstract or mapper.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.04.2008<br />
    */
   final class GenericDomainObject extends coreObject
   {

      /**
       * @protected
       * Data component, that can be used to lazy load attributes.
       * To set the member, use setByReference() from coreObject.
       */
      protected $__DataComponent = null;


      /**
       * @protected
       * Name of the object (see mapping table!).
       */
      protected $__ObjectName = null;


      /**
       * @protected
       * Properties of a domain object.
       */
      protected $__Properties = array();


      /**
       * @protected
       * Objects related to the current object. Sorted by composition or association key.
       */
      protected $__RelatedObjects = array();


      /**
       * @public
       *
       * Constructor of the generic domain object. Sets the object name if desired.<br />
       *
       * @param string $objectName name of the domain object
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.04.2008<br />
       */
      public function GenericDomainObject($objectName){
         $this->__ObjectName = $objectName;
       // end function
      }


      /**
       * @public
       *
       * Loads a list of related objects.<br />
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
         if($this->__DataComponent === null){
            trigger_error('[GenericDomainObject::loadRelatedObjects()] DataDomponent is not initialized, so related objects cannot be loaded! Please use the or mapper\'s loadRelatedObjects() method or call setByReference(\'DataComponent\',$ORM), where $ORM is an instance of the or mapper, on this object first!',E_USER_WARNING);
            return null;
          // end if
         }

         // return objects that are related to the current object
         return $this->__DataComponent->loadRelatedObjects($this,$relationName,$criterion);

       // end function
      }


      /**
       * @public
       *
       * Returns the reference on the list of related objects manually added to the object.
       *
       * @param string $relationName name of the desired relation
       * @return GenericDomainObject[] $relatedObjects a list of referenced objects that are related with the current object or null
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.12.2008<br />
       */
      public function &getRelatedObjects($relationName){

         if(isset($this->__RelatedObjects[$relationName])){
            return $this->__RelatedObjects[$relationName];
          // end if
         }
         else{
            $null = null;
            return $null;
          // end else
         }

       // end function
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
            $this->__RelatedObjects[$relationName][] = &$object;
         }

       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to set a domain object's simple property.<br />
      *
      *  @param string $name name of the specified domain object property
      *  @param string $value value of the specified domain object property
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      public function setProperty($name,$value){
         $this->__Properties[$name] = $value;
       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to get a domain object's simple property.<br />
      *
      *  @param string $name name of the specified domain object property
      *  @return string $PropertyValue value of the specified domain object property
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      public function getProperty($name){

         if(isset($this->__Properties[$name])){
            return $this->__Properties[$name];
          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to set all domain object's simple properties.<br />
      *
      *  @param array $properties list of defined properties to apply to the domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      public function setProperties($properties = array()){

         if(count($properties) > 0){
            $this->__Properties = $properties;
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to get all domain object's simple properties.<br />
      *
      *  @return array $Properties list of defined domain object properties
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      public function getProperties(){
         return $this->__Properties;
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
       */
      public function toString(){

         $stringRep = (string)'[GenericDomainObject ';

         $properties = array_merge(array('ObjectName' => $this->__ObjectName),$this->__Properties);

         $propCount = count($properties);
         $current = (int) 1;

         foreach($properties as $name => $value){

            $stringRep .= $name.'="'.$value.'"';

            if($current < $propCount) {
               $stringRep .= ',';
             // end if
            }

            $current++;
         
          // end foreach
         }
         return $stringRep.']';

       // end function
      }

    // end class
   }
?>