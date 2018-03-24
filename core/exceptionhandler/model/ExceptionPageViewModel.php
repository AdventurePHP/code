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
namespace APF\core\exceptionhandler\model;

use APF\core\registry\Registry;

/**
 * Represents the content of an exception page.
 */
class ExceptionPageViewModel {

   /**
    * @var string
    */
   private $exceptionId;

   /**
    * @var string
    */
   private $exceptionMessage;

   /**
    * @var string
    */
   private $exceptionNumber;

   /**
    * @var string
    */
   private $exceptionFile;

   /**
    * @var string
    */
   private $exceptionLine;

   /**
    * @var array
    */
   private $exceptionTrace;

   /**
    * @var string
    */
   private $exceptionType;

   /**
    * @return string
    */
   public function getExceptionId() {
      return $this->exceptionId;
   }

   /**
    * @param string $exceptionId
    */
   public function setExceptionId(string $exceptionId) {
      $this->exceptionId = $exceptionId;
   }

   /**
    * @return string
    */
   public function getExceptionMessage() {
      return htmlspecialchars(
            $this->exceptionMessage,
            ENT_QUOTES,
            Registry::retrieve('APF\core', 'Charset'),
            false
      );
   }

   /**
    * @param string $exceptionMessage
    */
   public function setExceptionMessage(string $exceptionMessage) {
      $this->exceptionMessage = $exceptionMessage;
   }

   /**
    * @return string
    */
   public function getExceptionNumber() {
      return $this->exceptionNumber;
   }

   /**
    * @param string $exceptionNumber
    */
   public function setExceptionNumber(string $exceptionNumber) {
      $this->exceptionNumber = $exceptionNumber;
   }

   /**
    * @return string
    */
   public function getExceptionFile() {
      return $this->exceptionFile;
   }

   /**
    * @param string $exceptionFile
    */
   public function setExceptionFile(string $exceptionFile) {
      $this->exceptionFile = $exceptionFile;
   }

   /**
    * @return string
    */
   public function getExceptionLine() {
      return $this->exceptionLine;
   }

   /**
    * @param string $exceptionLine
    */
   public function setExceptionLine(string $exceptionLine) {
      $this->exceptionLine = $exceptionLine;
   }

   /**
    * @return array
    */
   public function getExceptionTrace() {
      return $this->exceptionTrace;
   }

   /**
    * @param array $exceptionTrace
    */
   public function setExceptionTrace(array $exceptionTrace) {
      $this->exceptionTrace = $exceptionTrace;
   }

   /**
    * @return string
    */
   public function getExceptionType() {
      return $this->exceptionType;
   }

   /**
    * @param string $exceptionType
    */
   public function setExceptionType(string $exceptionType) {
      $this->exceptionType = $exceptionType;
   }

   /**
    * @return string Exception generation date.
    */
   public function getGeneratedDate() {
      return date('r');
   }

}
