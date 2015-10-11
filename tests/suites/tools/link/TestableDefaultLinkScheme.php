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
namespace APF\tests\suites\tools\link;

use APF\core\frontcontroller\FrontcontrollerInput;
use APF\tools\link\DefaultLinkScheme;

/**
 * Implements a testable link scheme regarding front controller link
 * generation capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2011<br />
 */
class TestableDefaultLinkScheme extends DefaultLinkScheme {

   protected function &getFrontcontrollerActions() {

      $actions = [];
      $action = new TestFrontControllerAction();
      $action->setActionNamespace('APF\cms\core\biz\setmodel');
      $action->setActionName('setModel');
      $action->setKeepInUrl(true); // to test action inclusion

      $input = new FrontcontrollerInput();
      $input->setParameter('page.config.section', 'external');
      $action->setInput($input);

      $actions[] = $action;

      return $actions;
   }

}
