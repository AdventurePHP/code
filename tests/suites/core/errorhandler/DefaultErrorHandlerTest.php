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
namespace APF\tests\suites\core\errorhandler;

use APF\core\errorhandler\DefaultErrorHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultErrorHandlerTest extends TestCase {

   /**
    * Test internal exception handling (logging and exception page creation).
    */
   public function testErrorHandler() {

      $message = 'Test';
      $number = 1024;
      $file = __FILE__;
      $line = __LINE__;

      /* @var $handler DefaultErrorHandler|MockObject */
      $handler = $this->getMockBuilder(DefaultErrorHandler::class)
            ->setMethods(['logError', 'buildErrorPage'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logError');

      $expected = 'Error Page Content';
      $handler->expects($this->once())
            ->method('buildErrorPage')
            ->willReturn($expected);

      ob_start();
      $handler->handleError($number, $message, $file, $line);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertEquals($expected, $output);

   }

   /**
    * Test exception page generation.
    */
   public function testErrorPageGeneration() {

      $message = 'Test Message to be printed to exception page';
      $number = 1024;
      $file = __FILE__;
      $line = __LINE__;

      /* @var $handler DefaultErrorHandler|MockObject */
      $handler = $this->getMockBuilder(DefaultErrorHandler::class)
            ->setMethods(['logError'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logError');

      ob_start();
      $handler->handleError($number, $message, $file, $line);
      $output = ob_get_contents();
      ob_end_clean();

      $this->assertContains('<h1>Error!</h1>', $output);

      $this->assertContains('<dt>Error-ID:</dt>', $output);
      $this->assertContains(md5($message . $number . $file . $line), $output);

      $this->assertContains('<dt>Message:</dt>', $output);
      $this->assertContains($message, $output);

      $this->assertContains('<dt>Number:</dt>', $output);
      $this->assertContains(strval($number), $output);

      $this->assertContains('<dt>File:</dt>', $output);
      $this->assertContains($file, $output);

      $this->assertContains('<dt>Line:</dt>', $output);
      $this->assertContains(strval($line), $output);

      $this->assertContains('<h2>Stacktrace:</h2>', $output);

   }

}
