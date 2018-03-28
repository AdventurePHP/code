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
namespace APF\tests\suites\core\configuration;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\db\DbConfigurationProvider;
use APF\core\database\MySQLiHandler;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the database configuration provider.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 25.03.2018 (ID#323 added tests while updating method declaration)<br />
 */
class DbConfigurationProviderTest extends TestCase {

   const TEST_VENDOR = 'TEST';
   const TEST_ENVIRONMENT = 'DEFAULT';
   const TEST_CONFIG_NAME = 'test-config.db';
   const TEST_CONTEXT = 'fake-context';

   public static function tearDownAfterClass() {
      // remove apc configuration provider to not disturb other tests
      ConfigurationManager::removeProvider('db');
   }

   /**
    * Tests loading a configuration file. On first attempt, this should be a physical load,
    * with "warmed cache" this should be an APCu fetch only.
    */
   public function testLoadConfigurationFile() {

      /* @var $connection MySQLiHandler|MockObject */
      $connection = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'getNumRows', 'fetchData'])
            ->getMock();

      $resourceIdentifier = 'resource';

      $connection->method('executeTextStatement')
            ->willReturn($resourceIdentifier);

      $connection->method('getNumRows')
            ->with($resourceIdentifier)
            ->willReturn(1);

      $sectionName = 'test';
      $value = 'bar';
      $key = 'foo';
      $connection->method('fetchData')
            ->with($resourceIdentifier)
            ->will($this->onConsecutiveCalls(
                  [
                        'section' => $sectionName,
                        'key' => $key,
                        'value' => $value
                  ],
                  false
            ));

      /* @var $provider DbConfigurationProvider|MockObject */
      $provider = $this->getMockBuilder(DbConfigurationProvider::class)
            ->setConstructorArgs(['fake-connection'])
            ->setMethods(['getConnection'])
            ->getMock();

      $provider->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection);

      ConfigurationManager::registerProvider('db', $provider);

      $config = ConfigurationManager::loadConfiguration(
            self::TEST_VENDOR,
            self::TEST_CONTEXT,
            null,
            self::TEST_ENVIRONMENT,
            self::TEST_CONFIG_NAME
      );

      $this->assertTrue($config->hasSection($sectionName));
      $this->assertInstanceOf(Configuration::class, $config->getSection($sectionName));
      $this->assertEquals($config->getValue($sectionName . '.' . $key), $value);

   }

}
