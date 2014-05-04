<?php
namespace APF\core\database\mysqli;

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
use APF\core\database\DatabaseHandlerException;
use APF\core\database\Statement;

/**
 * @package APF\core\database\mysqli
 * @class MySQLiStatement
 */
class MySQLiStatement implements Statement {

   protected $paramType = array(
         self::PARAM_BLOB    => 'b',
         self::PARAM_FLOAT   => 'd',
         self::PARAM_INTEGER => 'i',
         self::PARAM_STRING  => 's'
   );

   protected $paramsToBind = array(0 => null);
   protected $typesToBind = array();
   /** @var $statementObject \mysqli_stmt */
   protected $statementObject = null;
   protected $sortedParams = array();

   /**
    * @param \mysqli_stmt $resource
    * @param array $sortedParams
    *
    * @internal param array $params
    */
   public function __construct(\mysqli_stmt $resource, $sortedParams = array()) {
      $this->sortedParams = $sortedParams;
      $this->statementObject = $resource;
   }

   /**
    * @public
    * Executes a prepared Statement
    *
    * @param array $params [optional] binds the values of the array to the prepared statement. See Statement::bindValues()
    *
    * @throws DatabaseHandlerException
    * @return MySQLiResult
    */
   public function execute(array $params = array()) {
      if (!empty($params)) {
         $this->bindValues($params);
      }
      $paramStatementCount = $this->statementObject->param_count;
      $paramCount = count($this->paramsToBind) - 1;
      if ($paramStatementCount !== $paramCount) {
         throw new DatabaseHandlerException('Number '
               . 'of given params (' . $paramCount . ') does not match number of bind params '
               . 'within the statement (' . $paramStatementCount . ')! '
               , E_USER_ERROR);
      }
      $this->reallyBindParams();
      $this->statementObject->execute();
      if ($this->statementObject->field_count !== 0) {
         $result = new MySQLiResult($this->getStoredResult());

         return $result;
      }

      return null;
   }

   /**
    * @public
    * Binds the values of an associative or numeric array to placeholders in a prepared statement
    *
    * @param array $params Use an associative array if you have used named placeholders, numeric for question marks
    *
    * @return $this
    */
   public function bindValues(array $params) {
      if (isset($params[0])) {
         foreach ($params as $key => $value) {
            $this->bindValue($key + 1, $value);
         }
      } else {
         foreach ($params as $key => $value) {
            $this->bindValue($key, $value);
         }
      }

      return $this;
   }

   public function bindValue($param, $value, $dataType = self::PARAM_STRING) {
      return $this->bindParam($param, $value, $dataType);
   }

   public function bindParam($param, &$value, $dataType = self::PARAM_STRING) {
      if (!isset($this->paramType[$dataType])) {
         throw new DatabaseHandlerException('Undefined constant ' . $dataType, E_USER_ERROR);
      }
      if (!empty($this->sortedParams)) {
         $paramKeys = array_keys($this->sortedParams, $param);
         if (empty($paramKeys)) {
            throw new DatabaseHandlerException('Unknown parameter ' . $param);
         }
         foreach ($paramKeys as $key) {
            unset($this->paramsToBind[$key]);
            $this->paramsToBind[$key] = & $value;
            $this->typesToBind[$key] = $this->paramType[$dataType];
         };
      } else {
         unset($this->paramsToBind[$param]);
         $this->paramsToBind[$param] = & $value;
         $this->typesToBind[$param] = $this->paramType[$dataType];
      }

      return $this;
   }

   protected function reallyBindParams() {
      $reflectionMethod = new \ReflectionMethod('mysqli_stmt', 'bind_param');

      $this->paramsToBind[0] = implode('', $this->typesToBind);
      ksort($this->paramsToBind);
      $reflectionMethod->invokeArgs($this->statementObject, $this->paramsToBind);
   }

   /**
    * @return bool|\mysqli_result
    */
   protected function getStoredResult() {
      return $this->statementObject->get_result();
   }

}
