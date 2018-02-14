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

use APF\core\exceptionhandler\CLIExceptionHandler;
use Exception;
use PHPUnit_Framework_MockObject_MockObject;

class CLIExceptionHandlerTest extends \PHPUnit_Framework_TestCase {

   /**
    * Test internal exception handling (logging and exception page creation).
    */
   public function testExceptionHandler() {

      $message = 'Test';
      $code = 404;
      $exception = new Exception($message, $code);

      /* @var $handler CLIExceptionHandler|PHPUnit_Framework_MockObject_MockObject */
      $handler = $this->getMockBuilder(CLIExceptionHandler::class)
            ->setMethods(['logException', 'buildExceptionOutput'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logException');

      $expected = 'Exception Page Content';
      $handler->expects($this->once())
            ->method('buildExceptionOutput')
            ->willReturn($expected);

      ob_start();
      $handler->handleException($exception);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertEquals($expected, $output);
   }


   /**
    * Test exception message generation.
    */
   public function testExceptionPageGeneration() {

      $message = 'Test Message to be printed to exception page';
      $code = 404;
      $exception = new Exception($message, $code);

      /* @var $handler CLIExceptionHandler|PHPUnit_Framework_MockObject_MockObject */
      $handler = $this->getMockBuilder(CLIExceptionHandler::class)
            ->setMethods(['logException'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logException');

      ob_start();
      $handler->handleException($exception);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertContains('[' . $code . ']', $output);
      $this->assertContains($message, $output);
      $this->assertContains(__FILE__, $output);
      $this->assertContains('Stacktrace:', $output);
      $this->assertContains(str_replace('::', '->', __METHOD__), $output);

   }

}
