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

use APF\core\http\mixins\GetRequestResponse;
use APF\core\http\Session;
use APF\core\pagecontroller\APFObject;
use Exception;
use ReflectionClass;

// ID#178: use closure functions instead of [] to avoid issued with PHP 5.4.x
register_shutdown_function(function () {
   SessionSingleton::saveObjects();
});

/**
 * Implements the generic session singleton pattern. Can be used to create singleton objects
 * from every class. This eases unit tests, because explicit singleton implementations cause
 * side effects during unit testing. As a cache container, the $GLOBALS array is used.
 * <p/>
 * Session singleton objects remain valid throughout multiple requests and within one
 * sessions. They loose their validity when the session ends.
 * <p/>
 * Usage:
 * <pre>$myObject = SessionSingleton::getInstance('VENDOR\..\Class');</pre>
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 24.02.2008<br />
 * Version 0.2, 11.03.2010 (Refactoring to PHP5 only code)<br />
 * Version 0.3, 10.04.2015 (ID#249: introduced constructor injection for object creation)<br />
 */
class SessionSingleton extends Singleton {

   use GetRequestResponse;

   /**
    * The session instance to retrieve the session objects from.
    *
    * @var Session
    */
   private static $SESSION;

   private function __construct() {
   }

   /**
    * Returns a session singleton instance of the given class. In case the object is found
    * in the session singleton cache, the cached object is returned.
    * <p/>
    * In case the instance id parameter is given, the singleton instance is created for
    * this key instead of for the class name itself. This mechanism is needed by the
    * DIServiceManager to create more than one (named) singleton instance of a given
    * class (e.g. two different GenericORRelationMapper instances for your application and
    * the UMGT).
    *
    * @param string $class The name of the class, that should be created a session singleton instance from.
    * @param array $arguments A list of constructor arguments to create the singleton instance with.
    * @param string $instanceId The id of the instance to return.
    *
    * @return APFObject The desired object's singleton instance.
    * @throws Exception In case the implementation class cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2008<br />
    */
   public static function &getInstance($class, array $arguments = [], $instanceId = null) {

      $cacheKey = self::getCacheKey($class, $instanceId);

      if (!isset(self::$CACHE[$cacheKey])) {

         $cachedObject = self::getSession()->load($cacheKey);

         if ($cachedObject === null) {
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

   public static function deleteInstance($class, $instanceId = null) {
      parent::deleteInstance($class, $instanceId);

      // remove from session to not restore instance after in subsequent request by accident
      self::getSession()->delete(self::getCacheKey($class, $instanceId));
   }

   /**
    * @return Session
    */
   private static function getSession() {
      if (self::$SESSION === null) {
         self::$SESSION = self::getRequestStatic()->getSession(__NAMESPACE__);
      }

      return self::$SESSION;
   }

   /**
    * Implements a shutdown function to save all session singleton objects in the session.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2008<br />
    * Version 0.2, 26.02.2008 (Include of the Session was noted wrong)<br />
    * Version 0.3, 11.03.2010 (Refactoring to PHP5 only code)<br />
    * Version 0.4, 07.07.2012 (Included into SessionSingleton class to hide cache from the outside)<br />
    */
   public static function saveObjects() {
      if (count(self::$CACHE) > 0) {
         $session = self::getSession();
         foreach (self::$CACHE as $key => $DUMMY) {
            $session->save($key, serialize(self::$CACHE[$key]));
         }
      }
   }

}
