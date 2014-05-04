<?php
namespace APF\core\database\pdo;

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
 * @package APF\core\database\pdo
 * @class PDOStatement
 */
class PDOStatement implements Statement {

   /**
    * @var $statementObject \PDOStatement
    */
   protected $statementObject = null;

   protected $sortedParams = array();
   protected $paramType = array(
         self::PARAM_STRING  => \PDO::PARAM_STR,
         self::PARAM_INTEGER => \PDO::PARAM_INT,
         self::PARAM_BLOB    => \PDO::PARAM_LOB,
         self::PARAM_FLOAT   => \PDO::PARAM_STR
   );

   /**
    * @param \PDOStatement $statementObject
    * @param array $sortedParams
    */
   public function __construct(\PDOStatement $statementObject, $sortedParams = array()) {
      $this->sortedParams = $sortedParams;
      $this->statementObject = $statementObject;
   }

   public function execute(array $params = array()) {
      if (!empty($parameters)) {
         $this->bindValues($parameters);
      }
      $this->statementObject->execute();

      return new PDOResult($this->statementObject);
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
      $this->bindParam($param, $value, $dataType);

      return $this;
   }

   public function bindParam($param, &$value, $dataType = self::PARAM_STRING) {
      if (!isset($this->paramType[$dataType])) {
         throw new DatabaseHandlerException('Undefined constant ' . $dataType, E_USER_ERROR);
      }
      if (!empty($this->sortedParams)) {
         $paramkeys = array_keys($this->sortedParams, $param);
         if (empty($paramkeys)) {
            throw new DatabaseHandlerException('unknown Parameter ' . $param);
         }
         foreach ($paramkeys as $key) {
            $this->statementObject->bindParam($key, $value, $this->paramType[$dataType]);
         };
      } else {
         $this->statementObject->bindParam($param, $value, $this->paramType[$dataType]);
      }

      return $this;
   }

}
