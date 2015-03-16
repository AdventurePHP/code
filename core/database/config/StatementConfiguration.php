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
namespace APF\core\database\config;

use APF\core\configuration\provider\BaseConfiguration;

/**
 * Implements a configuration abstraction for database statements stored within files.
 * Stored statements are used within the <em>executeStatement()</em> methods of the
 * <em>AbstractDatabaseHandler</em> implementations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.02.2011
 */
class StatementConfiguration extends BaseConfiguration {

   /**
    * The statement content.
    *
    * @var string $statement
    */
   private $statement;

   public function getStatement() {
      return $this->statement;
   }

   public function setStatement($statement) {
      $this->statement = $statement;
   }

   /**
    * @return string[] A list of parameters within the current statement.
    */
   public function getParameterNames() {
      preg_match_all('/\[([A-Za-z0-9_\-]+)\]/u', $this->statement, $matches, PREG_SET_ORDER);
      $paramNames = array();
      for ($i = 0; $i < count($matches); $i++) {
         $paramNames[] = $matches[$i][1];
      }

      return $paramNames;
   }

}
