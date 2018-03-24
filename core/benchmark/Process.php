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
namespace APF\core\benchmark;

/**
 * Defines the stop watch's process DTO structure.
 */
interface Process {

   /**
    * Creates a stop watch process.
    *
    * @param string $name The name of the process.
    * @param int $level The hierarchy level.
    */
   public function __construct(string $name, int $level);

   /**
    * @return string The name of the process.
    */
   public function getName();

   /**
    * @return int The hierarchy level.
    */
   public function getLevel();

   /**
    * Starts the process.
    *
    * @return $this This instance for further usage.
    */
   public function start();

   /**
    * Stops the process.
    *
    * @return $this This instance for further usage.
    */
   public function stop();

   /**
    * @return float Process execution duration.
    */
   public function getDuration();

}
