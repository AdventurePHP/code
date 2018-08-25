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
use APF\modules\genericormapper\data\BaseMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Tests BaseMapper capabilities.
 */
class BaseMapperTest extends TestCase {

   /**
    * @return array Test data.
    */
   public function tableOptionDataProvider() {
      return [
            'All tables w/o prefixes (default)' => [
                  '', '', '', ''
            ],
            'All tables w/ prefixes' => [
                  'abc', 'def', 'ghi', 'jkl'
            ],
            'Only object tables w/ prefixes' => [
                  'abc', 'def', '', ''
            ],
            'Only relation tables w/ prefixes' => [
                  '', '', 'uvw', 'xyz'
            ],
            'Mixed setup' => [
                  'abc', '', '', 'xyz'
            ]
      ];
   }

   /**
    * ID#27: Test whether mapper generates tables w/ and w/o prefixes.
    *
    * @dataProvider tableOptionDataProvider
    *
    * @param string $dadPrefix Table prefix for object "Dad".
    * @param string $sonPrefix Table prefix for object "Son".
    * @param string $dad2SonPrefix Table prefix for relation "Dad2Son".
    * @param string $friendsPrefix Table prefix for relation "Friends".
    */
   public function testTableNames($dadPrefix, $sonPrefix, $dad2SonPrefix, $friendsPrefix) {

      $dataType = 'VARCHAR(100)';
      $relationTypeComposition = 'COMPOSITION';
      $relationTypeAssociation = 'ASSOCIATION';

      $objects = new IniConfiguration();

      $objects->setValue('Dad.Name', $dataType);
      if (!empty($dadPrefix)) {
         $objects->setValue('Dad.TablePrefix', $dadPrefix);
      }

      $objects->setValue('Son.Name', $dataType);
      if (!empty($sonPrefix)) {
         $objects->setValue('Son.TablePrefix', $sonPrefix);
      }

      $relations = new IniConfiguration();

      $relations->setValue('Dad2Son.Type', $relationTypeComposition);
      if (!empty($dad2SonPrefix)) {
         $relations->setValue('Dad2Son.TablePrefix', $dad2SonPrefix);
      }
      $relations->setValue('Dad2Son.SourceObject', 'Dad');
      $relations->setValue('Dad2Son.TargetObject', 'Son');

      $relations->setValue('Friends.Type', $relationTypeAssociation);
      if (!empty($friendsPrefix)) {
         $relations->setValue('Friends.TablePrefix', $friendsPrefix);
      }
      $relations->setValue('Friends.SourceObject', 'Son');
      $relations->setValue('Friends.TargetObject', 'Son');

      /* @var $mapper BaseMapper|MockObject */
      $mapper = $this->getMockBuilder(BaseMapper::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $mapper->method('getConfiguration')
            ->willReturnMap(
                  [
                        [
                              'foo',
                              'bar_objects.ini',
                              $objects
                        ],
                        [
                              'foo',
                              'bar_relations.ini',
                              $relations
                        ]
                  ]
            );

      $mapper->addMappingConfiguration('foo', 'bar');
      $mapper->addRelationConfiguration('foo', 'bar');

      $mappingTableProperty = new ReflectionProperty(BaseMapper::class, 'mappingTable');
      $mappingTableProperty->setAccessible(true);
      $mappingTable = $mappingTableProperty->getValue($mapper);

      $this->assertCount(2, $mappingTable);

      // check for correct setup for "Dad"
      $this->assertEquals($dataType, $mappingTable['Dad']['Name']);
      if (!empty($dadPrefix)) {
         $this->assertEquals($dadPrefix . '_ent_dad', $mappingTable['Dad']['Table']);
      } else {

         $this->assertEquals('ent_dad', $mappingTable['Dad']['Table']);
      }
      $this->assertEquals('DadID', $mappingTable['Dad']['ID']);

      // check for correct setup for "Son"
      $this->assertEquals($dataType, $mappingTable['Son']['Name']);
      if (!empty($sonPrefix)) {
         $this->assertEquals($sonPrefix . '_ent_son', $mappingTable['Son']['Table']);
      } else {

         $this->assertEquals('ent_son', $mappingTable['Son']['Table']);
      }
      $this->assertEquals('SonID', $mappingTable['Son']['ID']);

      $relationTableProperty = new ReflectionProperty(BaseMapper::class, 'relationTable');
      $relationTableProperty->setAccessible(true);
      $relationTable = $relationTableProperty->getValue($mapper);

      $this->assertCount(2, $relationTable);

      // check for correct setup of "Dad2Son"
      $this->assertEquals($relationTypeComposition, $relationTable['Dad2Son']['Type']);
      $this->assertEquals('Dad', $relationTable['Dad2Son']['SourceObject']);
      $this->assertEquals('Son', $relationTable['Dad2Son']['TargetObject']);
      if (!empty($dad2SonPrefix)) {
         $this->assertEquals($dad2SonPrefix . '_cmp_dad2son', $relationTable['Dad2Son']['Table']);
      } else {
         $this->assertEquals('cmp_dad2son', $relationTable['Dad2Son']['Table']);
      }
      $this->assertEquals('Source_DadID', $relationTable['Dad2Son']['SourceID']);
      $this->assertEquals('Target_SonID', $relationTable['Dad2Son']['TargetID']);

      // check for correct setup of "Friends"
      $this->assertEquals($relationTypeAssociation, $relationTable['Friends']['Type']);
      $this->assertEquals('Son', $relationTable['Friends']['SourceObject']);
      $this->assertEquals('Son', $relationTable['Friends']['TargetObject']);
      if (!empty($friendsPrefix)) {
         $this->assertEquals($friendsPrefix . '_ass_friends', $relationTable['Friends']['Table']);
      } else {
         $this->assertEquals('ass_friends', $relationTable['Friends']['Table']);
      }
      $this->assertEquals('Source_SonID', $relationTable['Friends']['SourceID']);
      $this->assertEquals('Target_SonID', $relationTable['Friends']['TargetID']);

   }

}
