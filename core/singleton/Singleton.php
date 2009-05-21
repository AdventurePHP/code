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
    * @namespace core::singleton
    * @class Singleton
    * @static
    *
    * Implements the generic singleton pattern. Can be used to create singleton objects from
    * every class. This eases unit tests, because explicit singleton implementations cause side
    * effects during unit testing. As a cache container, the $GLOBALS array is used.
    * Usage:
    * <pre>import('my::namespace','MyClass');
    * $myObject = &Singleton::getInstance('MyClass');</pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2006<br />
    */
   class Singleton {

      private function Singleton(){
      }

      /**
       * @public
       * @static
       *
       * Returns a singleton instance of the given class. In case the object is found in the
       * singleton cache, the cached object is returned.
       *
       * @param string $className The name of the class, that should be created a singleton instance from.
       * @return coreObject The desired object's singleton instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       * Version 0.2, 21.08.2007 (Added check, if the class exists.)<br />
       */
      public static function &getInstance($className){

         if(!Singleton::isInSingletonCache($className)){

            if(!class_exists($className)){
               trigger_error('[Singleton::getInstance()] Class "'.$className.'" cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
               exit(1);
             // end if
            }

            // create instance using the globals array.
            $GLOBALS[Singleton::showCacheContainerOffset()][Singleton::createCacheObjectName($className)] = new $className;

          // end if
         }

         return $GLOBALS[Singleton::showCacheContainerOffset()][Singleton::createCacheObjectName($className)];

       // end function
      }


      /**
       * @public
       * @static
       *
       * Removes the given instance from the cache.
       *
       * @param string $className The name of the singleton class.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       */
      public static function clearInstance($className){
         unset($GLOBALS[Singleton::showCacheContainerOffset()][Singleton::createCacheObjectName($className)]);
       // end function
      }


      /**
       * @public
       * @static
       *
       * Resets the entire singleton cache.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       */
      public static function clearAll(){
         $GLOBALS[Singleton::showCacheContainerOffset()] = array();
       // end function
      }


      /**
       * @protected
       * @static
       *
       * Checks, whether a class is already in the singleton cache.
       *
       * @param string $className The name of the singleton class.
       * @return boolean True in case it is, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       */
      protected static function isInSingletonCache($className){

         if(isset($GLOBALS[Singleton::showCacheContainerOffset()][Singleton::createCacheObjectName($className)])){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
       * @protected
       * @static
       *
       * Creates the class' cache name.
       *
       * @param string $className The name of the singleton class
       * @return string The name of the cache offset.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       */
      protected static function createCacheObjectName($className){
         return strtoupper($className);
       // end function
      }


      /**
       * @public
       * @static
       *
       * Returns the name of the cache container offset within the $GLOBALS array.
       *
       * @return string The name of the cache container offset.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.04.2006<br />
       */
      public static function showCacheContainerOffset(){
         return (string)'SINGLETON_CACHE';
       // end function
      }

    // end class
   }
?>