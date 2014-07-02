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
namespace APF\core\registry;

use InvalidArgumentException;

/**
 * Implements the registry pattern. You can register and retrieve namespace dependent values. The
 * Registry is a static container since 1.12. This is due to performance reasons! Please use
 * <code>Registry::register(...);</code>
 * or
 * <code>Registry::retrieve(...);</code>
 * to manipulate or query registry values.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 19.06.2008<br />
 * Version 0.2, 22.05.2010 (Changed to static container due to performance reasons)<br />
 */
final class Registry {

   /**
    * Stores the registry content.
    *
    * @var string[] $REGISTRY_STORE
    */
   private static $REGISTRY_STORE = array();

   private function __construct() {
   }

   /**
    * Adds a registry value to the registry. If write protection is enabled a warning is displayed.
    *
    * @param string $namespace namespace of the entry.
    * @param string $name name of the entry.
    * @param string $value value of the entry.
    * @param bool $readOnly true (value is read only) | false (value can be changed).
    *
    * @throws InvalidArgumentException In case you try to register a read-only value that has ben registered before.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1,19.06.2008<br />
    */
   public static function register($namespace, $name, $value, $readOnly = false) {

      if (isset(self::$REGISTRY_STORE[$namespace][$name]['readonly']) && self::$REGISTRY_STORE[$namespace][$name]['readonly'] === true) {
         throw new InvalidArgumentException('[Registry::register()] The entry with name "'
               . $name . '" already exists in namespace "' . $namespace . '" and is read only! '
               . 'Please choose another name.', E_USER_WARNING);
      } else {
         self::$REGISTRY_STORE[$namespace][$name]['value'] = $value;
         self::$REGISTRY_STORE[$namespace][$name]['readonly'] = $readOnly;
      }

   }

   /**
    * Retrieves a registry value from the registry.
    *
    * @param string $namespace Namespace of the entry.
    * @param string $name Name of the entry.
    * @param string $default The default value to return, if no key is registered.
    *
    * @return string The desired value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.06.2008<br />
    * Version 0.2, 13.09.2010 (Added support for custom default value definition)<br />
    */
   public static function retrieve($namespace, $name, $default = null) {

      if (isset(self::$REGISTRY_STORE[$namespace][$name]['value'])) {
         return self::$REGISTRY_STORE[$namespace][$name]['value'];
      } else {
         return $default;
      }

   }

}
