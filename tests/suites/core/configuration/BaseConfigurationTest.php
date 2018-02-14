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

use APF\core\configuration\provider\ini\IniConfiguration;

/**
 * Tests the BaseConfiguration class' functionality.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 11.03.2015 (see ID#224 for requirement details)<br />
 */
class BaseConfigurationTest extends \PHPUnit_Framework_TestCase {

   public function testSetSection() {

      $config = new IniConfiguration();
      $section = new IniConfiguration();

      $subSection = new IniConfiguration();
      $subSection->setValue('ValueOne', 'foo');
      $subSection->setValue('ValueTwo', 'bar');

      $section->setSection('SubSection', $subSection);

      $config->setSection('Section', $section);

      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));

   }

   public function testSetSectionWithPathExpression() {

      $subSection = new IniConfiguration();
      $subSection->setValue('ValueOne', 'foo');
      $subSection->setValue('ValueTwo', 'bar');

      $config = new IniConfiguration();
      $config->setSection('Section.SubSection', $subSection);

      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));
   }

   public function testSetValue() {

      $config = new IniConfiguration();
      $section = new IniConfiguration();

      $subSection = new IniConfiguration();
      $section->setSection('SubSection', $subSection);

      $config->setSection('Section', $section);

      $config->getSection('Section')->getSection('SubSection')->setValue('ValueOne', 'foo');
      $config->getSection('Section')->getSection('SubSection')->setValue('ValueTwo', 'bar');

      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));

   }

   public function testSetValueWithPathExpression() {

      $subSection = new IniConfiguration();

      $config = new IniConfiguration();
      $config->setSection('Section.SubSection', $subSection);

      $config->setValue('Section.SubSection.ValueOne', 'foo');
      $config->setValue('Section.SubSection.ValueTwo', 'bar');

      $this->assertEquals('foo', $config->getSection('Section')->getSection('SubSection')->getValue('ValueOne'));
      $this->assertEquals('bar', $config->getSection('Section')->getSection('SubSection')->getValue('ValueTwo'));

   }

   public function testRemoveSection() {

      $config = new IniConfiguration();
      $this->assertFalse($config->hasSection('Section'));

      $section = new IniConfiguration();
      $config->setSection('Section', $section);

      $config->removeSection('Section');

      $this->assertFalse($config->hasSection('Section'));

   }

   public function testRemoveSectionWithPathExpression() {

      $subSection = new IniConfiguration();

      $config = new IniConfiguration();
      $config->setSection('Section.SubSection', $subSection);

      $this->assertTrue($config->hasSection('Section.SubSection'));

      $config->removeSection('Section.SubSection');

      $this->assertFalse($config->hasSection('Section.SubSection'));

   }

   public function testRemoveValue() {

      $config = new IniConfiguration();
      $this->assertFalse($config->hasValue('ValueOne'));

      $config->setValue('ValueOne', 'foo');
      $this->assertTrue($config->hasValue('ValueOne'));
      $this->assertEquals('foo', $config->getValue('ValueOne'));

      $config->removeValue('ValueOne');

      $this->assertFalse($config->hasValue('ValueOne'));

   }

   public function testRemoveValueWithPathExpression() {

      $config = new IniConfiguration();
      $section = new IniConfiguration();

      $subSection = new IniConfiguration();
      $subSection->setValue('ValueOne', 'foo');
      $subSection->setValue('ValueTwo', 'bar');

      $section->setSection('SubSection', $subSection);

      $config->setSection('Section', $section);

      $this->assertTrue($config->hasValue('Section.SubSection.ValueOne'));
      $this->assertTrue($config->hasValue('Section.SubSection.ValueTwo'));

      $config->removeValue('Section.SubSection.ValueOne');

      $this->assertFalse($config->hasValue('Section.SubSection.ValueOne'));
      $this->assertTrue($config->hasValue('Section.SubSection.ValueTwo'));

   }

}
