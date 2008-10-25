<?php
   /**
   *  @package modules::genericormapper::data
   *  @class GenericCriterionObject
   *
   *  Implements a generic criterion object, that can be used to load a domain object or domain object list.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 17.06.2008<br />
   *  Version 0.2, 21.06.2008 (Added more indicators)<br />
   */
   class GenericCriterionObject extends coreObject
   {

      /**
      *  @private
      *  Stores the relation indicators.
      */
      var $__Relations = array();


      /**
      *  @private
      *  Stores the limit indicator.
      */
      var $__Limit = array();


      /**
      *  @private
      *  Stores the property indicator.
      */
      var $__Properties = array();


      /**
      *  @private
      *  Stores the properties to load into the object.
      */
      var $__LoadedProperties = array();


      /**
      *  @private
      *  Stores the order indicator.
      */
      var $__Orders = array();


      function GenericCriterionObject(){
      }


      /**
      *  @public
      *
      *  Method to add a relation indicator.
      *
      *  @param string $RelationName name of the relation between the object in the second argument and the object to load
      *  @param GenericDomainObject $SourceObject related object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.06.2008<br />
      */
      function addRelationIndicator($RelationName,$SourceObject){
         $this->__Relations[$RelationName] = $SourceObject;
       // end function
      }


      /**
      *  @public
      *
      *  Method to add a limit clause to the criterion object. If the second param is not present,
      *  the first param indicates the maximum amount of objects in a list.
      *
      *  @param int $StartOrCount start pointer
      *  @param int $Count optional count parameter
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.06.2008<br />
      */
      function addCountIndicator($StartOrCount,$Count = null){

         if($Count === null){
            $this->__Limit['Count'] = $StartOrCount;
          // end if
         }
         else{
            $this->__Limit['Start'] = $StartOrCount;
            $this->__Limit['Count'] = $Count;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Method to add a property to the where list.
      *
      *  @param string $AttributeName name of the attribute
      *  @param string $AttributeValue value of the attribute
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.06.2008<br />
      */
      function addPropertyIndicator($AttributeName,$AttributeValue){
         $this->__Properties[$AttributeName] = $AttributeValue;
       // end function
      }


      /**
      *  @public
      *
      *  Method to add a order indicator.
      *
      *  @param string $AttributeName name of the attribute
      *  @param string $OrderDirection direction of ordering
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 17.06.2008<br />
      */
      function addOrderIndicator($AttributeName,$OrderDirection = 'ASC'){
         $this->__Orders[$AttributeName] = $OrderDirection;
       // end function
      }


      /**
      *  @public
      *
      *  Method to add a property, that should be loaded into the result object or object list.
      *
      *  @param string $PropertyName name of the desired property
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.06.2008<br />
      */
      function addLoadedProperty($PropertyName){
         $this->__LoadedProperties[] = $PropertyName;
       // end function
      }

    // end class
   }
?>