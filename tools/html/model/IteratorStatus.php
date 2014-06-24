<?php
namespace APF\tools\html\model;

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
 * @package APF\tools\html\taglib
 * @class IteratorStatus
 *
 * Represents the status of a current loop run within the iterator. Can be accessed
 * via the extended template syntax:
 * <code>
 * ${status->isFirst()}
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.05.2014 (ID#189: introduced status variable to ease access and usage)<br />
 */
class IteratorStatus {

   /**
    * @var bool <em>True</em>, in case current loop outputs the first element of the list, <em>false</em> otherwise.
    */
   private $isFirst;

   /**
    * @var bool <em>True</em>, in case current loop outputs the last element of the list, <em>false</em> otherwise.
    */
   private $isLast;

   /**
    * @return int The number of total items within this iterator run.
    */
   private $itemCount;

   /**
    * @return int A counter that increments with each loop run. Can be used to number lists and tables.
    */
   private $counter;

   /**
    * @var string Css class tailored to the current loop run (first, middle, last).
    */
   private $cssClass;

   public function __construct($isFirst, $isLast, $itemCount, $counter, $cssClass) {
      $this->isFirst = $isFirst;
      $this->isLast = $isLast;
      $this->itemCount = $itemCount;
      $this->counter = $counter;
      $this->cssClass = $cssClass;
   }

   public function getCssClass() {
      return $this->cssClass;
   }

   public function isFirst($asString = false) {
      return $asString === false ? $this->isFirst : $this->convertToString($this->isFirst);
   }

   public function isLast($asString = false) {
      return $asString === false ? $this->isLast : $this->convertToString($this->isLast);
   }

   public function getItemCount() {
      return $this->itemCount;
   }

   public function getCounter() {
      return $this->counter;
   }

   private function convertToString($bool) {
      return $bool === true ? '1' : '0';
   }

}
