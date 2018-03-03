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
namespace APF\tests\suites\core\singleton;

use APF\core\singleton\Singleton;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * Tests singleton object creation capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.04.2015<br />
 */
class SingletonTest extends TestCase {

   const TEST_TAG = 'test';
   const MODEL_CLASS = TagModel::class;
   const INSTANCE_ID = 'test-id';

   /**
    * @throws Exception
    */
   public function testSimpleCreation() {

      Singleton::deleteInstance(self::MODEL_CLASS);

      /* @var $model TagModel */
      $model = Singleton::getInstance(self::MODEL_CLASS);
      $model->setTag(self::TEST_TAG);

      $model = Singleton::getInstance(self::MODEL_CLASS);
      $this->assertEquals(self::TEST_TAG, $model->getTag());

   }

   /**
    * @throws Exception
    */
   public function testConstructorCreation() {

      Singleton::deleteInstance(self::MODEL_CLASS);

      /* @var $model TagModel */
      $model = Singleton::getInstance(self::MODEL_CLASS, [self::TEST_TAG]);
      $this->assertEquals(self::TEST_TAG, $model->getTag());

   }

   /**
    * @throws Exception
    */
   public function testSimpleInstanceIdCreation() {

      Singleton::deleteInstance(self::MODEL_CLASS);
      Singleton::deleteInstance(self::MODEL_CLASS, self::INSTANCE_ID);

      /* @var $model TagModel */
      $model = Singleton::getInstance(self::MODEL_CLASS, [], self::INSTANCE_ID);
      $model->setTag(self::TEST_TAG);

      $model = Singleton::getInstance(self::MODEL_CLASS);
      $this->assertNotEquals(self::TEST_TAG, $model->getTag());

      $model = Singleton::getInstance(self::MODEL_CLASS, [], self::INSTANCE_ID);
      $this->assertEquals(self::TEST_TAG, $model->getTag());

   }

   /**
    * @throws Exception
    */
   public function testConstructorInstanceIdCreation() {

      Singleton::deleteInstance(self::MODEL_CLASS, self::INSTANCE_ID);

      /* @var $model TagModel */
      $model = Singleton::getInstance(self::MODEL_CLASS, [self::TEST_TAG], self::INSTANCE_ID);
      $this->assertEquals(self::TEST_TAG, $model->getTag());

   }

   /**
    * @throws Exception
    */
   public function testInstanceDeletion() {

      /* @var $model TagModel */
      Singleton::getInstance(self::MODEL_CLASS, [self::TEST_TAG]);

      $model = Singleton::getInstance(self::MODEL_CLASS);
      $this->assertNotNull($model->getTag());

      Singleton::deleteInstance(self::MODEL_CLASS);

      $model = Singleton::getInstance(self::MODEL_CLASS);
      $this->assertNull($model->getTag());

   }

   /**
    * @throws Exception
    */
   public function testInstanceWithIdDeletion() {

      /* @var $model TagModel */
      Singleton::getInstance(self::MODEL_CLASS, [self::TEST_TAG], self::INSTANCE_ID);

      $model = Singleton::getInstance(self::MODEL_CLASS, [], self::INSTANCE_ID);
      $this->assertNotNull($model->getTag());

      Singleton::deleteInstance(self::MODEL_CLASS, self::INSTANCE_ID);

      $model = Singleton::getInstance(self::MODEL_CLASS, [], self::INSTANCE_ID);
      $this->assertNull($model->getTag());

   }

}
