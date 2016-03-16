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
namespace APF\core\benchmark;

/**
 * Implements the Process DTO for the DefaultStopWatch.
 */
class DefaultProcess implements Process {

   /**
    * @var string The name of the process.
    */
   private $name;

   /**
    * @var int The hierarchy level of the process.
    */
   private $level;

   /**
    * @var float Start time.
    */
   private $start;

   /**
    * @var float End time.
    */
   private $end;

   public function __construct($name, $level) {
      $this->name = $name;
      $this->level = $level;
   }

   public function getName() {
      return $this->name;
   }

   public function getLevel() {
      return $this->level;
   }

   public function start() {
      // avoid double start to ensure accuracy
      if ($this->start === null) {
         $this->start = $this->getTimeStamp();
      }

      return $this;
   }

   /**
    * @return float The current timestamp.
    */
   private function getTimeStamp() {
      return microtime(true);
   }

   public function stop() {
      // avoid double stop to ensure accuracy
      if ($this->end === null) {
         $this->end = $this->getTimeStamp();
      }

      return $this;
   }

   public function getDuration() {

      // Return place holder in case process has not been started and/or stopped.
      if ($this->start === null || $this->end === null) {
         return '--------------------';
      }

      return number_format($this->end - $this->start, 10);
   }

}
