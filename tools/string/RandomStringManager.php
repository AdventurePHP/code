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
 * Version 0.2, 17.10.2012 (Added support to create serial numbers)<br />
 */
class RandomStringManager extends APFObject {

   private $chars;
   private $lenght;
   private $randomString;
   private $sheme;
   private $delimiter;

   /**
    * @pubic
    * 
    * @param type $chars Chars set by user via config in *_serviceobjects.ini
    */
   public function setChars($chars) {
      $this->chars = $chars;
   }

   /**
    * @public
    * 
    * @param type $lenght Lenght set by user via config in *_serviceobjects.ini
    */
   public function setLength($lenght) {
      $this->lenght = (int) $lenght;
   }

   /**
    * @public
    * 
    * @param type $connectionKey Connection key set by user via config in *_serviceobjects.ini
    */
   public function setConnectionKey($connectionKey) {
      $this->connectionKey = $connectionKey;
   }

   /**
    * @public
    * 
    * @param type $sheme Sheme set by user via config in *_serviceobjects.ini
    */
   public function setSheme($sheme) {
      $this->sheme = $sheme;
   }

   /**
    * @public
    * 
    * @param type $delimiter Delimiter set by user via config in *_serviceobjects.ini
    */
   public function setDelimiter($delimiter) {
      $this->delimiter = $delimiter;
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
    * Version 0.2, 15.10.2012 (Added sheme for creating a serial number)<br />
    */
   public function init($initParam) {
      if (empty($initParam['chars'])) {
         $this->chars = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      } else {
         $this->chars = $initParam['chars'];
      }
      if (empty($initParam['lenght'])) {
         $this->lenght = (int) 16;
      } else {
         $this->lenght = (int) $initParam['lenght'];
      }
      if (empty($initParam['sheme'])) {
         $this->sheme = 'XXX9-XX99-X99X-99XX';
      } else {
         $this->sheme = $initParam['sheme'];
      }
      if (empty($initParam['delimiter'])) {
         $this->delimiter = '-';
      } else {
         $this->delimiter = $initParam['delimiter'];
      }
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
    * Version 0.2, 15.10.2012 (Optimized performance of the method)<br />
    */
   public function createHash() {
      $chars = $this->mbStringToArray($this->chars);
      $MengeChars = count($chars);

      for ($i = 0; $i < $this->lenght; $i++) {
         $this->randomString .= $chars[mt_rand(0, $MengeChars - 1)];
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

   /**
    * @public
    * 
    * Creates a serial number by using the given sheme via configuration
    * 
    * @return string The created serial number
    * 
    * @author dave
    * @version
    * Version 0.1, 15.10.2012<br />
    */
   public function createSerial() {
      $k = strlen($this->sheme);
      $sernum = '';
      for ($i = 0; $i < $k; $i++) {
         switch ($this->sheme[$i]) {
            case 'X': $sernum .= $this->oneChar(false, true);
               break;
            case 'x': $sernum .= $this->oneChar();
               break;
            case '9': $sernum .= $this->oneChar(true);
               break;
            case $this->delimiter: $sernum .= $this->delimiter;
               break;
         }
      }

      return $sernum;
   }

   /**
    * @private
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
    * @private
    * 
    * Method returns one random char of all available chars.
    * 
    * @param boolean $OnlyNumeric Option to return only numeric character
    * @param boolean $OnlyBigLetter Option to return only a big letter, otherwise small letters will be returned
    * @return char One random char from all available chars set by user
    * 
    * @author dave
    * @version
    * Version 0.1, 15.10.2012<br />
    */
   private function oneChar($OnlyNumeric = false, $OnlyBigLetter = false) {

      if ($OnlyNumeric == true) {
         $chars = preg_replace('/[^0-9]/i', '', $this->chars);
         $chars = $this->mbStringToArray($chars);

         return $chars[mt_rand(0, count($chars) - 1)];
      } else {

         if ($OnlyBigLetter == true) {
            $chars = preg_replace('/[^A-Z]/', '', $this->chars);
            $chars = $this->mbStringToArray($chars);
         } else {
            $chars = preg_replace('/[^a-z]/', '', $this->chars);
            $chars = $this->mbStringToArray($chars);
         }

         return $chars[mt_rand(0, count($chars) - 1)];
      }
   }

}

?>