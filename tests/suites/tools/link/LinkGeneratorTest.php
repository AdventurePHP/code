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
import('tools::link', 'LinkGenerator');
import('core::frontcontroller', 'Frontcontroller');

/**
 * @package tests::suites::tools::link
 * @class LinkGeneratorTest
 *
 * Implements tests for the link generator and the url abstraction.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.12.2011
 */
class LinkGeneratorTest extends PHPUnit_Framework_TestCase {

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

   /* TODO create mock for front controller action stack + configuration for testing ... */
   public function __testFrontcontrollerUrlGeneration() {
      $fC = &Singleton::getInstance('Frontcontroller');
      /* @var $fC Frontcontroller */
      $fC->setContext('test');
      $fC->registerAction('cms::core::biz::setmodel', 'setModel', array('page.config.section' => 'external'));

      $url = Url::fromString($_SERVER['REQUEST_URI']);
      $url->setHost('localhost');
      $url->setScheme('http');
      echo LinkGenerator::generateUrl($url->mergeQuery(array('foo' => 'bar', 'baz' => '4', 'blubber' => null)));
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
      $actionNamespace = 'cms_core_biz_setmodel';
      $url = new Url(null, null, null, '/de/my-page/topic/2-user-research/~/' . $actionNamespace . '-action/setModel/page.config.section/external');
      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
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
      $actionNamespace = 'tools_media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      $urlOne = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), new RewriteLinkScheme());
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      assertEquals('/~/tools_media-action/streamMedia/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, array($foo => '1', $bar => '2'), new DefaultLinkScheme());
      assertEquals($path . '?tools_media-action:streamMedia=' . $foo . ':1|' . $bar . ':2', $link);
   }

}

?>