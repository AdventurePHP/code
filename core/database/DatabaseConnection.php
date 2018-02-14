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
namespace APF\core\database;

use APF\core\service\APFDIService;

/**
 * This interface defines the structure and functionality of APF database connections.
 *
 * @since 1.15
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.05.2012<br />
 */
interface DatabaseConnection extends APFDIService {

   const ASSOC_FETCH_MODE = 1;
   const OBJECT_FETCH_MODE = 2;
   const NUMERIC_FETCH_MODE = 3;

   /**
    * Setups the connection using the DIServiceManager
    */
   public function setup();

   /**
    * Executes a statement, located within a statement file. The place holders contained in the
    * file are replaced by the given values.
    *
    * @param string $namespace Namespace of the statement file.
    * @param string $statementName Name of the statement file (filebody!).
    * @param string[] $params A list of statement parameters.
    * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
    *
    * @return resource The database result resource.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   public function executeStatement($namespace, $statementName, array $params = [], $logStatement = false);

   /**
    * Executes a statement applied as a string to the method and returns the
    * result pointer.
    *
    * @param string $statement The statement string.
    * @param boolean $logStatement Indicates, whether the given statement should be
    *                              logged for debug purposes.
    *
    * @return resource The database result resource.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   public function executeTextStatement($statement, $logStatement = false);

   /**
    * Fetches a record from the database using the given result resource.
    *
    * @param resource $resultCursor The result resource returned by executeStatement() or executeTextStatement().
    * @param int $type The type the returned data should have. Use the static *_FETCH_MODE constants.
    *
    * @return string[]|false The associative result array. Returns false if no row was found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.09.2009<br />
    * Version 0.2, 08.08.2010 (Added optional second parameter) <br />
    */
   public function fetchData($resultCursor, $type = self::ASSOC_FETCH_MODE);

   /**
    * Escapes given values to be SQL injection save.
    *
    * @param string $value The un-escaped value.
    *
    * @return string The escaped string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 23.02.2008<br />
    */
   public function escapeValue($value);

   /**
    * Returns the amount of rows, that are affected by a previous update or delete call.
    *
    * @param resource $resultCursor The result resource pointer.
    *
    * @return int The number of affected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.02.2008<br />
    */
   public function getAffectedRows($resultCursor);

   /**
    * Returns the number of selected rows by the given result resource.
    *
    * @param resource $result The result resource.
    *
    * @return int The number of selected rows.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.03.2011 (Added missing interface method.)<br />
    */
   public function getNumRows($result);

}
