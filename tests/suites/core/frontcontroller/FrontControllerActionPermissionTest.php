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

use APF\core\frontcontroller\Action;
use APF\core\frontcontroller\Frontcontroller;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Tests the Frontcontroller's action execution capabilities.
 */
class FrontControllerActionPermissionTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether action is only executed in case of matching types.
    */
   public function testType() {

      $fC = new Frontcontroller();
      $actionStack = $this->getActionStackProperty();

      $typeToExecute = Action::TYPE_PRE_PAGE_CREATE;
      $actionStack->setValue(
            $fC,
            [
                  $this->getActionForTypeTest(Action::TYPE_POST_TRANSFORM, $typeToExecute),
                  $this->getActionForTypeTest(Action::TYPE_PRE_PAGE_CREATE, $typeToExecute),
                  $this->getActionForTypeTest(Action::TYPE_PRE_TRANSFORM, $typeToExecute)
            ]
      );

      $runActions = $this->getRunActionsMethod();
      $runActions->invoke($fC, $typeToExecute);
   }

   /**
    * @return ReflectionProperty
    */
   private function getActionStackProperty() {
      $actionStack = new ReflectionProperty(Frontcontroller::class, 'actionStack');
      $actionStack->setAccessible(true);

      return $actionStack;
   }

   /**
    * @param string $type Action type.
    * @param string $typeToRun Action type that will be executed for test.
    *
    * @return SimpleTestAction|PHPUnit_Framework_MockObject_MockObject
    */
   private function getActionForTypeTest($type, $typeToRun) {
      $mock = $this->getMock(SimpleTestAction::class, ['getType', 'run']);
      $mock->method('getType')
            ->willReturn($type);
      $mock->expects($type === $typeToRun ? $this->once() : $this->never())
            ->method('run');

      return $mock;
   }

   /**
    * @return ReflectionMethod
    */
   private function getRunActionsMethod() {
      $runActions = new ReflectionMethod(Frontcontroller::class, 'runActions');
      $runActions->setAccessible(true);

      return $runActions;
   }

   /**
    * Tests whether action is only executed in case of being active.
    */
   public function testActive() {

      $fC = new Frontcontroller();
      $actionStack = $this->getActionStackProperty();

      $typeToExecute = Action::TYPE_PRE_PAGE_CREATE;
      $actionStack->setValue(
            $fC,
            [
                  $this->getActionForActiveTest(true),
                  $this->getActionForActiveTest(false),
                  $this->getActionForActiveTest(true)
            ]
      );

      $runActions = $this->getRunActionsMethod();
      $runActions->invoke($fC, $typeToExecute);
   }

   /**
    * @param bool $active True in case action is active, false otherwise.
    *
    * @return SimpleTestAction|PHPUnit_Framework_MockObject_MockObject
    */
   private function getActionForActiveTest($active) {
      $mock = $this->getMock(SimpleTestAction::class, ['isActive', 'run']);
      $mock->method('isActive')
            ->willReturn($active);
      $mock->expects($active ? $this->once() : $this->never())
            ->method('run');

      return $mock;
   }

   /**
    * Tests whether action is only executed in case they are allowed to be executed (a.k.a. not being protected).
    */
   public function testAllowExecution() {

      $fC = new Frontcontroller();
      $actionStack = $this->getActionStackProperty();

      $typeToExecute = Action::TYPE_PRE_PAGE_CREATE;
      $actionStack->setValue(
            $fC,
            [
                  $this->getActionForProtectionTest(true),
                  $this->getActionForProtectionTest(false),
                  $this->getActionForProtectionTest(false)
            ]
      );

      $runActions = $this->getRunActionsMethod();
      $runActions->invoke($fC, $typeToExecute);
   }

   /**
    * @param bool $allowExecution True in case action may be executed, false otherwise.
    *
    * @return SimpleTestAction|PHPUnit_Framework_MockObject_MockObject
    */
   private function getActionForProtectionTest($allowExecution) {
      $mock = $this->getMock(SimpleTestAction::class, ['allowExecution', 'run']);
      $mock->method('allowExecution')
            ->willReturn($allowExecution);
      $mock->expects($allowExecution ? $this->once() : $this->never())
            ->method('run');

      return $mock;
   }

}
