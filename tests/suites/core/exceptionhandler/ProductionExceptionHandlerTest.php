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

use APF\core\exceptionhandler\ProductionExceptionHandler;
use APF\core\http\ResponseImpl;
use Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProductionExceptionHandlerTest extends TestCase {

   /**
    * Test internal exception handling (logging and exception page creation).
    */
   public function testExceptionHandler() {

      $message = 'Test';
      $code = 404;
      $exception = new Exception($message, $code);

      /* @var $handler ProductionExceptionHandler|MockObject */
      $handler = $this->getMockBuilder(ProductionExceptionHandler::class)
            ->setMethods(['logException', 'getRedirectPage', 'getResponse'])
            ->getMock();

      $handler->expects($this->once())
            ->method('logException');

      $handler->expects($this->once())
            ->method('getRedirectPage')
            ->willReturn('/');

      /* @var $response ResponseImpl|MockObject */
      $response = $this->getMockBuilder(ResponseImpl::class)
            ->setMethods(['forward'])
            ->getMock();

      $response->expects($this->once())
            ->method('forward');

      $handler->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);

      $handler->handleException($exception);

   }

}
