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
namespace APF\tests\suites\core\expression;

/**
 * Dummy model for testing purposes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 30.01.2014<br />
 */
class MethodArgumentsModel {

   public function singleParamCall($true = true) {
      return $true;
   }

   /**
    * @return int The sum of all integer valued applied as arguments.
    */
   public function sumUp() {
      $result = 0;
      foreach (func_get_args() as $arg) {
         $result = $result + $arg;
      }
      return $result;
   }

   public function concatenate() {
      $result = '';
      foreach (func_get_args() as $arg) {
         $result .= $arg;
      }
      return $result;
   }

}
