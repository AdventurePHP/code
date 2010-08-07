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

   import('tools::cache::provider','AdvancedTextCacheProvider');

   /**
    * @package tools::cache::provider
    * @class AdvancedObjectCacheProvider
    *
    * Implements the cache provider for serialized php objects using the AdvancedTextCacheProvider.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2010<br />
    */
   class AdvancedObjectCacheProvider extends AdvancedTextCacheProvider {

      public function read(CacheKey $cacheKey){

         $content = parent::read($cacheKey);
         if($content === null){
            return null;
          // end if
         }
         else{

            $unserialized = @unserialize($content);
            if($unserialized === false){
               $this->clear($cacheKey);
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

      public function write(CacheKey $cacheKey,$object){
         $serialized = @serialize($object);
         return ($serialized !== false) 
               ? parent::write($cacheKey,$serialized)
               : false;
      }

      public function clear(CacheKey $cacheKey = null){
         return parent::clear($cacheKey);
      }

    // end class
   }
?>