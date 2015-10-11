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
namespace APF\tests\suites\core\frontcontroller;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\frontcontroller\Frontcontroller;

/**
 * Tests prioritization of front controller actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.03.2014<br />
 */
class FrontControllerActionPriorityTest extends \PHPUnit_Framework_TestCase {

   const TEST_ACTION_NAME = 'TestAction';
   const TEST_ACTION_NAMESPACE = 'APF\tests\core\frontcontroller';
   const TEST_ACTION_CONFIG_NAME = 'actionconfig.ini';

   /**
    * @var IniConfigurationProvider
    */
   private $initialIniProvider;

   public function setUp() {

      // setup config provider to fake tests
      $this->initialIniProvider = ConfigurationManager::retrieveProvider('ini');

      // setup fake ini provider to avoid file-based configuration files
      $provider = new FakeIniProvider();

      $config = new IniConfiguration();

      // setup section for action
      $action = new IniConfiguration();
      $action->setValue('ActionClass', PriorityAwareTestAction::class);

      $config->setSection(FrontControllerActionPriorityTest::TEST_ACTION_NAME, $action);

      $provider->registerConfiguration(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_CONFIG_NAME, $config);

      ConfigurationManager::registerProvider('ini', $provider);

   }

   /**
    * Register actions with a random order and tests whether the right
    * prioritization comes up.
    */
   public function testActionPrioritization() {

      $fC = new Frontcontroller();

      PriorityAwareTestAction::$INITIAL_PRIORITY = 5;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 20;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 1;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 10;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 15;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      $actions = &$fC->getActions();
      $keys = array_keys($actions);

      assertEquals(20, $actions[$keys[0]]->getPriority());
      assertEquals(15, $actions[$keys[1]]->getPriority());
      assertEquals(10, $actions[$keys[2]]->getPriority());
      assertEquals(5, $actions[$keys[3]]->getPriority());
      assertEquals(1, $actions[$keys[4]]->getPriority());

   }

   public function testEquivalenceGroups() {

      $fC = new Frontcontroller();

      PriorityAwareTestAction::$INITIAL_PRIORITY = 10;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME, ['id' => '10-1']);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 1;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 5;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 10;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME, ['id' => '10-2']);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 10;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME, ['id' => '10-3']);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 20;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME, ['id' => '20-1']);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 15;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME);

      PriorityAwareTestAction::$INITIAL_PRIORITY = 20;
      $fC->addAction(self::TEST_ACTION_NAMESPACE, self::TEST_ACTION_NAME, ['id' => '20-2']);


      $actions = &$fC->getActions();
      $keys = array_keys($actions);

      assertEquals(20, $actions[$keys[0]]->getPriority());
      assertEquals('20-1', $actions[$keys[0]]->getInput()->getParameter('id'));

      assertEquals(20, $actions[$keys[1]]->getPriority());
      assertEquals('20-2', $actions[$keys[1]]->getInput()->getParameter('id'));

      assertEquals(15, $actions[$keys[2]]->getPriority());

      assertEquals(10, $actions[$keys[3]]->getPriority());
      assertEquals('10-1', $actions[$keys[3]]->getInput()->getParameter('id'));

      assertEquals(10, $actions[$keys[4]]->getPriority());
      assertEquals('10-2', $actions[$keys[4]]->getInput()->getParameter('id'));

      assertEquals(10, $actions[$keys[5]]->getPriority());
      assertEquals('10-3', $actions[$keys[5]]->getInput()->getParameter('id'));

      assertEquals(5, $actions[$keys[6]]->getPriority());

      assertEquals(1, $actions[$keys[7]]->getPriority());

   }

   protected function tearDown() {
      // restore previous setup to not influence further tests
      ConfigurationManager::registerProvider('ini', $this->initialIniProvider);
   }

}
