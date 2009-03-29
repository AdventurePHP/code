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

   import('tools::cache::provider','TextCacheProvider');


   /**
   *  @class ObjectCacheProvider
   *
   *  Implements the cache provider for serialized php objects.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   *  Version 0.2, 23.11.2008 (The reader now inherits from the TextCacheReader because of same functionalities)<br />
   *  Version 0.3, 24.11.2008 (Refactoring tue to provider introduction.)<br />
   */
   class ObjectCacheProvider extends TextCacheProvider
   {

      function ObjectCacheProvider(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheKey the application's cache key
      *  @return object $object desired object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 22.11.2008 (Refactoring due to global changes)<br />
      *  Version 0.3, 24.11.2008 (Refactoring tue to provider introduction.)<br />
      */
      function read($cacheKey){

         $content = parent::read($cacheKey);
         if($content === null){
            return null;
          // end if
         }
         else{

            $unserialized = @unserialize($content);
            if($unserialized === false){
               return null;
             // end if
            }
            else{
               return $unserialized;
             // end else
            }

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheKey the application's cache key
      *  @param string $cacheFile fully qualified cache file name
      *  @param object $object desired object to serialize
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 23.11.2008 (Adapted to the new reader/writer strategy)<br />
      *  Version 0.3, 24.11.2008 (Refactoring tue to provider introduction.)<br />
      */
      function write($cacheKey,$object){

         $serialized = @serialize($object);
         if($serialized !== false){
            return parent::write($cacheKey,$serialized);
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implements the provider's clear() method. Uses the parent class' method.
      *
      *  @param string $cacheKey the application's cache key or null (clear entire cache namespace)
      *  @return string $result true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.11.2008<br />
      */
      function clear($cacheKey = null){
         return parent::clear($cacheKey);
       // end function
      }

    // end class
   }
?>