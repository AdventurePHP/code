<?php
namespace APF\core\singleton;

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
use APF\core\pagecontroller\APFObject;

// ID#178: use closure functions instead of array() to avoid issued with PHP 5.4.x
register_shutdown_function(function () {
   ApplicationSingleton::saveObjects();
});

/**
 * @package APF\core\singleton
 * @class ApplicationSingleton
 * @static
 *
 * Implements the generic application singleton pattern. Can be used to create singleton objects from
 * every class. This eases unit tests, because explicit singleton implementations cause side
 * effects during unit testing.
 * <p/>
 * Application singleton objects remain valid throughout multiple requests and sessions and only loose
 * their validity when the web server is restarted.
 * <p/>
 * Usage:
 * <pre>$instance = &ApplicationSingleton::getInstance('VENDOR\..\Class');</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.07.2013<br />
 */
class ApplicationSingleton {

   /**
    * @var string[] Stores the objects, that are requested as singletons.
    */
   private static $CACHE = array();

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Returns an application singleton instance of the given class. In case the object is found in the
    * singleton cache, the cached object is returned.
    * <p>
    * In case the instance id parameter is given, the singleton instance is created for
    * this key instead of for the class name itself. This mechanism is needed by the
    * DIServiceManager to create more than one (named) singleton instance of a given
    * class (e.g. two different GenericORRelationMapper instances for your application and
    * the UMGT).
    *
    * @param string $class The name of the class, that should be created a singleton instance from.
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFObject The desired object's singleton instance.
    * @throws \Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.07.2013<br />
    */
   public static function &getInstance($class, $instanceId = null) {

      // the cache key is set to the class name for "normal" application singleton
      // instances. in case an instance id is given, more than one application
      // singleton instance can be created specified by the instance id - but only
      // one per instance id (->SPRING bean creation style).
      $cacheKey = $instanceId === null ? $class : $instanceId;

      // prepend class including namespace to cache key to avoid collisions within the APC store
      $cacheKey = __CLASS__ . '#' . $cacheKey;

      if (!isset(self::$CACHE[$cacheKey])) {

         $cachedObject = apc_fetch($cacheKey);

         if ($cachedObject === false) {
            // no class check needed due to auto loading!
            self::$CACHE[$cacheKey] = new $class();
         } else {
            self::$CACHE[$cacheKey] = unserialize($cachedObject);
         }
      }

      return self::$CACHE[$cacheKey];
   }

   /**
    * @public
    * @static
    *
    * Implements a shutdown function to save all application singleton objects to the APC store.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.07.2013<br />
    */
   public static function saveObjects() {
      if (count(self::$CACHE) > 0) {
         foreach (self::$CACHE as $key => $DUMMY) {
            // storage includes serialization to hide object storage mechanisms for users
            apc_store($key, serialize(self::$CACHE[$key]));
         }
      }
   }

}