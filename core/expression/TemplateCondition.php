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
    * <strong>regExp(&lt;regular expression&gt;)</strong>: whether or not the data matches the given regular expression.
    * </li>
    * <li>
    * <strong>contains(&lt;string&gt;)</strong>: whether or not the data contains the given string.
    * </li>
    * <li>
    * <strong>longerThan(&lt;number&gt;)</strong>: whether or not the data (string) is longer than the given argument.
    * </li>
    * <li>
    * <strong>shorterThan(&lt;number&gt;)</strong>: whether or not the data (string) is shorter than the given argument.
    * </li>
    * <li>
    * <strong>length(&lt;number&gt;)</strong>: whether of not the data (string) matches the length definition.
    * </li>
    * <li>
    * <strong>between(&lt;number&gt;,&lt;number&gt;)</strong>: whether or not the data (string) matches the given range.
    * </li>
    * <li>
    * <strong>lowerThan(&lt;number&gt;)</strong>: whether or not the data (number) is lower than the given argument
    * (numeric, float-based comparison).
    * </li>
    * <li>
    * <strong>equalTo(&lt;number&gt;)</strong>: whether or not the data (number) is the same as the given argument
    * (numeric, float-based comparison).
    * </li>
    * <li>
    * <strong>greaterThan(&lt;number&gt;)</strong>: whether or not the data (number) is greater than the given argument
    * (numeric, float-based comparison).
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
         case strpos($condition, 'regExp') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting regExp(\'#reg-exp#\') for example!'
               );
            }

            // wrap issues with regular expression compilation or execution in exception
            $result = @preg_match($arguments[0], $data);
            if ($result === false) {
               $lastError = error_get_last();
               throw new InvalidArgumentException($lastError['message'], $lastError['type']);
            }

            return 1 === $result;
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

            $length = strlen($data);
            return $length >= intval($arguments[0]) && $length <= intval($arguments[1]);
            break;
         case strpos($condition, 'greaterThan') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting greaterThan(10) for example!'
               );
            }

            return floatval($data) > floatval($arguments[0]);
            break;
         case strpos($condition, 'lowerThan') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting lowerThan(10) for example!'
               );
            }

            return floatval($data) < floatval($arguments[0]);
            break;
         case strpos($condition, 'equalTo') !== false:
            $arguments = self::getArgument($condition);
            if (count($arguments) === 0) {
               throw new InvalidArgumentException(
                     'Condition "' . $condition . '" is missing argument one for comparison! '
                     . 'Expecting greaterThan(10) for example!'
               );
            }

            return floatval($data) == floatval($arguments[0]);
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

      return ArgumentParser::getArguments(substr($condition, $open + 1, $close - $open - 1));
   }

}
