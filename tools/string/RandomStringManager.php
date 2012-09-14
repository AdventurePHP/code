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

/**
 * @package tools::string
 * @class RandomStringManager
 *
 * This class creates a random string.
 *
 * @author dave
 * @version
 * Version 0.1, 07.09.2011<br />
 */
class RandomStringManager extends APFObject {

   private $chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   private $lenght = 16;
   private $randomString;

   /**
    * @public
    *
    * Converting characters into UTF-8 conform format (for example: §%#).
    *
    * @param string $string The characters to convert.
    * @return array The converted characters as array.
    *
    * @author dave
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   private function mbStringToArray($string) {
      $strLen = mb_strlen($string);
      $array = array();
      while ($strLen) {
         $array[] = mb_substr($string, 0, 1, "UTF-8");
         $string = mb_substr($string, 1, $strLen, "UTF-8");
         $strLen = mb_strlen($string);
      }
      return $array;
   }

   /**
    * @public
    *
    * Initializes the component.
    *
    * @param array $initParam The parameter to use for creating a random string.
    *
    * @author dave
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function init($initParam) {
      if ($initParam['chars'] === '') {
         $this->chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      } else {
         $this->chars = $initParam['chars'];
      }
      if (empty($initParam['lenght'])) {
         $this->lenght = (int)16;
      } else {
         $this->lenght = (int)$initParam['lenght'];
      }

      $this->chars = $this->mbStringToArray($this->chars);
   }

   /**
    * @public
    *
    * Creates a RandomString using the parameters set by init-function.
    *
    * @return string The created random string.
    *
    * @author dave
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function createHash() {
      for ($i = 0; $i < $this->lenght; $i++) {
         $this->randomString .= $this->chars[mt_rand(0, count($this->chars) - 1)];
      }
      return $this->randomString;
   }

   /**
    * @public
    *
    * Creates a RandomString using the parameters set by init-function
    * until there was no corresponding string in the database found.
    *
    * @param string $select The SQL query to check, if the string is already in use.
    * @param string $connectionKey The database connection key.
    * @return string The created RandomString
    *
    * @author dave
    * @version
    * Version 0.1, 07.09.2011<br />
    */
   public function advancedCreateHash($select, $connectionKey) {
      $this->randomString = '';

      if (!$select) {
         throw new InvalidArgumentException('[RandomStringManager::advancedCreateHash()] You must provide a SQL query!', E_USER_ERROR);
      }

      if (!$connectionKey) {
         throw new InvalidArgumentException('[RandomStringManager::advancedCreateHash()] You must provide a ConnectionKey for the SQL Statement!', E_USER_ERROR);
      }

      $cM = &$this->getServiceObject('core::database', 'ConnectionManager');
      /* @var $cM ConnectionManager */
      $conn = &$cM->getConnection($connectionKey);
      /* @var $conn AbstractDatabaseHandler */

      $hash = $this->createHash();
      $hash = $conn->escapeValue($hash);
      $selection = $select . "'$hash'";
      $result = $conn->executeTextStatement($selection);

      while ($conn->getNumRows($result) > 0) {
         $this->advancedCreateHash($select, $connectionKey);
         break;
      }

      return $this->randomString;
   }

}
