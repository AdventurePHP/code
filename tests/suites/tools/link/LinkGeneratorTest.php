<?php
namespace APF\tests\suites\tools\link;

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
use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\frontcontroller\ActionUrlMapping;
use APF\core\frontcontroller\FrontcontrollerInput;
use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;
use APF\tools\link\RewriteLinkScheme;
use APF\tools\link\Url;
use string;

/**
 * @package APF\tests\suites\tools\link
 * @class TestFrontControllerAction
 *
 * Implements a dummy front controller action to enable testing the link scheme.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2011<br />
 */
class TestFrontControllerAction extends AbstractFrontcontrollerAction {

   public function __construct() {
      $this->input = new FrontcontrollerInput();
   }

   public function run() {
   }

}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableDefaultLinkScheme
 *
 * Implements a testable link scheme regarding front controller link
 * generation capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2011<br />
 */
class TestableDefaultLinkScheme extends DefaultLinkScheme {

   protected function &getFrontcontrollerActions() {

      $actions = array();
      $action = new TestFrontControllerAction();
      $action->setActionNamespace('APF\cms\core\biz\setmodel');
      $action->setActionName('setModel');
      $action->setKeepInUrl(true); // to test action inclusion

      $input = new FrontcontrollerInput();
      $input->setAttribute('page.config.section', 'external');
      $action->setInput($input);

      $actions[] = $action;

      return $actions;
   }
}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableRewriteLinkScheme
 *
 * Implements a testable link scheme regarding front controller link
 * generation capabilities.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2011<br />
 */
class TestableRewriteLinkScheme extends RewriteLinkScheme {

   protected function &getFrontcontrollerActions() {

      $actions = array();
      $action = new TestFrontControllerAction();
      $action->setActionNamespace('APF\cms\core\biz\setmodel');
      $action->setActionName('setModel');
      $action->setKeepInUrl(true); // to test action inclusion

      $input = new FrontcontrollerInput();
      $input->setAttribute('page.config.section', 'external');
      $action->setInput($input);

      $actions[] = $action;

      return $actions;
   }
}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableDoubleActionRewriteLinkScheme
 *
 * Implements a testable link scheme regarding front controller link
 * generation capabilities that avoids double-notation of actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2013<br />
 */
class TestableDoubleActionStandardLinkScheme extends DefaultLinkScheme {

   private $actionNamespace;
   private $actionName;

   public function __construct($actionNamespace, $actionName) {
      $this->actionNamespace = $actionNamespace;
      $this->actionName = $actionName;
   }

   protected function &getFrontcontrollerActions() {

      $actions = array();
      $action = new TestFrontControllerAction();
      $action->setActionNamespace($this->actionNamespace);
      $action->setActionName($this->actionName);
      $action->setKeepInUrl(true); // to test action inclusion

      $action->setInput(new FrontcontrollerInput());

      $actions[] = $action;

      return $actions;
   }

}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableDoubleActionRewriteLinkScheme
 *
 * Implements a testable link scheme regarding front controller link
 * generation capabilities that avoids double-notation of actions.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.08.2013<br />
 */
class TestableDoubleActionRewriteLinkScheme extends RewriteLinkScheme {

   private $actionNamespace;
   private $actionName;

   public function __construct($actionNamespace, $actionName) {
      $this->actionNamespace = $actionNamespace;
      $this->actionName = $actionName;
   }

   protected function &getFrontcontrollerActions() {

      $actions = array();
      $action = new TestFrontControllerAction();
      $action->setActionNamespace($this->actionNamespace);
      $action->setActionName($this->actionName);
      $action->setKeepInUrl(true); // to test action inclusion

      $action->setInput(new FrontcontrollerInput());

      $actions[] = $action;

      return $actions;
   }
}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableActionUrlMappingLinkScheme
 *
 * Encapsulates link scheme overrides for action mappings used for testing purposes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.03.2014<br />
 */
trait TestableActionUrlMappingLinkScheme {

   /**
    * @var ActionUrlMapping[]
    */
   private $actionMappings = array();

   public function addActionMapping(ActionUrlMapping $mapping) {
      $this->actionMappings[] = $mapping;
   }

   protected function getActionUrlMapping($namespace, $name) {
      foreach ($this->actionMappings as $mapping) {
         if ($mapping->getNamespace() == $namespace && $mapping->getName() == $name) {
            return $mapping;
         }
      }

      return null;
   }

   protected function getActionUrMappingTokens() {
      $keys = array();
      foreach ($this->actionMappings as $mapping) {
         $keys[] = $mapping->getUrlToken();
      }

      return $keys;
   }

}

class TestableActionUrlMappingStandardLinkScheme extends DefaultLinkScheme {

   const URL_TOKEN = 'media';

   use TestableActionUrlMappingLinkScheme;

}

class TestableActionUrlMappingRewriteLinkScheme extends RewriteLinkScheme {

   const URL_TOKEN = 'media';

   use TestableActionUrlMappingLinkScheme;

}

/**
 * @package APF\tests\suites\tools\link
 * @class TestableActionUrlMappingLinkScheme
 *
 * Encapsulates link scheme overrides for front controller actions used for testing purposes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.03.2014<br />
 */
trait MultipleMappedActionsTestableLinkScheme {

   /**
    * @var AbstractFrontcontrollerAction[]
    */
   private $actions = array();

   public function addAction(AbstractFrontcontrollerAction $action) {
      $this->actions[] = $action;
   }

   protected function &getFrontcontrollerActions() {
      return $this->actions;
   }

}

class MultipleMappedActionsTestableStandardLinkScheme extends DefaultLinkScheme {

   use TestableActionUrlMappingLinkScheme, MultipleMappedActionsTestableLinkScheme;

}

class MultipleMappedActionsTestableRewriteLinkScheme extends RewriteLinkScheme {

   use TestableActionUrlMappingLinkScheme, MultipleMappedActionsTestableLinkScheme;

}

/**
 * @package APF\tests\suites\tools\link
 * @class LinkGeneratorTest
 *
 * Implements tests for the link generator and the url abstraction.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.12.2011
 */
class LinkGeneratorTest extends \PHPUnit_Framework_TestCase {

   private static $DEFAULT_URL = '/page/001-main';

   public function testSimpleLinkScheme() {
      $url = new Url(null, null, null, self::$DEFAULT_URL);
      assertEquals(
            self::$DEFAULT_URL,
            LinkGenerator::generateUrl($url, new DefaultLinkScheme())
      );
   }

   public function testUrlCreationFromString() {
      $scheme = 'https';
      $domain = 'www.domain.tld';
      $path = '/path/to/dir';
      $paramName = 'foo';
      $paramValue = 'bar';
      $url = Url::fromString('' . $scheme . '://' . $domain . '' . $path . '?' . $paramName . '=' . $paramValue . '');
      assertEquals($scheme, $url->getScheme());
      assertEquals(null, $url->getPort());
      assertEquals($domain, $url->getHost());
      assertEquals($path, $url->getPath());
      assertEquals($paramValue, $url->getQueryParameter($paramName));
   }

   public function testFrontcontrollerUrlGeneration() {
      $url = Url::fromString('')->setHost('localhost')->setScheme('http');
      $link = LinkGenerator::generateUrl(
            $url->mergeQuery(array('foo' => 'bar', 'blubber' => null)),
            new TestableDefaultLinkScheme()
      );
      assertEquals('http://localhost?foo=bar&amp;APF_cms_core_biz_setmodel-action:setModel=page.config.section:external', $link);

      $link = LinkGenerator::generateUrl(
            $url->mergeQuery(array('foo' => 'bar', 'blubber' => null)),
            new TestableRewriteLinkScheme()
      );
      assertEquals('http://localhost/foo/bar/~/APF_cms_core_biz_setmodel-action/setModel/page.config.section/external', $link);
   }

   public function testConstructorPlusFluentConfiguration() {
      $host = 'localhost';
      $scheme = 'https';
      $url = Url::fromCurrent(true)->setHost($host)->setScheme($scheme);
      assertEquals($host, $url->getHost());
      assertEquals($scheme, $url->getScheme());
      assertEmpty($url->getQuery());
   }

   public function testConstructorPlusParameterMerge() {
      $host = 'localhost';
      $scheme = 'http';
      $paramOneName = 'blubber';
      $paramTwoName = 'zicke';
      $paramOneValue = 'bla';
      $paramTwoValue = 'zacke';
      $url = Url::fromCurrent(true)->mergeQuery(array($paramOneName => $paramOneValue, $paramTwoName => $paramTwoValue));
      assertEquals($host, $url->getHost());
      assertEquals($scheme, $url->getScheme());
      assertEquals($paramOneValue, $url->getQueryParameter($paramOneName));
      assertEquals($paramTwoValue, $url->getQueryParameter($paramTwoName));
   }

   public function testConstructorPlusQueryParameterSetting() {
      $paramOneName = 'gbview';
      $paramOneValue = 'display';
      $paramTwoName = 'entryid';
      $url = Url::fromCurrent(true)
            ->setQueryParameter($paramOneName, $paramOneValue)
            ->setQueryParameter($paramTwoName, null);
      assertEquals($paramOneValue, $url->getQueryParameter($paramOneName));
      assertEquals(null, $url->getQueryParameter($paramTwoName));
   }

   public function testLinkGenerationSimpleWithRewriteLinkScheme() {
      $url = new Url(null, null, null, '/foo/1/bar/2/blubber/3', array('foo' => '2'));
      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      assertContains('/foo/2/', $link);
   }

   public function testLinkGenerationFrontcontrollerActionWithActionParsing() {
      $actionNamespace = 'APF_cms_core_biz_setmodel';

      $url = Url::fromString('/de/my-page/topic/2-user-research/~/' . $actionNamespace . '-action/setModel/page.config.section/external');
      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      assertNotContains($actionNamespace, $link);

      $url = Url::fromString('/?de=my-page&topic=2-user-research&' . $actionNamespace . '-action:setModel=page.config.section:external');
      $link = LinkGenerator::generateUrl($url, new DefaultLinkScheme());
      assertNotContains($actionNamespace, $link);
   }

   public function testLinkGenerationFromParametersOnly() {
      $paramOneName = 'de';
      $paramOneValue = 'my-page';
      $paramTwoName = 'topic';
      $paramTwoValue = '2-research';
      $url = new Url(null, null, null, null, array($paramOneName => $paramOneValue, $paramTwoName => $paramTwoValue));

      $link = LinkGenerator::generateUrl($url);
      assertEquals('?' . $paramOneName . '=' . $paramOneValue . '&amp;' . $paramTwoName . '=' . $paramTwoValue, $link);

      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      assertEquals('/' . $paramOneName . '/' . $paramOneValue . '/' . $paramTwoName . '/' . $paramTwoValue, $link);
   }

   public function testLinkGenerationForActionLinks() {
      $path = '/my-app';
      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      $urlOne = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), new RewriteLinkScheme());
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/APF_tools_media-action/streamMedia/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), new DefaultLinkScheme());
      assertEquals($path . '?APF_tools_media-action:streamMedia=' . $foo . ':1|' . $bar . ':2', $link);
   }

   /**
    * Same action within url (keepInUrl=true) and applied via generateActionUrl() as request parameter
    * should result in action appears only once within URL - the action that is applied manually wins.
    */
   public function testLinkGenerationForActionLinksWithDuplicateActions() {
      $path = '/my-app';
      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      // configure link scheme to contain appropriate action
      $defaultScheme = new TestableDoubleActionStandardLinkScheme($actionNamespace, $actionName);
      $rewriteScheme = new TestableDoubleActionRewriteLinkScheme($actionNamespace, $actionName);

      $urlOne = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $rewriteScheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/APF_tools_media-action/streamMedia/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $defaultScheme);
      assertEquals($path . '?APF_tools_media-action:streamMedia=' . $foo . ':1|' . $bar . ':2', $link);

      // without explicit action definition, the registered front controller action makes it into the url
      $urlThree = new Url(null, null, null, $path);
      $link = LinkGenerator::generateUrl($urlThree, $rewriteScheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/APF_tools_media-action/streamMedia', $link);

      $urlFour = new Url(null, null, null, $path);
      $link = LinkGenerator::generateUrl($urlFour, $defaultScheme);
      assertEquals($path . '?APF_tools_media-action:streamMedia', $link);
   }

   public function testExclusionOfNullValueAndInclusionOfZeroValueParameters() {
      $url = Url::fromString('/');
      $url->mergeQuery(array(
            'foo'         => 'bar',
            'exclude'     => null,
         // see bug with ignoring zero values in 1.15 (fixed in 1.16)
            'include-one' => '0',
            'include-two' => 0
      ));

      $link = LinkGenerator::generateUrl($url, new DefaultLinkScheme(false));
      assertEquals('/?foo=bar&include-one=0&include-two=0', $link);
   }

   public function testAnchorWithNormalUrl() {

      $anchor = 'test-anchor';
      $path = '/folder1';
      $foo = 'foo';
      $bar = 'bar';
      $baz = 'baz';
      $url = new Url(null, null, null, $path, array($foo => $bar, $bar => $baz), $anchor);

      $linkScheme = new DefaultLinkScheme(false);
      $link = LinkGenerator::generateUrl($url, $linkScheme);

      // expect anchor at the very end
      assertTrue(preg_match('/#' . $anchor . '$/', $link) === 1);

      // "normal" of URL generation should be left as-is (exclusion/regression test)
      assertEquals(
            $path . '?' . $foo . '=' . $bar . '&' . $bar . '=' . $baz,
            LinkGenerator::generateUrl($url->setAnchor(null), $linkScheme)
      );
   }

   public function testAnchorWithRewriteUrl() {

      $anchor = 'test-anchor';
      $foo = 'foo';
      $bar = 'bar';
      $baz = 'baz';
      $url = new Url(null, null, null, null, array($foo => $bar, $bar => $baz), $anchor);

      $linkScheme = new RewriteLinkScheme(false);
      $link = LinkGenerator::generateUrl($url, $linkScheme);

      // expect anchor at the very end
      assertTrue(preg_match('/#' . $anchor . '$/', $link) === 1);

      // "normal" of URL generation should be left as-is (exclusion/regression test)
      assertEquals(
            '/' . $foo . '/' . $bar . '/' . $bar . '/' . $baz,
            LinkGenerator::generateUrl($url->setAnchor(null), $linkScheme)
      );
   }

   public function testActionMapping() {

      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';

      $urlOne = new Url(null, null, null, null);
      $scheme = new TestableActionUrlMappingRewriteLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array(), $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN, $link);

      $urlTwo = new Url(null, null, null, null);
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, array(), $scheme);
      assertEquals('?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $link);
   }

   public function testActionMappingWithPath() {

      $path = '/my-app';
      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      $urlOne = new Url(null, null, null, $path);
      $scheme = new TestableActionUrlMappingRewriteLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN . '/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $scheme);
      assertEquals($path . '?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN . '=' . $foo . ':1|' . $bar . ':2', $link);
   }

   /**
    * Test mapped action mixed with existing static action in URL.
    */
   public function testActionInUrlAndOneMappedAction() {

      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      // "normal" action within URL but no keepInUrl=true set for action; generate action url
      $urlOne = new Url(null, null, null, '/VENDOR_actions-action:doIt');
      $scheme = new TestableActionUrlMappingRewriteLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN . '/' . $foo . '/1/' . $bar . '/2', $link);

      // mapped action is within url but no keepInUrl=true set for action; generate normal url
      $urlTwo = new Url(null, null, null, '/foo/bar/~/media');
      $link = LinkGenerator::generateUrl($urlTwo, $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/foo/bar', $link);

      // normal action within URL but no keepInUrl=true set for action; generate action url
      $urlThree = new Url(null, null, null, null, array('VENDOR_actions-action:doIt' => ''));
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlThree, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), $scheme);
      assertEquals('?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN . '=' . $foo . ':1|' . $bar . ':2', $link);

      // mapped action is within url but no keepInUrl=true set for action; generate normal url
      $urlFour = new Url(null, null, null, '/foo/bar', array('media' => ''));
      $link = LinkGenerator::generateUrl($urlFour, $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/foo/bar', $link);

   }

   /**
    * Tests link generation with multiple mapped actions.
    */
   public function testMultipleMappedActions() {

      $standardScheme = new MultipleMappedActionsTestableStandardLinkScheme();
      $rewriteScheme = new MultipleMappedActionsTestableRewriteLinkScheme();

      // provokes test for naming collision!
      $fooMapping = new ActionUrlMapping('foo', 'VENDOR\foo', 'say-foo');
      $standardScheme->addActionMapping($fooMapping);
      $rewriteScheme->addActionMapping($fooMapping);

      $fooAction = new TestFrontControllerAction();
      $fooAction->setActionName('say-foo');
      $fooAction->setActionNamespace('VENDOR\foo');
      $fooAction->setKeepInUrl(true);

      $standardScheme->addAction($fooAction);
      $rewriteScheme->addAction($fooAction);

      // provokes test for naming collision!
      $barMapping = new ActionUrlMapping('bar', 'VENDOR\bar', 'say-bar');
      $standardScheme->addActionMapping($barMapping);
      $rewriteScheme->addActionMapping($barMapping);

      $barAction = new TestFrontControllerAction();
      $barAction->setActionName('say-bar');
      $barAction->setActionNamespace('VENDOR\bar');
      $barAction->setKeepInUrl(true);

      $standardScheme->addAction($barAction);
      $rewriteScheme->addAction($barAction);

      // simple normal URL containing mapped actions - w/o params
      $url = new Url(null, null, null, '/categories');
      $link = LinkGenerator::generateUrl($url, $standardScheme);
      assertEquals('/categories?foo&amp;bar', $link);

      // simple rewrite URL containing mapped actions - w/o params
      $url = new Url(null, null, null, null);
      $link = LinkGenerator::generateUrl($url, $rewriteScheme);
      assertEquals('/~/foo/~/bar', $link);

      // normal action URL containing mapped actions - w/o params
      $url = new Url(null, null, null, '/categories');
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', array(), $standardScheme);
      assertEquals('/categories?VENDOR_baz-action:say-baz&amp;foo&amp;bar', $link);

      // rewrite action URL containing mapped actions - w/o params
      $url = new Url(null, null, null, null);
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', array(), $rewriteScheme);
      assertEquals('/~/VENDOR_baz-action/say-baz/~/foo/~/bar', $link);

      // simple normal URL containing mapped actions - w/ params
      $url = new Url(null, null, null, '/categories', array('one' => '1', 'two' => '2'));
      $link = LinkGenerator::generateUrl($url, $standardScheme);
      assertEquals('/categories?one=1&amp;two=2&amp;foo&amp;bar', $link);

      // simple rewrite URL containing mapped actions - w/ params
      $url = new Url(null, null, null, null, array('one' => '1', 'two' => '2'));
      $link = LinkGenerator::generateUrl($url, $rewriteScheme);
      assertEquals('/one/1/two/2/~/foo/~/bar', $link);

      // normal action URL containing mapped actions - w/ params
      $url = new Url(null, null, null, '/categories', array('one' => '1', 'two' => '2'));
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', array(), $standardScheme);
      assertEquals('/categories?one=1&amp;two=2&amp;VENDOR_baz-action:say-baz&amp;foo&amp;bar', $link);

      // rewrite action URL containing mapped actions - w/ params
      $url = new Url(null, null, null, null, array('one' => '1', 'two' => '2'));
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', array(), $rewriteScheme);
      assertEquals('/one/1/two/2/~/VENDOR_baz-action/say-baz/~/foo/~/bar', $link);

   }

}
