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
namespace APF\core\expression;

use InvalidArgumentException;

/**
 * Implements a template-based condition definition evaluation. Allows to define expressions to be
 * matched against data to display content only under certain circumstances (e.g. data not empty).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.02.2016 (ID#283: added first version of conditions)<br />
 */
class TemplateCondition {

   /**
    * Allows evaluation of the following conditions:
    * <ul>
    * <li>
    * <strong>true()</strong>: whether or not the data passed is bool true.
    * </li>
    * <li>
    * <strong>false()</strong>: whether or not the data passed is bool false.
    * </li>
    * <li>
    * <strong>empty()</strong>: whether or not the data is empty.
    * </li>
    * <li>
    * <strong>notEmpty()</strong>: whether or not the data is not empty.
    * </li>
    * <li>
    * <strong>matches(&lt;string&gt;)</strong>: whether or not the data matches the given string or number.
    * </li>
    * <li>
    * <strong>contains(&lt;string&gt;)</strong>: whether or not the data contains the given string.
    * </li>
    * <li>
    * <strong>longerThan(&lt;number&gt;)</strong>: whether or not the data (string) is longer than.
    * </li>
    * <li>
    * <strong>shorterThan(&lt;number&gt;)</strong>: whether or not the data (string) is shorter than.
    * </li>
    * <li>
    * <strong>length(&lt;number&gt;)</strong>: whether of not the data (string) matches the length definition.
    * </li>
    * <li>
    * <strong>between(&lt;number&gt;,&lt;number&gt;)</strong>: whether or not the data (string) matches the given range.
    * </li>
    * </ul>
    *
    * @param string $condition The condition to evaluate.
    * @param mixed $data The data to match against the condition.
    *
    * @return bool <em>True</em> in case the condition matches, <em>false</em> otherwise.
    *
    * @throws InvalidArgumentException In case the given condition is invalid.
    */
   public static function applies($condition, $data) {
      switch ($condition) {
         case strpos($condition, 'true') !== false:
            return $data === true;
            break;
         case strpos($condition, 'false') !== false:
            return $data === false;
            break;
         case strpos($condition, 'empty') !== false:
            return empty(trim($data));
            break;
         case strpos($condition, 'notEmpty') !== false:
            return !empty($data);
            break;
         case strpos($condition, 'matches') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting matches(\'foo\') for example!'
               );
            }

            return $data == $arguments[0];
            break;
         case strpos($condition, 'contains') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting contains(\'foo\') for example!'
               );
            }

            return stripos($data, $arguments[0]) !== false;
            break;
         case strpos($condition, 'longerThan') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting longerThan(3) for example!'
               );
            }

            return strlen($data) > intval($arguments[0]);
            break;
         case strpos($condition, 'shorterThan') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting shorterThan(10) for example!'
               );
            }

            return strlen($data) < intval($arguments[0]);
            break;
         case strpos($condition, 'length') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting length(5) for example!'
               );
            }

            return strlen($data) == intval($arguments[0]);
            break;
         case strpos($condition, 'between') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) < 2) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one and/or two for comparison! '
                     . 'Expecting between(5,10) for example!'
               );
            }

            return strlen($data) >= intval($arguments[0]) && strlen($data) <= intval($arguments[1]);
            break;
         default:
            return false;
            break;
      }
   }

   /**
    * @param string $condition The condition definition.
    *
    * @return array List of arguments.
    */
   protected static function getArgument($condition) {
      $open = strpos($condition, '(');
      $close = strrpos($condition, ')', $open);

      $expression = trim(substr($condition, $open + 1, $close - $open - 1));

      if (empty($expression) && strval($expression) !== '0') {
         return [];
      }

      return explode(',', str_replace('\'', '', str_replace(' ', '', $expression)));
   }

}
