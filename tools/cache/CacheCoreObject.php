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
    * @abstract
    * @package tools::cache
    * @class CacheCoreObject
    *
    * Implements an abstact base class for the cache provider and the cache manager. Includes
    * a generic access method to the cache configuration attributes.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008<br />
    */
   abstract class CacheCoreObject extends coreObject {

      /**
       * @protected
       *
       * Returns the value of the cache config attribute or triggers an error (FATAL), if the
       * attribute is not given within the attributes array.
       *
       * @param string $name name of the desired attribute.
       * @return string Value of the attribute.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.11.2008<br />
       */
      protected function __getCacheConfigAttribute($name){

         if(!isset($this->__Attributes[$name])){
            $reg = &Singleton::getInstance('Registry');
            $env = $reg->retrieve('apf::core','Environment');
            trigger_error('['.get_class($this).'::__getCacheConfigAttribute()] The configuration directive "'.$name.'" is not present or empty. Please check your cache configuration ("'.$env.'_cacheconfig.ini") for namespace "tools::cache" and context "'.$this->__Context.'" or consult the documentation!',E_USER_ERROR);
            exit();
          // end if
         }
         else{
            return $this->__Attributes[$name];
          // end else
         }

       // end function
      }

    // end class
   }
?>