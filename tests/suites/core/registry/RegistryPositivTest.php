<?php
namespace APF\tests\suites\core\registry;

/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
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
use APF\core\registry\Registry;

/**
 * @package APF\tests\suites\core\registry
 * @class RegistryPositivTest
 *
 * Test methods of registry class with valid parameters<br />
 *
 * @author Florian Horn
 * @version
 * Version 0.1, 17.12.2011<br />
 */
class RegistryPositivTest extends PHPUnit_Framework_TestCase {

   /**
    * @protected static
    * @var string Registry namespace of the key
    */
   protected static $REGISTRY_NAMESPACE = 'APF\test\registry';

   /**
    * @protected static
    * @var string Registry key
    */
   protected static $REGISTRY_NAME = 'some-key';

   /**
    * @protected static
    * @var string Registry value
    */
   protected static $REGISTRY_VALUE = 'some-value';

   /**
    * @protected static
    * @var string A default value for the Registry::retrieve() method
    */
   protected static $REGISTRY_DEFAULT_VALUE = 'some-default-value';

   /**
    * @public
    *
    * Tests constructor method to not be callable because the class is declared
    * as static.
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testConstructorNotCallable() {
      $oReflectionRegistry = new \ReflectionClass('Registry');
      assertFalse($oReflectionRegistry->isInstantiable(), 'Registry object should not be abled to be instanced!');
   }

   /**
    * @public
    * @depends testConstructorNotCallable
    *
    * Tests the class' final declaration
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testStaticClassHasFinalAttribute() {
      $oReflectionRegistry = new \ReflectionClass('Registry');
      assertTrue($oReflectionRegistry->isFinal(), 'Registry object should be declared as final!');
   }

   /**
    * @public
    * @depends testConstructorNotCallable
    *
    * Tests existing of certain methods
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testMethodsExisting() {
      $oReflectionRegistry = new \ReflectionClass('Registry');
      assertTrue($oReflectionRegistry->hasMethod('register'));
      assertTrue($oReflectionRegistry->hasMethod('retrieve'));
   }

   /**
    * @public
    * @depends testMethodsExisting
    *
    * Tests default usage
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testRegisterDefaultMethod() {
      $oReflectionRegistry = new \ReflectionClass('Registry');
      $aProperties = $oReflectionRegistry->getStaticProperties();
      $aRegistryStore = $aProperties['REGISTRY_STORE'];
      // --- Prefilled with default values
      assertEquals(1, count($aRegistryStore));
      assertEquals(7, count($aRegistryStore['apf::core']));

      Registry::register(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_VALUE);

      $aProperties = $oReflectionRegistry->getStaticProperties();
      $aRegistryStore = $aProperties['REGISTRY_STORE'];
      assertEquals(2, count($aRegistryStore));
      assertEquals(7, count($aRegistryStore['apf::core']));
      assertEquals(1, count($aRegistryStore[self::$REGISTRY_NAMESPACE]));
      assertTrue(array_key_exists(self::$REGISTRY_NAME, $aRegistryStore[self::$REGISTRY_NAMESPACE]));
      assertTrue(array_key_exists('value', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]));
      assertTrue(array_key_exists('readonly', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]));
      assertEquals(self::$REGISTRY_VALUE, $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['value']);
      assertFalse($aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['readonly']);
   }

   /**
    * @public
    * @depends testRegisterDefaultMethod
    *
    * Tests default usage with readonly flag set to true
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testRegisterReadonlyMethod() {
      Registry::register(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_VALUE,
         true);

      $oReflectionRegistry = new \ReflectionClass('Registry');
      $aProperties = $oReflectionRegistry->getStaticProperties();
      $aRegistryStore = $aProperties['REGISTRY_STORE'];
      assertEquals(2, count($aRegistryStore));
      assertEquals(7, count($aRegistryStore['apf::core']));
      assertEquals(1, count($aRegistryStore[self::$REGISTRY_NAMESPACE]));
      assertTrue(array_key_exists(self::$REGISTRY_NAME, $aRegistryStore[self::$REGISTRY_NAMESPACE]));
      assertTrue(array_key_exists('value', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]));
      assertTrue(array_key_exists('readonly', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]));
      assertEquals(self::$REGISTRY_VALUE, $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['value']);
      assertTrue($aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['readonly']);
   }

   /**
    * @public
    * @depends testRegisterReadonlyMethod
    * @expectedException \InvalidArgumentException
    *
    * Tests default usage with readonly flag set to true and the throw of an
    * exception if the register method is called again for the same key
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testRegisterMethodReadonlyEffectWithOverwriteTry() {
      Registry::register(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_VALUE,
         true);

      Registry::register(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_VALUE);
   }

   /**
    * @public
    * @depends testRegisterReadonlyMethod
    *
    * Tests default usage of the retrieve method
    *
    * @return void
    *
    * @author Florian Horn
    * @version
    * Version 0.1, 17.12.2011<br />
    */
   public function testRetrieveMethod() {
      Registry::register(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_VALUE,
         true);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME);
      assertEquals(self::$REGISTRY_VALUE, $sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE,
         self::$REGISTRY_NAME,
         self::$REGISTRY_DEFAULT_VALUE);
      assertEquals(self::$REGISTRY_VALUE, $sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE . '42',
         self::$REGISTRY_NAME);
      assertNull($sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE . '42',
         self::$REGISTRY_NAME,
         self::$REGISTRY_DEFAULT_VALUE);
      assertEquals(self::$REGISTRY_DEFAULT_VALUE, $sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE,
            self::$REGISTRY_NAME . '42');
      assertNull($sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE,
            self::$REGISTRY_NAME . '42',
         self::$REGISTRY_DEFAULT_VALUE);
      assertEquals(self::$REGISTRY_DEFAULT_VALUE, $sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE . '42',
            self::$REGISTRY_NAME . '42');
      assertNull($sReturnValue);

      $sReturnValue = Registry::retrieve(
         self::$REGISTRY_NAMESPACE . '42',
            self::$REGISTRY_NAME . '42',
         self::$REGISTRY_DEFAULT_VALUE);
      assertEquals(self::$REGISTRY_DEFAULT_VALUE, $sReturnValue);
   }
}
