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
namespace APF\tests\suites\core\exceptionhandler;

use APF\core\exceptionhandler\DefaultExceptionHandler;
use APF\core\exceptionhandler\GlobalExceptionHandler;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionProperty;

class GlobalExceptionHandlerTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether registration and execution of a custom exception handler works.
    */
   public function testHandlerRegistration() {

      /* @var $handler DefaultExceptionHandler|PHPUnit_Framework_MockObject_MockObject */
      $handler = $this->getMockBuilder(DefaultExceptionHandler::class)
            ->setMethods(['handleException'])
            ->getMock();

      $handler->expects($this->once())
            ->method('handleException');

      GlobalExceptionHandler::registerExceptionHandler($handler);
      GlobalExceptionHandler::handleException(new Exception('Test'));

   }

   /**
    * Test whether fallback to PHP's internal exception handling works.
    */
   public function testPHPExceptionHandling() {
      $message = 'GlobalExceptionHandlerTest';

      $this->expectException(Exception::class);
      $this->expectExceptionMessage($message);

      // reset handler definition
      $property = new ReflectionProperty(GlobalExceptionHandler::class, 'HANDLER');
      $property->setAccessible(true);
      $property->setValue(null, null);

      GlobalExceptionHandler::handleException(new Exception($message));
   }

   /**
    * Tests whether the exception is printed in case the exception handler execution fails.
    */
   public function testFallbackExceptionHandling() {

      /* @var $handler DefaultExceptionHandler|PHPUnit_Framework_MockObject_MockObject */
      $handler = $this->getMockBuilder(DefaultExceptionHandler::class)
            ->setMethods(['handleException'])
            ->getMock();

      $code = 404;
      $message = 'Test';
      $exception = new Exception($message, $code);

      $handler->method('handleException')
            ->willThrowException($exception);

      GlobalExceptionHandler::registerExceptionHandler($handler);

      // collect stdout output
      ob_start();
      GlobalExceptionHandler::handleException($exception);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertContains('APF catchable exception:', $output);
      $this->assertContains($message, $output);
      $this->assertContains('(code: ' . $code . ')', $output);
      $this->assertContains(__FILE__, $output);

   }

}
