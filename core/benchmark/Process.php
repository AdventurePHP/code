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
 * Defines the stop watch's process DTO.
 */
class Process {

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

   /**
    * Creates a stop watch process.
    *
    * @param string $name The name of the process.
    * @param int $level The hierarchy level.
    */
   public function __construct($name, $level) {
      $this->name = $name;
      $this->level = $level;
   }

   /**
    * @return string The name of the process.
    */
   public function getName() {
      return $this->name;
   }

   /**
    * @return int The hierarchy level.
    */
   public function getLevel() {
      return $this->level;
   }

   /**
    * Starts the process.
    *
    * @return $this This instance for further usage.
    */
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

   /**
    * Stops the process.
    *
    * @return $this This instance for further usage.
    */
   public function stop() {
      // avoid double stop to ensure accuracy
      if ($this->end === null) {
         $this->end = $this->getTimeStamp();
      }

      return $this;
   }

   /**
    * @return float Process execution duration.
    */
   public function getDuration() {

      // Return place holder in case process has not been started and/or stopped.
      if ($this->start === null || $this->end === null) {
         return '--------------------';
      }

      return number_format($this->end - $this->start, 10);
   }

}
