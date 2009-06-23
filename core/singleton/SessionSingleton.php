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

   import('core::session','SessionManager');
   register_shutdown_function('saveSessionSingletonObjects');

   /**
    * @namespace core::singleton
    *
    * Implements a shutdown function to save all session singleton objects in the session.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2008<br />
    * Version 0.2, 26.02.2008 (Include of the SessionManager was noted wrong)<br />
    */
   function saveSessionSingletonObjects(){

      $cacheContainer = SessionSingleton::showCacheContainerOffset();

      if(isset($GLOBALS[$cacheContainer])){
         $cacheCount = count($GLOBALS[$cacheContainer]);

         if($cacheCount > 0){
            $sessMgr = new SessionManager(SessionSingleton::showSessionNamespace());

            foreach($GLOBALS[$cacheContainer] as $key => $DUMMY){
               $sessMgr->saveSessionData($key,serialize($GLOBALS[$cacheContainer][$key]));
             // end for
            }

          // end if
         }

       // end if
      }

    // end function
   }


   /**
    * @namespace core::singleton
    * @class SessionSingleton
    * @static
    *
    * Implements the generic session singleton pattern. Can be used to create singleton objects
    * from every class. This eases unit tests, because explicit singleton implementations cause
    * side effects during unit testing. As a cache container, the $GLOBALS array is used.
    * Usage:
    * <pre>import('my::namespace','MyClass');
    * $myObject = &SessionSingleton::getInstance('MyClass');</pre>
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2008<br />
    */
   class SessionSingleton extends Singleton {

      private function SessionSingleton(){
      }


      /**
       * @public
       * @static
       *
       * Returns a session singleton instance of the given class. In case the object is found
       * in the session singleton cache, the cached object is returned.
       *
       * @param string $className The name of the class, that should be created a session singleton instance from.
       * @return coreObject The desired object's singleton instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       */
      public static function &getInstance($className){

         $cacheContainer = SessionSingleton::showCacheContainerOffset();
         $cacheObjectName = SessionSingleton::createCacheObjectName($className);

         if(!SessionSingleton::isInSingletonCache($className)){

            $sessMgr = new SessionManager(SessionSingleton::showSessionNamespace());
            $cachedObject = $sessMgr->loadSessionData($cacheObjectName);

            if($cachedObject !== false){
               $GLOBALS[$cacheContainer][$cacheObjectName] = unserialize($cachedObject);
             // end if
            }
            else{

               if(!class_exists($className)){
                  trigger_error('[SessionSingleton::getInstance()] Class "'.$className.'" cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
                  exit(1);
                // end if
               }

               $GLOBALS[$cacheContainer][$cacheObjectName] = new $className();

             // end else
            }

          // end if
         }

         return $GLOBALS[$cacheContainer][$cacheObjectName];

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
       *  Version 0.1, 24.02.2008<br />
       */
      public static function clearInstance($className){
         unset($GLOBALS[SessionSingleton::showCacheContainerOffset()][SessionSingleton::createCacheObjectName($className)]);
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
       * Version 0.1, 24.02.2008<br />
       */
      public static function clearAll(){
         $GLOBALS[SessionSingleton::showCacheContainerOffset()] = array();
       // end function
      }


      /**
       * @public
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
      public static function isInSingletonCache($className){

         if(isset($GLOBALS[SessionSingleton::showCacheContainerOffset()][SessionSingleton::createCacheObjectName($className)])){
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
       * @public
       * @static
       *
       * Returns the name of the cache container offset within the $GLOBALS array.
       *
       * @return string The name of the cache container offset.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       */
      public static function showCacheContainerOffset(){
         return (string)'SESSION_SINGLETON_CACHE';
       // end function
      }


      /**
       * @public
       * @static
       *
       * Returns the namespace of the session cache for the session manager.
       *
       * @return string The namespace of the session singleton cache namespace in the session.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       */
      public static function showSessionNamespace(){
         return (string)'core::sessionsingleton';
       // end function
      }

    // end class
   }
?>