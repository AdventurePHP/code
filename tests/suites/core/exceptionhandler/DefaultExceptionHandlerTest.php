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
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultExceptionHandlerTest extends TestCase {

   /**
    * Test internal exception handling (logging and exception page creation).
    */
   public function testExceptionHandler() {

      $message = 'Test';
      $code = 404;
      $exception = new Exception($message, $code);

      /* @var $handler DefaultExceptionHandler|MockObject */
      $handler = $this->getMockBuilder(DefaultExceptionHandler::class)
            ->setMethods(['logException', 'buildExceptionPage'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logException');

      $expected = 'Exception Page Content';
      $handler->expects($this->once())
            ->method('buildExceptionPage')
            ->willReturn($expected);

      ob_start();
      $handler->handleException($exception);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertEquals($expected, $output);

   }

   /**
    * Test exception page generation.
    */
   public function testExceptionPageGeneration() {

      $message = 'Test Message to be printed to exception page';
      $code = 404;
      $exception = new Exception($message, $code);

      /* @var $handler DefaultExceptionHandler|MockObject */
      $handler = $this->getMockBuilder(DefaultExceptionHandler::class)
            ->setMethods(['logException'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logException');

      ob_start();
      $handler->handleException($exception);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertContains('<h1>Uncaught exception!</h1>', $output);

      $this->assertContains('<dt>Exception-ID:</dt>', $output);
      $this->assertContains(md5($exception->getMessage() . $exception->getCode() . __FILE__ . $exception->getLine()), $output);

      $this->assertContains('<dt>Type:</dt>', $output);
      $this->assertContains(get_class($exception), $output);

      $this->assertContains('<dt>Message:</dt>', $output);
      $this->assertContains($exception->getMessage(), $output);

      $this->assertContains('<dt>Number:</dt>', $output);
      $this->assertContains(strval($exception->getCode()), $output);

      $this->assertContains('<dt>File:</dt>', $output);
      $this->assertContains(__FILE__, $output);

      $this->assertContains('<dt>Line:</dt>', $output);
      $this->assertContains(strval($exception->getLine()), $output);

      $this->assertContains('<h2>Stacktrace:</h2>', $output);

   }

}
