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

   /**
   *  @namespace modules::genericormapper::biz
   *  @class GenericDomainObject
   *
   *  Abstract class for all domain objects handled by the abstract or mapper.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   */
   class GenericDomainObject extends coreObject
   {

      /**
      *  @private
      *  Data component, that can be used to lazy load attributes.
      *  To set the member, use setByReference() from coreObject.
      */
      var $__DataComponent = null;


      /**
      *  @private
      *  Name of the object (see mapping table!).
      */
      var $__ObjectName = null;


      /**
      *  @private
      *  Properties of a domain object.
      */
      var $__Properties = array();


      /**
      *  @private
      *  Objects related to the current object. Sorted by composition or association key.
      */
      var $__RelatedObjects = array();


      /**
      *  @public
      *
      *  Constructor of the generic domain object. Sets the object name if desired.<br />
      *
      *  @param string $ObjectName name of the domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.04.2008<br />
      */
      function GenericDomainObject($ObjectName = null){

         if($ObjectName !== null){
            $this->__ObjectName = $ObjectName;
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Loads a list of related objects.<br />
      *
      *  @param string $RelationName name of the desired relation
      *  @param GenericCriterionObject $Criterion criterion object
      *  @return array $ObjectList List of objects that are related with the current object or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.04.2008<br />
      *  Version 0.2, 15.06.2008 (If data component is not initialized, the method now returns null)<br />
      *  Version 0.3, 16.06.2008 (Caching of objects disabled, due to recursion errors)<br />
      *  Version 0.4, 25.06.2008 (Added a second parameter to have influence on the loaded list)<br />
      */
      function loadRelatedObjects($RelationName,$Criterion = null){

         // check weather data component is there
         if($this->__DataComponent === null){
            trigger_error('[GenericDomainObject::loadRelatedObjects()] DataDomponent is not initialized, so related objects cannot be loaded! Please use the or mapper\'s loadRelatedObjects() method or call setByReference(\'DataComponent\',$ORM), where $ORM is an instance of the or mapper, on this object first!',E_USER_WARNING);
            return null;
          // end if
         }

         // return objects that are related to the current object
         return $this->__DataComponent->loadRelatedObjects($this,$RelationName,$Criterion);

       // end function
      }


      /**
      *  @public
      *
      *  Returns the reference on the list of related objects manually added to the object.
      *
      *  @param string $relationName name of the desired relation
      *  @return GenericDomainObject[] $relatedObjects a list of referenced objects that are related with the current object or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2008<br />
      */
      function &getRelatedObjects($relationName){

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
      *  @public
      *
      *  Add a related object.<br />
      *
      *  @param string $RelationName name of the desired relation
      *  @return array $Object Object that is related with the current object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.04.2008<br />
      */
      function addRelatedObject($RelationName,&$Object){
         $this->__RelatedObjects[$RelationName][] = &$Object;
       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to set a domain object's simple property.<br />
      *
      *  @param string $PropertyName name of the specified domain object property
      *  @param string $PropertyValue value of the specified domain object property
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      function setProperty($PropertyName,$PropertyValue){
         $this->__Properties[$PropertyName] = $PropertyValue;
       // end function
      }


      /**
      *  @public
      *
      *  Abstract method to get a domain object's simple property.<br />
      *
      *  @param string $PropertyName name of the specified domain object property
      *  @return string $PropertyValue value of the specified domain object property
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      function getProperty($PropertyName){

         if(isset($this->__Properties[$PropertyName])){
            return $this->__Properties[$PropertyName];
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
      *  @param array $Properties list of defined properties to apply to the domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.04.2008<br />
      */
      function setProperties($Properties = array()){

         if(count($Properties) > 0){
            $this->__Properties = $Properties;
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
      function getProperties(){
         return $this->__Properties;
       // end function
      }


      /**
      *  @public
      *
      *  Implements the toString() method for the generic domain object.<br />
      *
      *  @return string $PropertiesString string output of the domain object's properties
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 18.08.2008<br />
      */
      function toString(){
         return printObject(array_merge(array('ObjectName' => $this->__ObjectName),$this->__Properties));
       // end function
      }

    // end class
   }
?>