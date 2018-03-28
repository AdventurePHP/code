<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\core\singleton;

use APF\core\pagecontroller\APFObject;
use Exception;
use ReflectionClass;

// ID#178: use closure functions instead of [] to avoid issued with PHP 5.4.x
register_shutdown_function(function () {
   ApplicationSingleton::saveObjects();
});

/**
 * Implements the generic application singleton pattern. Can be used to create singleton objects from
 * every class. This eases unit tests, because explicit singleton implementations cause side
 * effects during unit testing.
 * <p/>
 * Application singleton objects remain valid throughout multiple requests and sessions and only loose
 * their validity when the web server is restarted.
 * <p/>
 * Usage:
 * <pre>$instance = ApplicationSingleton::getInstance('VENDOR\..\Class');</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 17.07.2013<br />
 * Version 0.2, 10.04.2015 (ID#249: introduced constructor injection for object creation)<br />
 */
class ApplicationSingleton extends Singleton {

   /**
    * Stores the objects, that are requested as singletons.
    *
    * @var mixed $CACHE
    */
   private static $CACHE = [];

   private function __construct() {
   }

   /**
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
    * @param array $arguments A list of constructor arguments to create the singleton instance with.
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFObject The desired object's singleton instance.
    * @throws Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.07.2013<br />
    */
   public static function getInstance(string $class, array $arguments = [], string $instanceId = null) {

      $cacheKey = self::getCacheKey($class, $instanceId);

      if (!isset(self::$CACHE[$cacheKey])) {

         $cachedObject = apcu_fetch($cacheKey);

         if ($cachedObject === false) {
            // no class check needed due to auto loading!
            if (count($arguments) > 0) {
               self::$CACHE[$cacheKey] = (new ReflectionClass($class))->newInstanceArgs($arguments);
            } else {
               self::$CACHE[$cacheKey] = new $class;
            }
         } else {
            self::$CACHE[$cacheKey] = unserialize($cachedObject);
         }
      }

      return self::$CACHE[$cacheKey];
   }

   protected static function getCacheKey(string $class, string $instanceId = null) {
      $cacheKey = parent::getCacheKey($class, $instanceId);

      // prepend class including namespace to cache key to avoid collisions within the APC store
      return __CLASS__ . '#' . $cacheKey;
   }

   public static function deleteInstance(string $class, string $instanceId = null) {

      // remove from local cache
      $cacheKey = self::getCacheKey($class, $instanceId);
      unset(self::$CACHE[$cacheKey]);

      // remove from APC store to not restore instance after in subsequent request by accident
      apcu_delete(self::getCacheKey($class, $instanceId));
   }

   /**
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
            apcu_store($key, serialize(self::$CACHE[$key]));
         }
      }
   }

}
