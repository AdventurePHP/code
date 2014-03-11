<?php
namespace APF\tests\suites\core\frontcontroller;

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
use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\configuration\provider\ini\IniConfigurationProvider;

class FakeIniProvider extends IniConfigurationProvider {

   public function loadConfiguration($namespace, $context, $language, $environment, $name) {

      $config = new IniConfiguration();

      // setup section for action
      $action = new IniConfiguration();
      $action->setValue('ActionClass', 'APF\tests\suites\core\frontcontroller\PriorityAwareTestAction');

      $config->setSection(FrontcontrollerTest::TEST_ACTION_NAME, $action);

      return $config;
   }

}