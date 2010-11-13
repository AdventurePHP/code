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
    * Version 0.3, 28.05.2010 (Bugfix: added method to return the property indicators)<br />
    * Version 0.4, 18.07.2010 (Added "Fluent Interface" support.)<br />
    */
   final class GenericCriterionObject extends APFObject {

      /**
       * @private
       * @var string[] Stores the relation indicators.
       */
      private $__Relations = array();

      /**
       * @private
       * @var string[] Stores the limit indicator.
       */
      private $__Limit = array();

      /**
       * @private
       * @var string[] Stores the property indicator.
       */
      private $__Properties = array();

      /**
       * @private
       * @var string[] Stores the properties to load into the object.
       */
      private $__LoadedProperties = array();

      /**
       * @private
       * @var string[] Stores the order indicator.
       */
      private $__Orders = array();

      /**
       * @public
       *
       * Method to add a relation indicator.
       *
       * @param string $relationName name of the relation between the object in the second argument and the object to load
       * @param GenericDomainObject $sourceObject related object
       *
       * @return GenericCriterionObject Returns itself.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
       */
      public function addRelationIndicator($relationName,$sourceObject){
         $this->__Relations[$relationName] = $sourceObject;
         return $this;
       // end function
      }

      /**
       * @public
       *
       * Returns the relation definitions for the current query.
       *
       * @return string[] The relation definitions.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.04.2010<br />
       */
      public function getRelations(){
         return $this->__Relations;
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
       * @return GenericCriterionObject Returns itself.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
       */
      public function addCountIndicator($startOrCount,$count = null){

         if($count === null){
            $this->__Limit['Count'] = $startOrCount;
          // end if
         }
         else{
            $this->__Limit['Start'] = $startOrCount;
            $this->__Limit['Count'] = $count;
          // end else
         }

         return $this;
       // end function
      }

      /**
       * @public
       *
       * Returns the defined limitations for the current query.
       *
       * @return string[] The limit definition.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.04.2010<br />
       */
      public function getLimitDefinition(){
         return $this->__Limit;
      }

      /**
       * @public
       *
       * Method to add a property to the where list.
       *
       * @param string $attributeName name of the attribute
       * @param string $attributeValue value of the attribute
       * 
       * @return GenericCriterionObject Returns itself.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
       */
      public function addPropertyIndicator($attributeName,$attributeValue){
         $this->__Properties[$attributeName] = $attributeValue;
         return $this;
       // end function
      }

      /**
       * @public
       *
       * Returns the attribute restrictions defined.
       *
       * @return string[] Attribute restrictions for the current query.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.05.2010<br />
       */
      public function getPropertyDefinition(){
         return $this->__Properties;
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
       * @return GenericCriterionObject Returns itself.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.06.2008<br />
       * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
       */
      public function addOrderIndicator($attributeName,$orderDirection = 'ASC'){
         $this->__Orders[$attributeName] = $orderDirection;
         return $this;
       // end function
      }

      /**
       * @public
       *
       * Returns the order indicators for the current query.
       *
       * @return string[] The order indicators.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.04.2010<br />
       */
      public function getOrderIndicators(){
         return $this->__Orders;
      }

      /**
       * @public
       *
       * Method to add a property, that should be loaded into the result object or object list.
       *
       * @param string $propertyName Name of the desired property
       *
       * @return GenericCriterionObject Returns itself.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.06.2008<br />
       * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
       */
      public function addLoadedProperty($propertyName){
         $this->__LoadedProperties[] = $propertyName;
         return $this;
       // end function
      }

      /**
       * @public
       *
       * Return the list of properties to load with the current query.
       *
       * @return string[] The properties to load.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.04.2010<br />
       */
      public function getLoadedProperties(){
         return $this->__LoadedProperties;
      }

    // end class
   }
?>