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
namespace APF\tests\suites\tools\link;

use APF\core\frontcontroller\FrontcontrollerInput;
use APF\tools\link\RewriteLinkScheme;

/**
 * Implements a testable link scheme regarding front controller link
 * generation capabilities that avoids double-notation of actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2013<br />
 */
class TestableDoubleActionRewriteLinkScheme extends RewriteLinkScheme {

   private $actionNamespace;
   private $actionName;

   public function __construct($actionNamespace, $actionName) {
      $this->actionNamespace = $actionNamespace;
      $this->actionName = $actionName;
      parent::__construct();
   }

   protected function getFrontControllerActions() {

      $actions = [];
      $action = new TestFrontControllerAction();
      $action->setActionNamespace($this->actionNamespace);
      $action->setActionName($this->actionName);
      $action->setKeepInUrl(true); // to test action inclusion

      $action->setInput(new FrontcontrollerInput());

      $actions[] = $action;

      return $actions;
   }

}
