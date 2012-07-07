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
import('core::session', 'SessionManager');
register_shutdown_function(array('SessionSingleton', 'saveObjects'));

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

   const SESSION_NAMESPACE = 'core::singleton::session';

   /**
    * Stores the objects, that are requested as singletons.
    * @var string[] The singleton cache.
    */
   private static $CACHE = array();

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Returns a session singleton instance of the given class. In case the object is found
    * in the session singleton cache, the cached object is returned.
    * <p>
    * In case the instance id parameter is given, the singleton instance is created for
    * this key instead of for the class name itself. This mechanism is needed by the
    * DIServiceManager to create more than one (named) singleton instance of a given
    * class (e.g. two different GenericORRelationMapper instances for your application and
    * the UMGT).
    *
    * @param string $className The name of the class, that should be created a session singleton instance from.
    * @param string $instanceId The id of the instance to return.
    * @return APFObject The desired object's singleton instance.
    * @throws Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2008<br />
    */
   public static function &getInstance($className, $instanceId = null) {

      // the cache key is set to the class name for "normal" singleton instances.
      // in case an instance id is given, more than one singleton instance can
      // be created specified by the instance id - but only one per instance id
      // (->SPRING bean creation style).
      $cacheKey = $instanceId === null ? $className : $instanceId;

      if (!isset(self::$CACHE[$cacheKey])) {

         $sessMgr = new SessionManager(SessionSingleton::SESSION_NAMESPACE);
         $cachedObject = $sessMgr->loadSessionData($cacheKey);

         if ($cachedObject !== null) {
            self::$CACHE[$cacheKey] = unserialize($cachedObject);
         } else {
            if (!class_exists($className)) {
               throw new Exception('[SessionSingleton::getInstance()] Class "' . $className . '" '
                     . 'cannot be found! Maybe the class name is misspelt!', E_USER_ERROR);
            }

            self::$CACHE[$cacheKey] = new $className();
         }

      }

      return self::$CACHE[$cacheKey];
   }

   /**
    * @public
    * @static
    *
    * Implements a shutdown function to save all session singleton objects in the session.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2008<br />
    * Version 0.2, 26.02.2008 (Include of the SessionManager was noted wrong)<br />
    * Version 0.3, 11.03.2010 (Refactoring to PHP5 only code)<br />
    * Version 0.4, 07.07.2012 (Included into SessionSingleton class to hide cache from the outside)<br />
    */
   public static function saveObjects() {
      if (count(self::$CACHE) > 0) {
         $sessMgr = new SessionManager(SessionSingleton::SESSION_NAMESPACE);

         foreach (self::$CACHE as $key => $DUMMY) {
            $sessMgr->saveSessionData($key, serialize(self::$CACHE[$key]));
         }
      }
   }

}
