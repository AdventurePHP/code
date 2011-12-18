<?php
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

/**
 * @package tests::suites::core::registry
 * @class RegistryPositivTest
 * 
 * Test methods of registry class with valid parameters<br />
 *
 * @author Florian Horn
 * @version
 * Version 0.1, 17.12.2011<br />
 */

import('core::registry','Registry');
class RegistryPositivTest extends PHPUnit_Framework_TestCase
{
    /**
     * Registry namespace of the key
     * @var string
     */
    static protected $REGISTRY_NAMESPACE = 'apf::test::registry';
    
    /**
     * Registry key
     * @var string
     */
    static protected $REGISTRY_NAME = 'some-key';
    
    /**
     * Registry value
     * @var string
     */
    static protected $REGISTRY_VALUE = 'some-value';
    
    
    /**
     * A default value for the Registry::retrieve() method
     * @var string 
     */
    static protected $REGISTRY_DEFAULT_VALUE = 'some-default-value';
    
    
    
    /**
     * 
     */
    public function testConstructorNotCallable()
    {
        $oReflectionRegistry = new ReflectionClass('Registry'); 
        assertFalse($oReflectionRegistry->isInstantiable(),'Registry object should not be abled to be instanced!');
    }
    
    
    
    /**
     * @depends testConstructorNotCallable
     */
    public function testStaticClassHasFinalAttribute()
    {
        $oReflectionRegistry = new ReflectionClass('Registry'); 
        assertTrue($oReflectionRegistry->isFinal(),'Registry object should be declared as final!');
    }
    
    
    
    /**
     * @depends testConstructorNotCallable
     */
    public function testMethodsExisting()
    {
        $oReflectionRegistry = new ReflectionClass('Registry');
        assertTrue( $oReflectionRegistry->hasMethod('register') );
        assertTrue( $oReflectionRegistry->hasMethod('retrieve') );
    }
    
    
    
    /**
     * @depends testMethodsExisting
     */
    public function testRegisterDefaultMethod()
    {
        $oReflectionRegistry = new ReflectionClass('Registry'); 
        $aProperties = $oReflectionRegistry->getStaticProperties();
        $aRegistryStore = $aProperties['REGISTRY_STORE'];
        // --- Prefilled with default values
        assertEquals(1, count($aRegistryStore) );
        assertEquals(9, count($aRegistryStore['apf::core']) );
        
        Registry::register(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_VALUE);
        
        $aProperties = $oReflectionRegistry->getStaticProperties();
        $aRegistryStore = $aProperties['REGISTRY_STORE'];
        assertEquals(2, count($aRegistryStore) );
        assertEquals(9, count($aRegistryStore['apf::core']) );
        assertEquals(1, count($aRegistryStore[self::$REGISTRY_NAMESPACE]) );
        assertTrue( array_key_exists( self::$REGISTRY_NAME, $aRegistryStore[self::$REGISTRY_NAMESPACE] ) );
        assertTrue( array_key_exists( 'value', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME] ) );
        assertTrue( array_key_exists( 'readonly', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME] ) );
        assertEquals( self::$REGISTRY_VALUE, $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['value'] );
        assertFalse( $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['readonly'] );
    }
    
    
    
    /**
     * @depends testRegisterDefaultMethod
     */
    public function testRegisterReadonlyMethod()
    {
        Registry::register(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_VALUE,
                true);
        
        $oReflectionRegistry = new ReflectionClass('Registry'); 
        $aProperties = $oReflectionRegistry->getStaticProperties();
        $aRegistryStore = $aProperties['REGISTRY_STORE'];
        assertEquals(2, count($aRegistryStore) );
        assertEquals(9, count($aRegistryStore['apf::core']) );
        assertEquals(1, count($aRegistryStore[self::$REGISTRY_NAMESPACE]) );
        assertTrue( array_key_exists( self::$REGISTRY_NAME, $aRegistryStore[self::$REGISTRY_NAMESPACE] ) );
        assertTrue( array_key_exists( 'value', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME] ) );
        assertTrue( array_key_exists( 'readonly', $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME] ) );
        assertEquals( self::$REGISTRY_VALUE, $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['value'] );
        assertTrue( $aRegistryStore[self::$REGISTRY_NAMESPACE][self::$REGISTRY_NAME]['readonly'] );
    }
    
    
    
    /**
     * @depends testRegisterReadonlyMethod
     * @expectedException InvalidArgumentException
     */
    public function testRegisterMethodReadonlyEffectWithOverwriteTry()
    {
        Registry::register(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_VALUE,
                true);
        
        Registry::register(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_VALUE );
    }
    
    
    
    /**
     * @depends testRegisterReadonlyMethod
     */
    public function testRetrieveMethod()
    {
        Registry::register(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_VALUE,
                true);
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME);
        assertEquals( self::$REGISTRY_VALUE, $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME,
                self::$REGISTRY_DEFAULT_VALUE);
        assertEquals( self::$REGISTRY_VALUE, $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE.'42', 
                self::$REGISTRY_NAME);
        assertNull( $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE.'42', 
                self::$REGISTRY_NAME,
                self::$REGISTRY_DEFAULT_VALUE);
        assertEquals( self::$REGISTRY_DEFAULT_VALUE, $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME.'42');
        assertNull( $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE, 
                self::$REGISTRY_NAME.'42',
                self::$REGISTRY_DEFAULT_VALUE);
        assertEquals( self::$REGISTRY_DEFAULT_VALUE, $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE.'42', 
                self::$REGISTRY_NAME.'42');
        assertNull( $sReturnValue );
        
        $sReturnValue = Registry::retrieve(
                self::$REGISTRY_NAMESPACE.'42', 
                self::$REGISTRY_NAME.'42',
                self::$REGISTRY_DEFAULT_VALUE);
        assertEquals( self::$REGISTRY_DEFAULT_VALUE, $sReturnValue );
    }
}

?>