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
   *  @namespace modules::usermanagement::biz
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