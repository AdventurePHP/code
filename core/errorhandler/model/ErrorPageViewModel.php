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
namespace APF\core\errorhandler\model;

use APF\core\registry\Registry;

/**
 * Represents the content of an error page.
 */
class ErrorPageViewModel {

   /**
    * @var string
    */
   private $errorId;

   /**
    * @var string
    */
   private $errorMessage;

   /**
    * @var string
    */
   private $errorNumber;

   /**
    * @var string
    */
   private $errorFile;

   /**
    * @var string
    */
   private $errorLine;

   /**
    * @return string
    */
   public function getErrorId() {
      return $this->errorId;
   }

   /**
    * @param string $errorId
    */
   public function setErrorId(string $errorId) {
      $this->errorId = $errorId;
   }

   /**
    * @return string
    */
   public function getErrorMessage() {
      return htmlspecialchars(
            $this->errorMessage,
            ENT_QUOTES,
            Registry::retrieve('APF\core', 'Charset'),
            false
      );
   }

   /**
    * @param string $errorMessage
    */
   public function setErrorMessage(string $errorMessage) {
      $this->errorMessage = $errorMessage;
   }

   /**
    * @return string
    */
   public function getErrorNumber() {
      return $this->errorNumber;
   }

   /**
    * @param string $errorNumber
    */
   public function setErrorNumber(string $errorNumber) {
      $this->errorNumber = $errorNumber;
   }

   /**
    * @return string
    */
   public function getErrorFile() {
      return $this->errorFile;
   }

   /**
    * @param string $errorFile
    */
   public function setErrorFile(string $errorFile) {
      $this->errorFile = $errorFile;
   }

   /**
    * @return string
    */
   public function getErrorLine() {
      return $this->errorLine;
   }

   /**
    * @param string $errorLine
    */
   public function setErrorLine(string $errorLine) {
      $this->errorLine = $errorLine;
   }

   /**
    * @return string Exception generation date.
    */
   public function getGeneratedDate() {
      return date('r');
   }

}
