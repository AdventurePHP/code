<?php
namespace APF\core\database;

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
 * @package APF\core\database
 * @class Statement
 */
interface Statement {

   const PARAM_STRING = 1;
   const PARAM_INTEGER = 2;
   const PARAM_BLOB = 3;
   const PARAM_FLOAT = 4;

   /**
    * @public
    *
    * Binds a variable to a corresponding named or question mark placeholder in the prepared SQL statement.
    *
    * @param mixed $parameter Name of the parameter if you used named placeholder or the position of the place holder.
    * @param mixed $variable The variable to be bound given by reference.
    * @param int $dataType
    *
    * @throws DatabaseHandlerException
    * @return $this
    */
   public function bindParam($parameter, &$variable, $dataType = self::PARAM_STRING);

   /**
    * @public
    *
    * Binds a value to a corresponding named or question mark placeholder in the prepared SQL statement.
    *
    * @param mixed $parameter name of the parameter if you used named placeholder or the position of the placeholder
    * @param mixed $value the value to be bound
    * @param int $dataType
    *
    * @throws DatabaseHandlerException
    * @return $this
    */
   public function bindValue($parameter, $value, $dataType = self::PARAM_STRING);

   /**
    * @public
    *
    * Binds the values of an associative or numeric array to placeholders in a prepared statement.
    *
    * @param array $params Use an associative array if you have used named placeholders, numeric for question marks
    *
    * @return $this
    */
   public function bindValues(array $params);

   /**
    * @public
    *
    * Executes a prepared statement.
    *
    * @param array $params  Binds the values of the array to the prepared statement (optional). See Statement::bindValues().
    *
    * @throws DatabaseHandlerException
    * @return Result
    */
   public function execute(array $params = array());

}
