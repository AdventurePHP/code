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
use APF\modules\genericormapper\data\GenericORMapperException;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the generic O/R mapper's capabilities.
 *
 * @author Christian Achatz
 * @version 0.1, 30.07.2016 (ID#306: fixed SQL injection issue)<br />
 */
class GenericORMapperTest extends TestCase {

   public function testLoadObjectByID1() {
      $this->expectException(InvalidArgumentException::class);
      $mapper = new GenericORRelationMapper();
      $mapper->loadObjectByID('foo', 'bar');
   }

   public function testLoadObjectByID2() {

      $objectName = 'Object';
      $attributeName = 'DisplayName';
      $expectedValue = 'Foo';

      /* @var $mapper GenericORRelationMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
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

      /* @var $mapper GenericORMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([['foo', 0], ['12', 12]]);

      $mapper->setDbDriver($driver);

      // delete object w/ invalid id
      $object = new GenericDomainObject($objectName);
      $object->setObjectId(1);
      $this->assertEquals(0, $mapper->deleteObject($object));

      // delete object w/ valid id
      $object = new GenericDomainObject($objectName);
      $object->setObjectId('12');
      $this->assertEquals(12, $mapper->deleteObject($object));

   }

   /**
    * Test object deletion w/ complex object setup.
    */
   public function testDeleteObject2() {

      $objectOneName = 'ObjectOne';
      $objectTwoName = 'ObjectTwo';
      $objectThreeName = 'ObjectThree';
      $attributeName = 'DisplayName';

      /* @var $mapper GenericORMapper|MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $mappingConfig = new IniConfiguration();
      $mappingConfig->setValue($objectOneName . '.' . $attributeName, 'VARCHAR(100)');
      $mappingConfig->setValue($objectTwoName . '.' . $attributeName, 'VARCHAR(100)');
      $mappingConfig->setValue($objectThreeName . '.' . $attributeName, 'VARCHAR(100)');

      $relationConfig = new IniConfiguration();

      $associationName = $objectOneName . '2' . $objectTwoName;
      $relationConfig->setValue($associationName . '.Type', 'ASSOCIATION');
      $relationConfig->setValue($associationName . '.SourceObject', $objectOneName);
      $relationConfig->setValue($associationName . '.TargetObject', $objectTwoName);

      $compositionName = $objectThreeName . '2' . $objectOneName;
      $relationConfig->setValue($compositionName . '.Type', 'COMPOSITION');
      $relationConfig->setValue($compositionName . '.SourceObject', $objectThreeName);
      $relationConfig->setValue($compositionName . '.TargetObject', $objectOneName);

      $mapper->method('getConfiguration')
            ->will($this->onConsecutiveCalls(
                  $mappingConfig,
                  $relationConfig
            ));

      // setup mapping table
      $mapper->addMappingConfiguration('namespace', 'affix');
      $mapper->addRelationConfiguration('namespace', 'affix');

      // inject pre-configured DB handler
      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(3))
            ->method('executeTextStatement')
            ->withConsecutive(
                  ['DELETE FROM `ass_objectone2objecttwo`
                       WHERE `Source_ObjectOneID` = \'1\';'],
                  ['DELETE FROM `ent_objectone` WHERE `ObjectOneID` = \'1\';'],
                  ['DELETE FROM `cmp_objectthree2objectone`
                       WHERE `Target_ObjectOneID` = \'1\';']
            );

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([['1', 1]]);

      $mapper->setDbDriver($driver);

      // delete object
      $object = new GenericDomainObject($objectOneName);
      $object->setObjectId(1);
      $this->assertEquals(1, $mapper->deleteObject($object));

   }

   /**
    * @throws GenericORMapperException
    */
   public function testSaveObject() {

      $objectName = 'Object';
      $attributeName = 'DisplayName';
      $attributeValue = 'Foo';
      $id = 42;

      /* @var $mapper GenericORRelationMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([[strval($id), $id], [strval($attributeValue), $attributeValue]]);

      $driver->expects($this->once())
            ->method('executeTextStatement')
            ->with(
                  'UPDATE ent_object SET `' . $attributeName . '` = \'' . $attributeValue . '\', '
                  . 'ModificationTimestamp = NOW() WHERE ObjectID = \'' . $id . '\';'
            );

      $mapper->setDbDriver($driver);

      /* @var $object GenericDomainObject|MockObject */
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

   /**
    * @throws GenericORMapperException
    */
   public function testCreateAssociation() {

      $sourceId = 1;
      $targetId = 2;

      /* @var $mapper GenericORRelationMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([
                  [strval($sourceId), $sourceId],
                  [strval($targetId), $targetId]
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

   /**
    * @throws GenericORMapperException
    */
   public function testDeleteAssociation() {

      $sourceId = 1;
      $targetId = 2;

      /* @var $mapper GenericORRelationMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue'])
            ->getMock();

      $driver->expects($this->exactly(2))
            ->method('escapeValue')
            ->willReturnMap([
                  [strval($sourceId), $sourceId],
                  [strval($targetId), $targetId]
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

      /* @var $mapper GenericORRelationMapper|MockObject */
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
      /* @var $driver MySQLiHandler|MockObject */
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
    * @throws GenericORMapperException
    */
   public function testLoadObjectListByCriterion() {

      $objectName = 'User';
      $attributeName = 'DisplayName';

      /* @var $mapper GenericORRelationMapper|MockObject */
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

      /* @var $driver MySQLiHandler|MockObject */
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

   /**
    * Test whether two objects are associated.
    */
   public function testIsAssociated() {

      $relationName = 'Foo2Bar';

      /* @var $mapper GenericORRelationMapper|MockObject */
      $mapper = $this->getMockBuilder(GenericORRelationMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      // mock configuration
      $mappingConfig = new IniConfiguration();
      $mappingConfig->setValue('Foo.DisplayName', 'VARCHAR(100)');
      $mappingConfig->setValue('Bar.DisplayName', 'VARCHAR(100)');

      $relationConfig = new IniConfiguration();
      $relationConfig->setValue($relationName . '.Type', 'ASSOCIATION');
      $relationConfig->setValue($relationName . '.SourceObject', 'Foo');
      $relationConfig->setValue($relationName . '.TargetObject', 'Bar');

      $mapper->method('getConfiguration')
            ->will($this->onConsecutiveCalls(
                  $mappingConfig,
                  $relationConfig
            ));

      $mapper->addMappingConfiguration('namespace', 'affix');
      $mapper->addRelationConfiguration('namespace', 'affix');

      /* @var $driver MySQLiHandler|MockObject */
      $driver = $this->getMockBuilder(MySQLiHandler::class)
            ->setMethods(['executeTextStatement', 'escapeValue', 'getNumRows'])
            ->getMock();

      $driver->method('getNumRows')
            ->will($this->onConsecutiveCalls(true, false));

      $driver->method('escapeValue')
            ->willReturnMap([
                  ['1', 1],
                  ['2', 2],
                  ['1', 1],
                  ['3', 3]
            ]);

      $mapper->setDbDriver($driver);

      $foo = new GenericDomainObject('Foo');
      $foo->setObjectId(1);

      $bar = new GenericDomainObject('Bar');
      $bar->setObjectId(1);

      $this->assertTrue($mapper->isAssociated($relationName, $foo->setObjectId(1), $bar->setObjectId(1)));
      $this->assertFalse($mapper->isAssociated($relationName, $foo->setObjectId(1), $bar->setObjectId(3)));
   }

   public function testIsComposed() {
      $this->markTestSkipped('Not yet implemented!');
   }

   public function testLoadRelatedObjects() {
      $this->markTestSkipped('Not yet implemented!');
   }

   public function testLoadNotRelatedObjects() {
      $this->markTestSkipped('Not yet implemented!');
   }

   public function testLoadRelationMultiplicity() {
      $this->markTestSkipped('Not yet implemented!');
   }

   public function testDeleteAssociations() {
      $this->markTestSkipped('Not yet implemented!');
   }

}
