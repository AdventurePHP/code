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
use APF\modules\genericormapper\data\GenericCriterionObject;
use APF\modules\genericormapper\data\GenericDomainObject;
use APF\modules\genericormapper\data\GenericORMapper;
use APF\modules\genericormapper\data\GenericORMapperDataObject;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use InvalidArgumentException;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests the generic O/R mapper's capabilities.
 *
 * @author Christian Achatz
 * @version 0.1, 30.07.2016 (ID#306: fixed SQL injection issue)<br />
 */
class GenericORMapperTest extends \PHPUnit_Framework_TestCase {

   public function testLoadObjectByID1() {
      $this->expectException(InvalidArgumentException::class);
      $mapper = new GenericORRelationMapper();
      $mapper->loadObjectByID('foo', 'bar');
   }

   public function testLoadObjectByID2() {

      $objectName = 'Object';
      $attributeName = 'DisplayName';
      $expectedValue = 'Foo';

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $config = new IniConfiguration();
      $config->setValue($objectName . '.' . $attributeName, 'VARCHAR(100)');

      $mapper->method('getConfiguration')
            ->willReturn($config);

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'fetchData'])
            ->getMock();

      $driver->method('fetchData')
            ->willReturn([$attributeName => $expectedValue, $objectName . 'ID' => 2]);

      $mapper->setDbDriver($driver);

      $object = $mapper->loadObjectByID($objectName, '2');

      $this->assertInstanceOf(GenericORMapperDataObject::class, $object);
      $this->assertEquals(2, $object->getObjectId());
      $this->assertEquals($expectedValue, $object->getProperty($attributeName));
   }

   public function testDeleteObject() {

      $objectName = 'Object';
      $attributeName = 'DisplayName';

      /* @var $mapper GenericORMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $config = new IniConfiguration();
      $config->setValue($objectName . '.' . $attributeName, 'VARCHAR(100)');

      $mapper->method('getConfiguration')
            ->willReturn($config);

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([['foo', 0], ['12', 12]]);

      $mapper->setDbDriver($driver);

      // delete object w/ invalid id
      $object = new GenericDomainObject($objectName);
      $object->setObjectId('foo');
      $this->assertEquals(0, $mapper->deleteObject($object));

      // delete object w/ valid id
      $object = new GenericDomainObject($objectName);
      $object->setObjectId('12');
      $this->assertEquals(12, $mapper->deleteObject($object));

   }

   public function testSaveObject() {

      $objectName = 'Object';
      $attributeName = 'DisplayName';
      $attributeValue = 'Foo';
      $id = 42;

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $config = new IniConfiguration();
      $config->setValue($objectName . '.' . $attributeName, 'VARCHAR(100)');

      $mapper->method('getConfiguration')
            ->willReturn($config);

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([[$id, $id], [$attributeValue, $attributeValue]]);

      $driver->expects($this->once())
            ->method('executeTextStatement')
            ->with(
                  'UPDATE ent_object SET `' . $attributeName . '` = \'' . $attributeValue . '\', '
                  . 'ModificationTimestamp = NOW() WHERE ObjectID = \'' . $id . '\';'
            );

      $mapper->setDbDriver($driver);

      /* @var $object GenericDomainObject|PHPUnit_Framework_MockObject_MockObject */
      $object = $this->getMockBuilder(GenericDomainObject::class)
            ->setMethods(['setDataComponent', 'beforeSave', 'afterSave'])
            ->setConstructorArgs([$objectName])
            ->getMock();

      // expect the mapper to inject the data component instance
      $object->method('setDataComponent')
            ->with($mapper);

      $object->expects($this->once())
            ->method('beforeSave');

      $object->expects($this->once())
            ->method('afterSave');

      $object->setObjectId($id);

      $object->setProperty($attributeName, $attributeValue);

      $this->assertEquals($id, $mapper->saveObject($object, false));
      $this->assertEquals($id, $object->getObjectId());
   }

   public function testCreateAssociation() {

      $sourceId = 1;
      $targetId = 2;

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $mappingConfig = new IniConfiguration();
      $mappingConfig->setValue('Foo.DisplayName', 'VARCHAR(100)');
      $mappingConfig->setValue('Bar.DisplayName', 'VARCHAR(100)');

      $relationConfig = new IniConfiguration();
      $relationConfig->setValue('Foo2Bar.Type', 'ASSOCIATION');
      $relationConfig->setValue('Foo2Bar.SourceObject', 'Foo');
      $relationConfig->setValue('Foo2Bar.TargetObject', 'Bar');

      $mapper->method('getConfiguration')
            ->willReturnMap([
                  ['namespace', 'affix_objects.ini', $mappingConfig],
                  ['namespace', 'affix_relations.ini', $relationConfig]
            ]);

      // setup mapping and relation table
      $mapper->addMappingConfiguration('namespace', 'affix');
      $mapper->addRelationConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([
                  [$sourceId, $sourceId],
                  [$targetId, $targetId]
            ]);

      $driver->expects($this->once())
            ->method('executeTextStatement')
            ->with(
                  'INSERT INTO `ass_foo2bar`
                    (`Source_FooID`,`Target_BarID`)
                    VALUES
                    (\'' . $sourceId . '\',\'' . $targetId . '\');'
            );
      $mapper->setDbDriver($driver);

      // create association
      $source = new GenericDomainObject('Foo');
      $source->setObjectId($sourceId);

      $target = new GenericDomainObject('Bar');
      $target->setObjectId($targetId);

      $mapper->createAssociation('Foo2Bar', $source, $target);

   }

   public function testDeleteAssociation() {

      $sourceId = 1;
      $targetId = 2;

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $mappingConfig = new IniConfiguration();
      $mappingConfig->setValue('Foo.DisplayName', 'VARCHAR(100)');
      $mappingConfig->setValue('Bar.DisplayName', 'VARCHAR(100)');

      $relationConfig = new IniConfiguration();
      $relationConfig->setValue('Foo2Bar.Type', 'ASSOCIATION');
      $relationConfig->setValue('Foo2Bar.SourceObject', 'Foo');
      $relationConfig->setValue('Foo2Bar.TargetObject', 'Bar');

      $mapper->method('getConfiguration')
            ->willReturnMap([
                  ['namespace', 'affix_objects.ini', $mappingConfig],
                  ['namespace', 'affix_relations.ini', $relationConfig]
            ]);

      // setup mapping and relation table
      $mapper->addMappingConfiguration('namespace', 'affix');
      $mapper->addRelationConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([
                  [$sourceId, $sourceId],
                  [$targetId, $targetId]
            ]);

      $driver->expects($this->once())
            ->method('executeTextStatement')
            ->with(
                  'DELETE FROM `ass_foo2bar`
                    WHERE
                       `Source_FooID` = \'' . $sourceId . '\'
                       AND
                       `Target_BarID` = \'' . $targetId . '\';'
            );
      $mapper->setDbDriver($driver);

      // create association
      $source = new GenericDomainObject('Foo');
      $source->setObjectId($sourceId);

      $target = new GenericDomainObject('Bar');
      $target->setObjectId($targetId);

      $mapper->deleteAssociation('Foo2Bar', $source, $target);
   }

   /**
    * ID#310: loadObjectList() return value should be null/[] instead of [[0] => null].
    */
   public function testLoadObjectList() {

      $objectName = 'User';
      $attributeName = 'DisplayName';

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $config = new IniConfiguration();
      $config->setValue($objectName . '.' . $attributeName, 'VARCHAR(100)');

      $mapper->method('getConfiguration')
            ->willReturn($config);

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'fetchData'])
            ->getMock();
      $driver->method('fetchData')
            ->willReturn(false);

      $mapper->setDbDriver($driver);

      $result = $mapper->loadObjectList($objectName);

      $this->assertInternalType('array', $result);
      $this->assertEmpty($result);
   }

   /**
    * ID#310: loadObjectListByCriterion() return value should be null/[] instead of [[0] => null].
    */
   public function testLoadObjectListByCriterion() {

      $objectName = 'User';
      $attributeName = 'DisplayName';

      /* @var $mapper GenericORRelationMapper|PHPUnit_Framework_MockObject_MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $config = new IniConfiguration();
      $config->setValue($objectName . '.' . $attributeName, 'VARCHAR(100)');

      $mapper->method('getConfiguration')
            ->willReturn($config);

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');

      /* @var $driver MySQLiHandler|PHPUnit_Framework_MockObject_MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'fetchData'])
            ->getMock();
      $driver->method('fetchData')
            ->willReturn(false);

      $mapper->setDbDriver($driver);

      $result = $mapper->loadObjectListByCriterion($objectName, new GenericCriterionObject());

      $this->assertInternalType('array', $result);
      $this->assertEmpty($result);
   }

}
