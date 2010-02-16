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
    * @class GenericCriterionObject
    *
    * Implements a generic criterion object, that can be used to load a domain object or domain object list.<br />
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.06.2008<br />
    * Version 0.2, 21.06.2008 (Added more indicators)<br />
    */
   final class GenericCriterionObject extends APFObject {

      /**
       * @protected
       * @var string[] Stores the relation indicators.
       */
      protected $__Relations = array();

      /**
       * @protected
       * @var string[] Stores the limit indicator.
       */
      protected $__Limit = array();

      /**
       * @protected
       * @var string[] Stores the property indicator.
       */
      protected $__Properties = array();

      /**
       * @protected
       * @var string[] Stores the properties to load into the object.
       */
      protected $__LoadedProperties = array();

      /**
       * @protected
       * @var string[] Stores the order indicator.
       */
      protected $__Orders = array();

      public function GenericCriterionObject(){
      }

      /**
       * @public
       *
       * Method to add a relation indicator.
       *
       * @param string $relationName name of the relation between the object in the second argument and the object to load
       * @param GenericDomainObject $sourceObject related object
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       */
      function addRelationIndicator($relationName,$sourceObject){
         $this->__Relations[$relationName] = $sourceObject;
       // end function
      }

      /**
       * @public
       *
       * Method to add a limit clause to the criterion object. If the second param is not present,
       * the first param indicates the maximum amount of objects in a list.
       *
       * @param int $startOrCount start pointer
       * @param int $count optional count parameter
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       */
      function addCountIndicator($startOrCount,$count = null){

         if($count === null){
            $this->__Limit['Count'] = $startOrCount;
          // end if
         }
         else{
            $this->__Limit['Start'] = $startOrCount;
            $this->__Limit['Count'] = $count;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Method to add a property to the where list.
       *
       * @param string $attributeName name of the attribute
       * @param string $attributeValue value of the attribute
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       */
      function addPropertyIndicator($attributeName,$attributeValue){
         $this->__Properties[$attributeName] = $attributeValue;
       // end function
      }

      /**
       * @public
       *
       * Method to add a order indicator.
       *
       * @param string $attributeName name of the attribute
       * @param string $orderDirection direction of ordering
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       */
      function addOrderIndicator($attributeName,$orderDirection = 'ASC'){
         $this->__Orders[$attributeName] = $orderDirection;
       // end function
      }

      /**
       * @public
       *
       * Method to add a property, that should be loaded into the result object or object list.
       *
       * @param string $propertyName name of the desired property
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.06.2008<br />
       */
      function addLoadedProperty($propertyName){
         $this->__LoadedProperties[] = $propertyName;
       // end function
      }

    // end class
   }
?>