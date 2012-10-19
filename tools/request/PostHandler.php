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
 * @class PostHandler
 *
 * This component let's you easily retrieve values from the POST-request
 *
 * @author Ralf Schubert
 * @version
 * Version 0.1, 04.03.2011<br />
 */
final class PostHandler {

   /**
    * @public
    * @static
    *
    * Retrieves the desired content from the POST-request. If the offset does not exist, the
    * given default value is taken. Usage:
    * <pre>$value = PostHandler::getValue('foo','bar');</pre>
    *
    * @param string $name name of the POST-request offset
    * @param string $defaultValue the default value
    * @return string The desired value.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 04.03.2011<br />
    * Version 0.2, 19.10.2012 (Bug-fix: "0" values are now considered as an existing value)<br />
    */
   public static function getValue($name, $defaultValue = null) {
      return isset($_POST[$name])
            // avoid issues with "0" values being skipped due to empty() check
            && (!empty($_POST[$name]) || (string)$_POST[$name] === '0')
            ? $_POST[$name]
            : $defaultValue;
   }

   /**
    * @public
    * @static
    *
    * Retrieves the desired values from the POST-request. If one offset does not exist, the
    * given default value or null is taken. Usage:
    * <pre>$values = PostHandler::getValues(array('foo' => 'bar','baz'));</pre>
    *
    * @param array $namesWithDefaults an input array with names and default values
    * @return array The desired values
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 04.03.2011<br />
    */
   public static function getValues($namesWithDefaults) {

      // initialize values
      $values = array();

      // retrieve values from the request
      foreach ($namesWithDefaults as $name => $defaultValue) {

         if (is_int($name)) {
            $values[$defaultValue] = PostHandler::getValue($defaultValue);
         } else {
            $values[$name] = PostHandler::getValue($name, $defaultValue);
         }

      }

      return $values;

   }

}
