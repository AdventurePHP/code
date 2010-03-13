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

   import('core::session','SessionManager');
   register_shutdown_function('saveSessionSingletonObjects');

   /**
    * @package core::singleton
    *
    * Implements a shutdown function to save all session singleton objects in the session.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2008<br />
    * Version 0.2, 26.02.2008 (Include of the SessionManager was noted wrong)<br />
    * Version 0.3, 11.03.2010 (Refactoring to PHP5 only code)<br />
    */
   function saveSessionSingletonObjects(){

      $cacheItems = SessionSingleton::getCacheItems();

      if(count($cacheItems) > 0){

         $sessMgr = new SessionManager(SessionSingleton::getSessionNamespace());

         foreach($cacheItems as $key => $DUMMY){
            $sessMgr->saveSessionData($key,serialize($cacheItems[$key]));
         }

      }

    // end function
   }

   /**
    * @package core::singleton
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
    * Version 0.2, 11.03.2010 (Refactoring to PHP5 only code)<br />
    */
   class SessionSingleton extends Singleton {

      /**
       * Stores the objects, that are requested as singletons.
       * @var string[] The singleton cache.
       */
      private static $CACHE = array();

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
       * @return APFObject The desired object's singleton instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       */
      public static function &getInstance($className){

         $cacheKey = SessionSingleton::createCacheObjectName($className);

         if(!isset(self::$CACHE[$cacheKey])){

            $sessMgr = new SessionManager(SessionSingleton::getSessionNamespace());
            $cachedObject = $sessMgr->loadSessionData($cacheKey);

            if($cachedObject !== null){
               self::$CACHE[$cacheKey] = unserialize($cachedObject);
            }
            else{
               if(!class_exists($className)){
                  throw new Exception('[SessionSingleton::getInstance()] Class "'.$className.'" '
                          .'cannot be found! Maybe the class name is misspelt!',E_USER_ERROR);
               }

               self::$CACHE[$cacheKey] = new $className();

             // end else
            }

          // end if
         }

         return self::$CACHE[$cacheKey];

       // end function
      }

      /**
       * @public
       *
       * Returns the content of the session singleton cache to
       * be stored within the session.
       *
       * @return string[] The session singleton cache items.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.03.2010<br />
       */
      public static function getCacheItems(){
         return self::$CACHE;
      }

      /**
       * @public
       *
       * Returns the namespace of the session, that the session singelton
       * objects are stored in (needed by the shutdown function; thus public).
       *
       * @return string The session namespace, where the session singleton objects are saved.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 11.03.2010<br />
       */
      public static function getSessionNamespace(){
         return (string)'core::singleton::session';
      }

    // end class
   }
?>