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
namespace APF\tests\suites\core\frontcontroller;

use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\frontcontroller\Action;
use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\Frontcontroller;
use APF\core\frontcontroller\FrontcontrollerInput;
use APF\core\http\RequestImpl;
use APF\core\http\ResponseImpl;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Tests various front controller capabilities.
 */
class FrontcontrollerTest extends TestCase {

   public function testStart() {

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getRequest', 'getResponse', 'runActions'])
            ->getMock();

      $fC->expects($this->once())
            ->method('getRequest')
            ->willReturn(new RequestImpl());
      $fC->expects($this->once())
            ->method('getResponse')
            ->willReturn(new ResponseImpl());

      // set expectations for timing model
      $fC->expects($this->exactly(3))
            ->method('runActions')
            ->withConsecutive(
                  [Action::TYPE_PRE_PAGE_CREATE],
                  [Action::TYPE_PRE_TRANSFORM],
                  [Action::TYPE_POST_TRANSFORM]
            );

      // ID#317: define context to guarantee correct application behavior
      $fC->setContext('dummy');

      $response = $fC->start(__NAMESPACE__ . '\templates', 'main');

      // check whether returned response contains correct content
      $this->assertInstanceOf(ResponseImpl::class, $response);
      $this->assertEquals('This is my page content.', $response->getBody());
   }

   /**
    * ID#317: Tests whether front controller throws exception when not properly initialized to avoid context clashes.
    */
   public function testWrongInitialization() {
      $this->expectException(Exception::class);
      $this->expectExceptionMessage('Front controller requires context initialization to guarantee accurate '
            . 'application execution! Please inject desired context via $fC->setContext(\'...\').');
      $fC = new Frontcontroller();
      $fC->start('foo', 'bar');
   }

   /**
    * Tests registration of URL mappings via configuration file.
    */
   public function testRegisterActionUrlMappings() {

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration', 'registerActionUrlMapping'])
            ->getMock();

      $namespace = 'VENDOR\actions';

      $config = new IniConfiguration();
      $config->setValue('Foo.ActionNamespace', $namespace);
      $config->setValue('Foo.ActionName', 'FooAction');
      $config->setValue('Bar.ActionNamespace', $namespace);
      $config->setValue('Bar.ActionName', 'BarAction');

      $fC->expects($this->exactly(2))
            ->method('registerActionUrlMapping')
            ->withConsecutive(
                  [new ActionUrlMapping('Foo', $namespace, 'FooAction')],
                  [new ActionUrlMapping('Bar', $namespace, 'BarAction')]
            );
      $fC->method('getConfiguration')
            ->willReturn($config);

      $fC->registerActionUrlMappings(null, null);
   }

   /**
    * Tests whether missing configuration entry leads to an exception.
    */
   public function testAddAction1() {

      $this->expectException(InvalidArgumentException::class);

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn(new IniConfiguration());

      $fC->addAction(null, null);
   }

   /**
    * Tests "classic" action generation as simple instance with *non-existing* class.
    */
   public function testAddAction2() {

      $this->expectException(InvalidArgumentException::class);

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $namespace = 'VENDOR\actions';
      $name = 'Foo';


      $config = new IniConfiguration();
      $config->setValue($name . '.ActionClass', 'VENDOR\actions\FooAction');

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      $fC->addAction($namespace, $name);
   }

   /**
    * Tests "classic" action generation as simple instance with *existing* class.
    */
   public function testAddAction3() {

      $context = 'context';
      $lang = 'es';

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration'])
            ->getMock();
      $fC->setContext($context);
      $fC->setLanguage($lang);

      $namespace = 'VENDOR\actions';
      $name = 'Foo';

      $config = new IniConfiguration();
      $config->setValue($name . '.ActionClass', SimpleTestAction::class);
      $config->setValue($name . '.InputParams', 'foo:bar|bar:baz');

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      // register action with pseudo-dynamic parameters (e.g. via URL as inserted by input filters)
      $fC->addAction($namespace, $name, ['test' => '123', 'bar' => '2']);

      // check whether action is registered
      $this->assertNotEmpty($fC->getActions());
      $action = $fC->getActionByName($name);
      $this->assertNotEmpty($action);

      // check whether environment variables have been injected
      $this->assertEquals($context, $action->getContext());
      $this->assertEquals($lang, $action->getLanguage());

      // check whether input representation has been created correctly
      $input = $action->getInput();
      $this->assertInstanceOf(FrontcontrollerInput::class, $input);
      $this->assertEquals('bar', $input->getParameter('foo'));
      $this->assertEquals('bar', $input->getParameter('non-existing', 'bar'));

      $this->assertEquals(['foo' => 'bar', 'bar' => '2', 'test' => '123'], $input->getParameters());

      $this->assertEquals($action, $input->getAction());

      // check whether front controller is known to action
      $this->assertEquals($fC, $action->getFrontController());
   }

   /**
    * Tests "classic" action generation w/ *non-existing* custom input implementation.
    */
   public function testAddAction4() {

      $this->expectException(InvalidArgumentException::class);

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $namespace = 'VENDOR\actions';
      $name = 'Foo';

      $config = new IniConfiguration();
      $config->setValue($name . '.ActionClass', SimpleTestAction::class);
      $config->setValue($name . '.InputClass', 'VENDOR\actions\NonExistingAction');

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      $fC->addAction($namespace, $name);
   }

   /**
    * Tests "classic" action generation w/ custom input implementation.
    */
   public function testAddAction5() {

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration'])
            ->getMock();

      $namespace = 'VENDOR\actions';
      $name = 'Foo';

      $config = new IniConfiguration();
      $config->setValue($name . '.ActionClass', SimpleTestAction::class);
      $config->setValue($name . '.InputClass', CustomActionInput::class);

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      $fC->addAction($namespace, $name);

      // check whether action is registered
      $action = $fC->getActionByName($name);
      $this->assertNotEmpty($action);

      // check whether input representation has been created correctly
      $input = $action->getInput();
      $this->assertInstanceOf(CustomActionInput::class, $input);
   }

   /**
    * Tests "classic" action generation w/ action mapping.
    */
   public function testAddAction6() {

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration', 'registerActionUrlMapping'])
            ->getMock();

      $namespace = 'VENDOR\actions';
      $name = 'Foo';
      $urlToken = 'foo';

      $config = new IniConfiguration();
      $config->setValue($name . '.ActionClass', SimpleTestAction::class);

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      $fC->expects($this->once())
            ->method('registerActionUrlMapping')
            ->with(new ActionUrlMapping($urlToken, $namespace, $name));

      $fC->addAction($namespace, $name, [], $urlToken);
   }

   /**
    * Test action creation via DIServiceManager.
    */
   public function testAddAction7() {

      /* @var $fC Frontcontroller|MockObject */
      $fC = $this->getMockBuilder(Frontcontroller::class)
            ->setMethods(['getConfiguration', 'getDIServiceObject'])
            ->getMock();

      $namespace = 'VENDOR\actions';
      $name = 'Foo';

      $config = new IniConfiguration();
      $config->setValue($name . '.ActionServiceName', $name);
      $config->setValue($name . '.ActionServiceNamespace', $namespace);

      $fC->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($config);

      $action = new SimpleTestAction();
      $fC->expects($this->once())
            ->method('getDIServiceObject')
            ->willReturn($action);

      $fC->addAction($namespace, $name);

      $this->assertEquals($action, $fC->getActionByName($name));
   }

   public function testGenerateParamsFromInputConfig() {
      $method = new ReflectionMethod(Frontcontroller::class, 'generateParamsFromInputConfig');
      $method->setAccessible(true);

      // empty config generates empty array
      $this->assertEmpty($method->invoke(new Frontcontroller(), ''));
      $this->assertEmpty($method->invoke(new Frontcontroller(), ' '));

      // test single kay value couple
      $this->assertEquals(['foo' => 'bar'], $method->invoke(new Frontcontroller(), 'foo:bar'));

      // test multiple couples
      $this->assertEquals(['foo' => 'bar', 'bar' => 'baz'], $method->invoke(new Frontcontroller(), 'foo:bar|bar:baz'));

      // test missing key/value
      // test missing ":"
      // test missing "|"
      $this->assertEquals(['foo' => 'bar'], $method->invoke(new Frontcontroller(), 'foo:bar|barbaz'));
      $this->assertEquals(['foo' => 'bar'], $method->invoke(new Frontcontroller(), 'foo:bar|bar:'));
      $this->assertEquals(['foo' => 'bar'], $method->invoke(new Frontcontroller(), 'foo:bar|:baz'));
      $this->assertEquals(['foo' => 'bar'], $method->invoke(new Frontcontroller(), 'foo:bar|'));
      $this->assertEquals(['bar' => 'baz'], $method->invoke(new Frontcontroller(), 'foobar|bar:baz'));
      $this->assertEquals(['bar' => 'baz'], $method->invoke(new Frontcontroller(), 'foo:|bar:baz'));
      $this->assertEquals(['bar' => 'baz'], $method->invoke(new Frontcontroller(), ':bar|bar:baz'));
      $this->assertEquals(['bar' => 'baz'], $method->invoke(new Frontcontroller(), '|bar:baz'));
   }

}
