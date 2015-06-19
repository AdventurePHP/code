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

/**
 * Includes basic functionality common to all APF expression implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.01.2014<br />
 */
abstract class EvaluationExpressionBase {

   /**
    * The expression to execute.
    *
    * @var string $expression
    */
   protected $expression;

   /**
    * The previous expression execution result (e.g. an object or array).
    *
    * @var mixed $previousResult
    */
   protected $previousResult;

   /**
    * @param string $expression The expression to execute.
    * @param mixed $previousResult The previous expression execution result (e.g. an object or array).
    */
   public function __construct($expression, $previousResult) {
      $this->expression = $expression;

      // check result according to executed evaluation
      $this->check($expression, $previousResult);
      $this->previousResult = $previousResult;
   }

   /**
    * Checks the input parameters according to implementation needs.
    *
    * @param string $expression The expression to execute.
    * @param mixed $previousResult The previous expression execution result (e.g. an object or array).
    */
   abstract protected function check($expression, $previousResult);

}
