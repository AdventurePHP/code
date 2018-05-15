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
namespace APF\tests\suites\core\database;

use APF\core\database\DatabaseHandlerException;
use APF\core\database\MySQLiHandler;
use mysqli;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Basic tests for MySQLi database connection.
 */
class MySQLiHandlerTest extends TestCase {

   public function testConnect() {

      $this->expectException(DatabaseHandlerException::class);

      /* @var $conn MockObject|MySQLiHandler */
      $conn = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(null)
            ->getMock();

      $databaseName = 'test';
      $conn->setHost('localhost')
            ->setPort('3306')
            ->setUser('root')
            ->setPass('foo')
            ->setCharset('UTF-8')
            ->setCollation('UTF-8')
            ->setDatabaseName($databaseName)
            ->setDebug('1')
            ->setSocket('foo');

      $this->assertEquals($databaseName, $conn->getDatabaseName());

      $conn->setup();
   }

   public function testExecuteTextStatement() {

      /* @var $conn MockObject|MySQLiHandler */
      $conn = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(null)
            ->getMock();

      $property = new ReflectionProperty(MySQLiHandler::class, 'dbConn');
      $property->setAccessible(true);

      /* @var $conn MockObject|mysqli */
      $mySQLi = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['real_query', 'store_result'])
            ->getMock();

      $statement = 'SELECT 1 FROM dual';
      $mySQLi->expects($this->once())
            ->method('real_query')
            ->with($statement);

      $result = 'result';
      $mySQLi->expects($this->once())
            ->method('store_result')
            ->willReturn($result);

      $mySQLi->error = null;
      $mySQLi->errno = null;
      $id = 1;
      $mySQLi->insert_id = $id;

      $property->setValue($conn, $mySQLi);

      $this->assertEquals($result, $conn->executeTextStatement($statement));
      $this->assertEquals($id, $conn->getLastID());

   }

}
