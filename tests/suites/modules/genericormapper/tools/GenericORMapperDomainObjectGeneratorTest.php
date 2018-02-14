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
namespace APF\tests\suites\modules\genericormapper\tools;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\pagecontroller\Document;
use APF\modules\genericormapper\data\tools\GenericORMapperDomainObjectGenerator;
use APF\tools\filesystem\Folder;
use APF\tools\form\taglib\HtmlFormTag;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Tests domain object / DTO generation for the Generic O/R Mapper.
 */
class GenericORMapperDomainObjectGeneratorTest extends \PHPUnit_Framework_TestCase {

   /**
    * @var ConfigurationProvider
    */
   protected static $configProvider;

   public static function setUpBeforeClass() {

      // save original config provider
      self::$configProvider = ConfigurationManager::retrieveProvider('ini');

      /* @var $provider IniConfigurationProvider */
      $provider = clone self::$configProvider;
      $provider->setOmitConfigSubFolder(true);
      $provider->setOmitContext(false);
      ConfigurationManager::registerProvider('ini', $provider);

      // generate test files
      $gen = new GenericORMapperDomainObjectGenerator();
      $gen->setContext('test-config');

      $gen->addMappingConfiguration(__NAMESPACE__, 'test');
      $gen->addDomainObjectsConfiguration(__NAMESPACE__, 'test');

      $gen->generateServiceObjects();

   }

   public static function tearDownAfterClass() {
      // restore previous INI configuration provider
      ConfigurationManager::registerProvider('ini', self::$configProvider);

      // remove generated test classes
      (new Folder())->open(__DIR__ . '/test')->delete();
   }

   /**
    * Tests whether "real" object properties are generated to allow usage of the form fill mechanism.
    */
   public function testGenerateBaseObjectCode() {

      $gen = new GenericORMapperDomainObjectGenerator();

      $dtoTableProperty = new ReflectionProperty(GenericORMapperDomainObjectGenerator::class, 'domainObjectsTable');
      $dtoTableProperty->setAccessible(true);
      $dtoTableProperty->setValue(
            $gen,
            [
                  'Dummy' =>
                        [
                              'Class' => __NAMESPACE__ . '\test\Dummy'
                        ]
            ]
      );

      $mappingTableProperty = new ReflectionProperty(GenericORMapperDomainObjectGenerator::class, 'mappingTable');
      $mappingTableProperty->setAccessible(true);
      $mappingTableProperty->setValue(
            $gen,
            [
                  'Dummy' =>
                        [
                              'ID' => 'DummyID',
                              'Table' => 'ent_dummy',
                              'Name' => 'VARCHAR(20)',
                              'Value' => 'VARCHAR(20)'
                        ]
            ]
      );

      $method = new ReflectionMethod(GenericORMapperDomainObjectGenerator::class, 'generateBaseObjectCode');
      $method->setAccessible(true);
      $code = $method->invokeArgs($gen, ['Dummy']);

      $this->assertContains('protected $Name;', $code);
      $this->assertContains('protected $Value;', $code);
      $this->assertContains('protected $DummyID;', $code);

      $this->assertContains('protected $CreationTimestamp;', $code);
      $this->assertContains('protected $ModificationTimestamp;', $code);

      $this->assertContains('protected $propertyNames = [', $code);

      $this->assertContains('public function setProperty($name, $value)', $code);
      $this->assertContains('public function getProperties()', $code);
      $this->assertContains('public function setProperties($properties = [])', $code);
      $this->assertContains('public function deleteProperty($name)', $code);

      $this->assertContains('public function setObjectId($id)', $code);
      $this->assertContains('public function getObjectId()', $code);

      $this->assertContains('public function getName()', $code);
      $this->assertContains('public function setName($value)', $code);
      $this->assertContains('public function deleteName()', $code);

      $this->assertContains('public function getValue()', $code);
      $this->assertContains('public function setValue($value) ', $code);
      $this->assertContains('public function deleteValue()', $code);

      // test dynamic sleep property generation
      $sleepPropertiesCode = '   public function __sleep() {' . GenericORMapperDomainObjectGenerator::EOL .
            '      return [' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'objectName\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'DummyID\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'CreationTimestamp\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'ModificationTimestamp\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'Name\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'Value\',' . GenericORMapperDomainObjectGenerator::EOL .
            '            \'relatedObjects\'' . GenericORMapperDomainObjectGenerator::EOL .
            '      ];' . GenericORMapperDomainObjectGenerator::EOL .
            '   }';
      $this->assertContains($sleepPropertiesCode, $code);

      // test dynamic property name generation
      $propertyNamesCode = '   protected $propertyNames = [' . GenericORMapperDomainObjectGenerator::EOL .
            '       \'DummyID\',' . GenericORMapperDomainObjectGenerator::EOL .
            '       \'CreationTimestamp\',' . GenericORMapperDomainObjectGenerator::EOL .
            '       \'ModificationTimestamp\',' . GenericORMapperDomainObjectGenerator::EOL .
            '       \'Name\',' . GenericORMapperDomainObjectGenerator::EOL .
            '       \'Value\'' . GenericORMapperDomainObjectGenerator::EOL .
            '    ];';
      $this->assertContains($propertyNamesCode, $code);

   }

   /**
    * Test basic DTO functions.
    */
   public function testDto() {

      $object = new \APF\tests\suites\modules\genericormapper\tools\test\Dummy();

      // test object ID definition
      $this->assertNull($object->getObjectId());
      $expected = 42;
      $object->setObjectId($expected);
      $this->assertEquals($expected, $object->getObjectId());
      $this->assertEquals($expected, $object->getProperty('DummyID'));
      $object->deleteProperty('DummyID');

      // test property setting
      $object->setProperty('Foo', 'Bar');
      $this->assertNull($object->getProperty('Foo'));

      $object->setProperty('Name', 'Foo');
      $this->assertEquals('Foo', $object->getProperty('Name'));
      $object->deleteProperty('Name');

      // test setting multiple properties
      $expected = [
            'DummyID' => 42,
            'Name' => 'Foo',
            'Value' => 'Bar'
      ];
      $object->setProperties(array_merge($expected, ['Foo' => 'Bar']));

      $this->assertEquals($expected, $object->getProperties());

      // test convenience methods
      $object->deleteProperty('DummyID');
      $object->deleteProperty('Name');
      $object->deleteProperty('Value');

      $this->assertNotNull($object->getProperties());
      $this->assertEmpty($object->getProperties());

      $object->setProperty('DummyID', 42);
      $this->assertEquals(42, $object->getObjectId());

      $object->setName('Foo');
      $this->assertEquals('Foo', $object->getName());

      $object->setValue('Bar');
      $this->assertEquals('Bar', $object->getValue());

      $this->assertEquals($expected, $object->getProperties());

      $object->deleteName();
      $this->assertNull($object->getName());

      $object->deleteValue();
      $this->assertNull($object->getValue());

      $creationTimestamp = '2017-09-02 17:46:21';
      $modificationTimestamp = '0000-00-00 00:00:00';
      $object->setProperties([
            'CreationTimestamp' => $creationTimestamp,
            'ModificationTimestamp' => $modificationTimestamp
      ]);
      $this->assertEquals($creationTimestamp, $object->getCreationTimestamp());
      $this->assertEquals($modificationTimestamp, $object->getModificationTimestamp());

   }

   /**
    * Test form mapping capabilities for "new" domain objects.
    */
   public function testFormMapping() {

      $_REQUEST = [];
      $_POST = [];
      $_GET = [];

      $form = new HtmlFormTag();

      $doc = new Document();
      $form->setParentObject($doc);

      $form->setAttribute('name', 'foo');
      $form->setContent('<form:text name="Name" />
      <form:text name="Value" />
      <form:button name="send" value="send" />');

      $form->onParseTime();
      $form->onAfterAppend();

      $name = $form->getFormElementByName('Name');
      $this->assertEmpty($name->getValue());

      $value = $form->getFormElementByName('Value');
      $this->assertEmpty($value->getValue());

      // test form presetting
      $model = new \APF\tests\suites\modules\genericormapper\tools\test\Dummy();
      $model->setName('Foo');
      $model->setValue('Bar');

      $form->fillForm($model);

      $this->assertEquals('Foo', $name->getValue());
      $this->assertEquals('Bar', $value->getValue());

      // test model presetting
      $model = new \APF\tests\suites\modules\genericormapper\tools\test\Dummy();

      $this->assertNull($model->getName());
      $this->assertNull($model->getValue());

      $form->fillModel($model);

      $this->assertEquals('Foo', $model->getName());
      $this->assertEquals('Bar', $model->getValue());

   }

}
