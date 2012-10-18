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

/**
 * @package tools::request
 * @class RequestHandler
 *
 * This component let's you easily retrieve values from the request.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.12.2008<br />
 */
final class RequestHandler {

   private function RequestHandler() {
   }

   /**
    * @public
    * @static
    *
    * Retrieves the desired content from the request. If the request offset does not exist, the
    * given default value is taken. Usage:
    * <pre>$value = RequestHandler::getValue('foo','bar');</pre>
    *
    * @param string $name name of the request offset
    * @param string $defaultValue the default value
    * @return string The desired value.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.12.2008<br />
    * Version 0.2, 10.08.2009 (Empty values are now treated as non existing values as well)<br />
    */
   static function getValue($name, $defaultValue = null) {

      if (isset($_REQUEST[$name]) && !empty($_REQUEST[$name])) {
         $value = $_REQUEST[$name];
      } else {
         $value = $defaultValue;
      }
      return $value;
   }

   /**
    * @public
    * @static
    *
    * Retrieves the desired values from the request. If one request offset does not exist, the
    * given default value or null is taken. Usage:
    * <pre>$values = RequestHandler::getValues(array('foo' => 'bar','baz'));</pre>
    *
    * @param array $namesWithDefaults an input array with names and default values
    * @return array The desired values
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.12.2008<br />
    */
   static function getValues($namesWithDefaults) {

      $values = array();

      foreach ($namesWithDefaults as $name => $defaultValue) {

         if (is_int($name)) {
            $values[$defaultValue] = RequestHandler::getValue($defaultValue);
         } else {
            $values[$name] = RequestHandler::getValue($name, $defaultValue);
         }

      }

      return $values;

   }

}
