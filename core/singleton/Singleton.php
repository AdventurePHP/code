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

/**
 * @package core::singleton
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
 * Version 0.2, 11.03.2010 (Refactoring to PHP5 only code)<br />
 */
class Singleton {

   /**
    * Stores the objects, that are requested as singletons.
    * @var APFObject[] The singleton cache.
    */
   private static $CACHE = array();

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Returns a singleton instance of the given class. In case the object is found in the
    * singleton cache, the cached object is returned.
    * <p>
    * In case the instance id parameter is given, the singleton instance is created for
    * this key instead of for the class name itself. This mechanism is needed by the
    * DIServiceManager to create more than one (named) singleton instance of a given
    * class (e.g. two different GenericORRelationMapper instances for your application and
    * the UMGT).
    *
    * @param string $className The name of the class, that should be created a singleton instance from.
    * @param string $instanceId The id of the instance to return.
    * @return APFObject The desired object's singleton instance.
    * @throws \Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2006<br />
    * Version 0.2, 21.08.2007 (Added check, if the class exists.)<br />
    */
   public static function &getInstance($className, $instanceId = null) {

      file_put_contents(__FUNCTION__ . '.log', $className, FILE_APPEND);

      // the cache key is set to the class name for "normal" singleton instances.
      // in case an instance id is given, more than one singleton instance can
      // be created specified by the instance id - but only one per instance id
      // (->SPRING bean creation style).
      $cacheKey = $instanceId === null ? $className : $instanceId;

      if (!isset(self::$CACHE[$cacheKey])) {
         file_put_contents(__FUNCTION__ . '.log', ' --> create: ' . $cacheKey . PHP_EOL, FILE_APPEND);
         self::$CACHE[$cacheKey] = new $className();
      } else {
         file_put_contents(__FUNCTION__ . '.log', ' --> deliver: ' . $cacheKey . PHP_EOL, FILE_APPEND);
      }

      return self::$CACHE[$cacheKey];
   }

}
