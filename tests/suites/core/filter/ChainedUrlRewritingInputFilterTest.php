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
namespace APF\tests\suites\core\filter;

use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\provider\ini\IniConfiguration;
use APF\core\configuration\provider\ini\IniConfigurationProvider;
use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\Frontcontroller;
use APF\tests\suites\core\frontcontroller\FakeIniProvider;

/**
 * Tests the action mapping capabilities of the ChainedUrlRewritingInputFilter.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 18.03.2014<br />
 */
class ChainedUrlRewritingInputFilterTest extends \PHPUnit_Framework_TestCase {

   const TEST_ACTION_CONFIG_NAME = 'actionconfig.ini';
   const REWRITTEN_QUERY_ATTRIBUTE = 'apf-rewritten-query';

   /**
    * @var IniConfigurationProvider
    */
   private $initialIniProvider;

   /**
    * Tests standard action as before realization of CR ID#63.
    */
   public function testSimpleSingleAction() {

      $actionNamespace = 'VENDOR\foo';
      $actionName = 'say-foo';

      // action on stack w/o params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/VENDOR_foo-action/' . $actionName;

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      // action on stack w/ params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/VENDOR_foo-action/' . $actionName . '/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

   }

   public function testSimpleMultipleActions() {

      // action on stack w/o params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/VENDOR_foo-action/say-foo/~/VENDOR_bar-action/say-bar';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals('VENDOR\foo', $actions[0]->getActionNamespace());
      assertEquals('say-foo', $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      assertEquals('VENDOR\bar', $actions[1]->getActionNamespace());
      assertEquals('say-bar', $actions[1]->getActionName());
      assertEquals([], $actions[1]->getInput()->getParameters());

      // action on stack w/ params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/VENDOR_foo-action/say-foo/one/1/two/2/~/VENDOR_bar-action/say-bar/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals('VENDOR\foo', $actions[0]->getActionNamespace());
      assertEquals('say-foo', $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

      assertEquals('VENDOR\bar', $actions[1]->getActionNamespace());
      assertEquals('say-bar', $actions[1]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[1]->getInput()->getParameters());

   }

   public function testSimpleMultipleActionsMixedWithParams() {

      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/zero/0/~/VENDOR_foo-action/say-foo/one/1/two/2/~/VENDOR_bar-action/say-bar/one/1/two/2/~/three/3/four/4';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals('VENDOR\foo', $actions[0]->getActionNamespace());
      assertEquals('say-foo', $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

      assertEquals('VENDOR\bar', $actions[1]->getActionNamespace());
      assertEquals('say-bar', $actions[1]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[1]->getInput()->getParameters());

   }

   public function testSingleMappedAction() {

      $urlToken = 'foo';
      $actionNamespace = 'VENDOR\foo';
      $actionName = 'say-foo';

      // action on stack w/o params - start with action
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/' . $urlToken;

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($urlToken, $actionNamespace, $actionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      // action on stack w/o params - start with action delimiter
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/~/' . $urlToken;

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($urlToken, $actionNamespace, $actionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      // action on stack w/ params - start with action
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/' . $urlToken . '/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($urlToken, $actionNamespace, $actionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

      // action on stack w/ params - start with action delimiter
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/~/' . $urlToken . '/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($urlToken, $actionNamespace, $actionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();
      assertEquals($actionNamespace, $actions[0]->getActionNamespace());
      assertEquals($actionName, $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

   }

   public function testMultipleMappedActions() {

      $fooUrlToken = 'foo';
      $fooActionNamespace = 'VENDOR\foo';
      $fooActionName = 'say-foo';

      $barUrlToken = 'bar';
      $barActionNamespace = 'VENDOR\bar';
      $barActionName = 'say-bar';

      // action on stack w/o params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/' . $fooUrlToken . '/~/' . $barUrlToken;

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($fooUrlToken, $fooActionNamespace, $fooActionName));
      $fC->registerActionUrlMapping(new ActionUrlMapping($barUrlToken, $barActionNamespace, $barActionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals($fooActionNamespace, $actions[0]->getActionNamespace());
      assertEquals($fooActionName, $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      assertEquals($barActionNamespace, $actions[1]->getActionNamespace());
      assertEquals($barActionName, $actions[1]->getActionName());
      assertEquals([], $actions[1]->getInput()->getParameters());

      // action on stack w/ params - start with action delimiter
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/~/' . $fooUrlToken . '/one/1/two/2/~/' . $barUrlToken . '/one/1/two/2/';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($fooUrlToken, $fooActionNamespace, $fooActionName));
      $fC->registerActionUrlMapping(new ActionUrlMapping($barUrlToken, $barActionNamespace, $barActionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals($fooActionNamespace, $actions[0]->getActionNamespace());
      assertEquals($fooActionName, $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

      assertEquals($barActionNamespace, $actions[1]->getActionNamespace());
      assertEquals($barActionName, $actions[1]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[1]->getInput()->getParameters());

   }

   public function testMixedActions() {

      $fooUrlToken = 'foo';
      $fooActionNamespace = 'VENDOR\foo';
      $fooActionName = 'say-foo';

      $barUrlToken = 'bar';
      $barActionNamespace = 'VENDOR\bar';
      $barActionName = 'say-bar';

      // action on stack w/o params - trailing slash special challenge ;)
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/' . $fooUrlToken . '/~/VENDOR_baz-action/say-baz/~/' . $barUrlToken . '/';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($fooUrlToken, $fooActionNamespace, $fooActionName));
      $fC->registerActionUrlMapping(new ActionUrlMapping($barUrlToken, $barActionNamespace, $barActionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals($fooActionNamespace, $actions[0]->getActionNamespace());
      assertEquals($fooActionName, $actions[0]->getActionName());
      assertEquals([], $actions[0]->getInput()->getParameters());

      assertEquals('VENDOR\baz', $actions[1]->getActionNamespace());
      assertEquals('say-baz', $actions[1]->getActionName());
      assertEquals([], $actions[1]->getInput()->getParameters());

      assertEquals($barActionNamespace, $actions[2]->getActionNamespace());
      assertEquals($barActionName, $actions[2]->getActionName());
      assertEquals([], $actions[2]->getInput()->getParameters());

      // action on stack w/ params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/~/' . $fooUrlToken . '/one/1/two/2/~/VENDOR_baz-action/say-baz/one/1/two/2/~/' . $barUrlToken . '/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $fC->registerActionUrlMapping(new ActionUrlMapping($fooUrlToken, $fooActionNamespace, $fooActionName));
      $fC->registerActionUrlMapping(new ActionUrlMapping($barUrlToken, $barActionNamespace, $barActionName));

      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      $actions = $fC->getActions();

      assertEquals($fooActionNamespace, $actions[0]->getActionNamespace());
      assertEquals($fooActionName, $actions[0]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[0]->getInput()->getParameters());

      assertEquals('VENDOR\baz', $actions[1]->getActionNamespace());
      assertEquals('say-baz', $actions[1]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[1]->getInput()->getParameters());

      assertEquals($barActionNamespace, $actions[2]->getActionNamespace());
      assertEquals($barActionName, $actions[2]->getActionName());
      assertEquals(['one' => '1', 'two' => '2'], $actions[2]->getInput()->getParameters());

   }

   public function testParametersOnly() {

      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/zero/0/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      assertEquals([], $fC->getActions());

      // special case for rewrite URLs: action delimiter but with only "normal" params
      $_REQUEST = [];
      $_REQUEST[self::REWRITTEN_QUERY_ATTRIBUTE] = '/~/zero/0/one/1/two/2';

      $filter = new TestableChainedUrlRewritingInputFilter();
      $fC = new Frontcontroller();
      $filter->setFrontcontroller($fC);
      $filter->filter(new TestableFilterChain(), null);

      assertEquals([], $fC->getActions());
   }

   protected function setUp() {

      // setup config provider to fake tests
      $this->initialIniProvider = ConfigurationManager::retrieveProvider('ini');

      // setup fake ini provider to avoid file-based configuration files
      $provider = new FakeIniProvider();

      $config = new IniConfiguration();

      // setup section for action
      $action = new IniConfiguration();
      $action->setValue('ActionClass', FilterTestAction::class);

      $config->setSection('say-foo', $action);
      $provider->registerConfiguration('VENDOR\foo', self::TEST_ACTION_CONFIG_NAME, $config);

      $config->setSection('say-bar', $action);
      $provider->registerConfiguration('VENDOR\bar', self::TEST_ACTION_CONFIG_NAME, $config);

      $config->setSection('say-baz', $action);
      $provider->registerConfiguration('VENDOR\baz', self::TEST_ACTION_CONFIG_NAME, $config);

      ConfigurationManager::registerProvider('ini', $provider);
   }

   protected function tearDown() {
      // restore previous setup to not influence further tests
      ConfigurationManager::registerProvider('ini', $this->initialIniProvider);
   }

}
