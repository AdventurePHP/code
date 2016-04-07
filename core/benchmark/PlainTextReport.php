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
 * Simple report creating just plain/text output.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 14.03.2016 (ID#214: added new plain/text report)<br />
 */
class PlainTextReport implements Report {

   public function compile(array $processes) {

      $buffer = $processes[0]->getName() . ' ' . $processes[0]->getDuration() . 's' . PHP_EOL;

      foreach (array_slice($processes, 1) as $process) {
         /* @var $process Process */
         $buffer .= str_repeat('-', $process->getLevel())
               . ' ' . $process->getName()
               . ' ' . $process->getDuration() . 's'
               . PHP_EOL;
      }

      return $buffer . PHP_EOL;

   }

}