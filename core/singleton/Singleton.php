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
namespace APF\core\singleton;

use APF\core\pagecontroller\APFObject;
use Exception;

/**
 * Implements the generic singleton pattern. Can be used to create singleton objects from
 * every class. This eases unit tests, because explicit singleton implementations cause side
 * effects during unit testing. As a cache container, the $GLOBALS array is used.
 * <p/>
 * Singleton objects remain valid within one requests. They loose their validity with every
 * new request.
 * <p/>
 * Usage:
 * <pre>$myObject = &Singleton::getInstance('VENDOR\..\Class');</pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.04.2006<br />
 * Version 0.2, 11.03.2010 (Refactoring to PHP5 only code)<br />
 */
class Singleton {

   /**
    * Stores the objects, that are requested as singletons.
    *
    * @var string[] $CACHE
    */
   protected static $CACHE = array();

   private function __construct() {
   }

   /**
    * Returns a singleton instance of the given class. In case the object is found in the
    * singleton cache, the cached object is returned.
    * <p/>
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
    * @throws Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2006<br />
    * Version 0.2, 21.08.2007 (Added check, if the class exists.)<br />
    */
   public static function &getInstance($class, $instanceId = null) {
      $cacheKey = self::getCacheKey($class, $instanceId);
      if (!isset(self::$CACHE[$cacheKey])) {
         self::$CACHE[$cacheKey] = new $class();
      }

      return self::$CACHE[$cacheKey];
   }

   /**
    * Destroys a singleton instance of given class.
    * <p/>
    * Provide the instance id parameter to destroy a named instance.
    *
    * @param string $class The name of the class to be created as singleton.
    * @param string $instanceId The id of the instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2015<br />
    */
   public static function deleteInstance($class, $instanceId = null) {
      $cacheKey = self::getCacheKey($class, $instanceId);
      unset(self::$CACHE[$cacheKey]);
   }

   /**
    * The cache key is set to the class name for "normal" singleton instances.
    * in case an instance id is given, more than one singleton instance can
    * be created specified by the instance id - but only one per instance id
    * (->SPRING bean creation style).
    *
    * @param string $class The name of the class to be created as singleton.
    * @param string $instanceId The id of the instance.
    *
    * @return string The instance cache key for the singleton cache.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.01.2015<br />
    */
   protected static function getCacheKey($class, $instanceId) {
      return $instanceId === null ? $class : $instanceId;
   }

}
