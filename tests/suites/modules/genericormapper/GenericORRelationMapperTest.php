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
namespace APF\tests\suites\modules\genericormapper;

use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\database\MySQLiHandler;
use APF\modules\genericormapper\data\GenericDomainObject;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use APF\modules\genericormapper\data\tools\GenericORMapperDomainObjectGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GenericORRelationMapperTest extends TestCase {

   /**
    * Clean-up generated files.
    */
   public function tearDown() {
      unlink(__DIR__ . '/Dad.php');
      unlink(__DIR__ . '/DadBase.php');
      unlink(__DIR__ . '/Son.php');
      unlink(__DIR__ . '/SonBase.php');
   }

   public function testRelationTimeStamps() {

      // Generate domain objects ///////////////////////////////////////////////////////////////////////////////////////

      /* @var $generator GenericORMapperDomainObjectGenerator|MockObject */
      $generator = $this->getMockBuilder(GenericORMapperDomainObjectGenerator::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $generator->method('getConfiguration')
            ->willReturnMap($this->getConfigReturnMap());

      $generator->addMappingConfiguration('foo', 'bar');
      $generator->addDomainObjectsConfiguration('foo', 'bar');

      $generator->generateServiceObjects();


      // Test w/ generic domain object tests ///////////////////////////////////////////////////////////////////////////

      /* @var $mapper GenericORRelationMapper|MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $mapper->method('getConfiguration')
            ->willReturnMap($this->getConfigReturnMap());

      $mapper->addMappingConfiguration('foo', 'bar');
      $mapper->addRelationConfiguration('foo', 'bar');

      $objectId = 2;
      $name = 'Son';
      $timestamp = 'YYYY-MM-dd HH:ii:ss';

      /* @var $db MySQLiHandler|MockObject */
      $db = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['connect', 'fetchData', 'escapeValue', 'executeTextStatement'])
            ->getMock();

      $db->method('fetchData')
            ->willReturnOnConsecutiveCalls(
                  [
                        'SonID' => $objectId,
                        'Name' => $name,
                        'Relation_CreationTimestamp' => $timestamp
                  ],
                  false
            );

      $db->method('escapeValue')
            ->willReturnArgument(0);

      $mapper->setDbDriver($db);

      $dad = new GenericDomainObject('Dad');
      $dad->setObjectId(1);

      /* @var $children GenericDomainObject[] */
      $children = $mapper->loadRelatedObjects($dad, 'Dad2Son');

      $this->assertCount(1, $children);
      $this->assertInstanceOf(GenericDomainObject::class, $children[0]);
      $this->assertEquals($name, $children[0]->getProperty('Name'));
      $this->assertEquals($objectId, $children[0]->getObjectId());
      $this->assertEquals($timestamp, $children[0]->getRelationCreationTimestamp());


      // Test w/ custom domain objects /////////////////////////////////////////////////////////////////////////////////
      /* @var $mapper GenericORRelationMapper|MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $mapper->method('getConfiguration')
            ->willReturnMap($this->getConfigReturnMap());

      $mapper->addMappingConfiguration('foo', 'bar');
      $mapper->addRelationConfiguration('foo', 'bar');
      $mapper->addDomainObjectsConfiguration('foo', 'bar');

      /* @var $db MySQLiHandler|MockObject */
      $db = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['connect', 'fetchData', 'escapeValue', 'executeTextStatement'])
            ->getMock();

      $db->method('fetchData')
            ->willReturnOnConsecutiveCalls(
                  [
                        'SonID' => $objectId,
                        'Name' => $name,
                        'Relation_CreationTimestamp' => $timestamp
                  ],
                  false
            );

      $db->method('escapeValue')
            ->willReturnArgument(0);

      $mapper->setDbDriver($db);

      $dad = new GenericDomainObject('Dad');
      $dad->setObjectId(1);

      /* @var $children GenericDomainObject[] */
      $children = $mapper->loadRelatedObjects($dad, 'Dad2Son');

      $this->assertCount(1, $children);
      $this->assertEquals(__NAMESPACE__ . '\Son', get_class($children[0]));
      $this->assertEquals($name, $children[0]->getProperty('Name'));
      $this->assertEquals($objectId, $children[0]->getObjectId());
      $this->assertEquals($timestamp, $children[0]->getRelationCreationTimestamp());

   }

   /**
    * @return array Return values for the getConfiguration() method.
    */
   protected function getConfigReturnMap() {

      $objects = new IniConfiguration();
      $objects->setValue('Dad.Name', 'VARCHAR(100)');
      $objects->setValue('Son.Name', 'VARCHAR(100)');

      $relations = new IniConfiguration();
      $relations->setValue('Dad2Son.Type', 'COMPOSITION');
      $relations->setValue('Dad2Son.SourceObject', 'Dad');
      $relations->setValue('Dad2Son.TargetObject', 'Son');
      $relations->setValue('Dad2Son.Timestamps', 'true');

      $domainObjects = new IniConfiguration();
      $domainObjects->setValue('Dad.Class', __NAMESPACE__ . '\Dad');
      $domainObjects->setValue('Son.Class', __NAMESPACE__ . '\Son');

      return [
            [
                  'foo',
                  'bar_objects.ini',
                  $objects
            ],
            [
                  'foo',
                  'bar_relations.ini',
                  $relations
            ],
            [
                  'foo',
                  'bar_domainobjects.ini',
                  $domainObjects
            ]
      ];
   }

}
