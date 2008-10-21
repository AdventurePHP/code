<?php
   /**
   *  @package modules::usermanagement::biz
   *  @module umgtBase
   *
   *  Base class for concrete user management domain objects.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.04.2008<br />
   */
   class umgtBase extends coreObject
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


      function umgtBase(){
      }


      /**
      *  @module setProperty()
      *  @public
      *
      *  Abstract method to set a domain object's simple property.<br />
      */
      function setProperty($PropertyName,$PropertyValue){
         $this->__Properties[$PropertyName] = $PropertyValue;
       // end function
      }


      /**
      *  @module getProperty()
      *  @public
      *
      *  Abstract method to get a domain object's simple property.<br />
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
      *  @module setProperties()
      *  @public
      *
      *  Abstract method to set all domain object's simple properties.<br />
      */
      function setProperties($Properties = array()){

         if(count($Properties) > 0){
            $this->__Properties = $Properties;
          // end if
         }

       // end function
      }


      /**
      *  @module getProperties()
      *  @public
      *
      *  Abstract method to get all domain object's simple properties.<br />
      */
      function getProperties(){
         return $this->__Properties;
       // end function
      }

    // end class
   }
?>