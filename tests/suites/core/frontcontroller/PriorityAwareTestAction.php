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
namespace APF\tests\suites\core\frontcontroller;

use APF\core\frontcontroller\AbstractFrontControllerAction;

class PriorityAwareTestAction extends AbstractFrontControllerAction {

   /**
    * @var int The priority to inject into the action for testing purposes.
    */
   public static $INITIAL_PRIORITY;

   /**
    * @var int The action's priority.
    */
   private $priority;

   public function __construct() {
      $this->priority = self::$INITIAL_PRIORITY;
   }

   public function run() {
   }

   public function getPriority() {
      return $this->priority;
   }

}
