<?php
namespace APF\tools\request;

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

/**
 * This component let's you easily retrieve values from the request.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.12.2008<br />
 */
final class RequestHandler {

   /**
    * @const string Defines to use only content from the $_GET super-global.
    */
   const USE_GET_PARAMS = 'GET';

   /**
    * @const string Defines to use only content from the $_POST super-global.
    */
   const USE_POST_PARAMS = 'POST';

   /**
    * @const string Defines to use only content from the $_REQUEST super-global (default behaviour).
    */
   const USE_REQUEST_PARAMS = 'REQUEST';

   private function RequestHandler() {
   }

   /**
    * Retrieves the desired content from the request. If the request offset does not exist, the
    * given default value is taken. Usage:
    * <pre>$value = RequestHandler::getValue('foo','bar');</pre>
    *
    * @param string $name name of the request offset
    * @param string $defaultValue the default value
    * @param string $type The type of parameter set to request.
    *
    * @return string The desired value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.12.2008<br />
    * Version 0.2, 10.08.2009 (Empty values are now treated as non existing values as well)<br />
    * Version 0.3, 19.10.2012 (Bug-fix: "0" values are now considered as an existing value)<br />
    */
   public static function getValue($name, $defaultValue = null, $type = self::USE_REQUEST_PARAMS) {
      $lookupTable = $GLOBALS['_' . $type];

      return isset($lookupTable[$name])
      // avoid issues with "0" values being skipped due to empty() check
      && (!empty($lookupTable[$name]) || (string) $lookupTable[$name] === '0')
            ? $lookupTable[$name]
            : $defaultValue;
   }

   /**
    * Retrieves the desired values from the request. If one request offset does not exist, the
    * given default value or null is taken. Usage:
    * <pre>$values = RequestHandler::getValues(array('foo' => 'bar','baz'));</pre>
    *
    * @param array $namesWithDefaults an input array with names and default values.
    * @param string $type The type of parameter set to request.
    *
    * @return array The desired values.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.12.2008<br />
    */
   public static function getValues(array $namesWithDefaults, $type = self::USE_REQUEST_PARAMS) {

      $values = array();

      foreach ($namesWithDefaults as $name => $defaultValue) {
         if (is_int($name)) { // in case $name is numeric, we got applied an array with numeric keys and no default values!
            $values[$defaultValue] = self::getValue($defaultValue, null, $type);
         } else {
            $values[$name] = self::getValue($name, $defaultValue, $type);
         }

      }

      return $values;
   }

}
