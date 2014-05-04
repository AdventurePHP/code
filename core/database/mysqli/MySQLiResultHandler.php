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
use APF\core\database\Result;

/**
 * @package APF\core\database\mysqli
 * @class MySQLiResult
 */
class MySQLiResult implements Result {

   /* @var $resultObject \mysqli_result */
   protected $resultObject = null;

   /**
    * @param \mysqli_result $resource
    */
   public function __construct(\mysqli_result $resource) {
      $this->result = $resource;
   }

   /**
    * @public
    * frees up the connection so that a new statement can be executed
    */
   public function freeResult() {
      $this->result->free_result();
   }

   /**
    * @public
    *
    * Fetches all records from the database .
    *
    * @param int $type The type the returned data should have. Use the static FETCH_* constants.
    *
    * @return array An multidimensional result array.
    *
    * @author dingsda
    * @version
    * Version 0.1, 08.04.2014<br />
    */
   public function fetchAll($type = self::FETCH_ASSOC) {
      if (!method_exists($this->result, 'fetch_all') || $type == self::FETCH_OBJECT) {
         $data = array();
         while ($row = $this->fetchData($type)) {
            $data[] = $row;
         }

         return $data;
      }
      if ($type === self::FETCH_ASSOC) {
         return $this->result->fetch_all(MYSQLI_ASSOC);
      } else {
         return $this->result->fetch_all(MYSQLI_NUM);
      }
   }

   /**
    * @public
    *
    * Fetches a record from the database.
    *
    * @param int $type The type the returned data should have. Use the static FETCH_* constants.
    *
    * @return mixed The result array. Returns false if no row was found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.09.2009<br />
    * Version 0.2, 08.08.2010 (Added optional second parameter) <br />
    */
   public function fetchData($type = self::FETCH_ASSOC) {
      switch ($type) {
         case self::FETCH_ASSOC:
            return $this->result->fetch_assoc();
            break;
         case self::FETCH_NUMERIC:
            return $this->result->fetch_row();
            break;
         case self::FETCH_OBJECT;
            return $this->result->fetch_object();
            break;
      }
      return false;
   }

   /**
    * @public
    *
    * Returns the number of selected rows by a select Statement.
    * Some databases do not support this so you should not relied on
    * this behavior for portable applications.
    *
    * @return int The number of selected rows.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function getNumRows() {
      return $this->result->num_rows;
   }

}