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
namespace APF\tests\suites\tools\link;

use APF\core\frontcontroller\ActionUrlMapping;
use APF\tools\link\DefaultLinkScheme;
use APF\tools\link\LinkGenerator;
use APF\tools\link\RewriteLinkScheme;
use APF\tools\link\Url;
use APF\tools\link\UrlFormatException;
use PHPUnit\Framework\TestCase;

/**
 * Implements tests for the link generator and the url abstraction.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.12.2011
 */
class LinkGeneratorTest extends TestCase {

   private static $DEFAULT_URL = '/page/001-main';

   public function testSimpleLinkScheme() {
      $url = new Url(null, null, null, self::$DEFAULT_URL);
      $this->assertEquals(
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
      $this->assertEquals($scheme, $url->getScheme());
      $this->assertEquals(null, $url->getPort());
      $this->assertEquals($domain, $url->getHost());
      $this->assertEquals($path, $url->getPath());
      $this->assertEquals($paramValue, $url->getQueryParameter($paramName));
   }

   public function testFrontControllerUrlGeneration() {
      $url = Url::fromString('')->setHost('localhost')->setScheme('http');
      $link = LinkGenerator::generateUrl(
            $url->mergeQuery(['foo' => 'bar', 'blubber' => null]),
            new TestableDefaultLinkScheme()
      );
      $this->assertEquals('http://localhost?foo=bar&amp;APF_cms_core_biz_setmodel-action:setModel=page.config.section:external', $link);

      $link = LinkGenerator::generateUrl(
            $url->mergeQuery(['foo' => 'bar', 'blubber' => null]),
            new TestableRewriteLinkScheme()
      );
      $this->assertEquals('http://localhost/foo/bar/~/APF_cms_core_biz_setmodel-action/setModel/page.config.section/external', $link);
   }

   /**
    * @throws UrlFormatException
    */
   public function testConstructorPlusFluentConfiguration() {
      $host = 'localhost';
      $scheme = 'https';
      $url = Url::fromCurrent(true)->setHost($host)->setScheme($scheme);
      $this->assertEquals($host, $url->getHost());
      $this->assertEquals($scheme, $url->getScheme());
      $this->assertEmpty($url->getQuery());
   }

   public function testConstructorPlusParameterMerge() {
      $host = 'localhost';
      $scheme = 'http';
      $paramOneName = 'blubber';
      $paramTwoName = 'zicke';
      $paramOneValue = 'bla';
      $paramTwoValue = 'zacke';
      $url = Url::fromCurrent(true)->mergeQuery([$paramOneName => $paramOneValue, $paramTwoName => $paramTwoValue]);
      $this->assertEquals($host, $url->getHost());
      $this->assertEquals($scheme, $url->getScheme());
      $this->assertEquals($paramOneValue, $url->getQueryParameter($paramOneName));
      $this->assertEquals($paramTwoValue, $url->getQueryParameter($paramTwoName));
   }

   public function testConstructorPlusQueryParameterSetting() {
      $paramOneName = 'gbview';
      $paramOneValue = 'display';
      $paramTwoName = 'entryid';
      $url = Url::fromCurrent(true)
            ->setQueryParameter($paramOneName, $paramOneValue)
            ->setQueryParameter($paramTwoName, null);
      $this->assertEquals($paramOneValue, $url->getQueryParameter($paramOneName));
      $this->assertEquals(null, $url->getQueryParameter($paramTwoName));
   }

   public function testLinkGenerationSimpleWithRewriteLinkScheme() {
      $url = new Url(null, null, null, '/foo/1/bar/2/blubber/3', ['foo' => '2']);
      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      $this->assertContains('/foo/2/', $link);
   }

   public function testLinkGenerationFrontControllerActionWithActionParsing() {
      $actionNamespace = 'APF_cms_core_biz_setmodel';

      $url = Url::fromString('/de/my-page/topic/2-user-research/~/' . $actionNamespace . '-action/setModel/page.config.section/external');
      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      $this->assertNotContains($actionNamespace, $link);

      $url = Url::fromString('/?de=my-page&topic=2-user-research&' . $actionNamespace . '-action:setModel=page.config.section:external');
      $link = LinkGenerator::generateUrl($url, new DefaultLinkScheme());
      $this->assertNotContains($actionNamespace, $link);
   }

   public function testLinkGenerationFromParametersOnly() {
      $paramOneName = 'de';
      $paramOneValue = 'my-page';
      $paramTwoName = 'topic';
      $paramTwoValue = '2-research';
      $url = new Url(null, null, null, null, [$paramOneName => $paramOneValue, $paramTwoName => $paramTwoValue]);

      $link = LinkGenerator::generateUrl($url);
      $this->assertEquals('?' . $paramOneName . '=' . $paramOneValue . '&amp;' . $paramTwoName . '=' . $paramTwoValue, $link);

      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      $this->assertEquals('/' . $paramOneName . '/' . $paramOneValue . '/' . $paramTwoName . '/' . $paramTwoValue, $link);
   }

   public function testLinkGenerationForActionLinks() {
      $path = '/my-app';
      $actionNamespace = 'APF\tools\media';
      $actionName = 'streamMedia';
      $foo = 'foo';
      $bar = 'bar';

      $urlOne = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], new RewriteLinkScheme());
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/APF_tools_media-action/streamMedia/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], new DefaultLinkScheme());
      $this->assertEquals($path . '?APF_tools_media-action:streamMedia=' . $foo . ':1|' . $bar . ':2', $link);
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
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $rewriteScheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/APF_tools_media-action/streamMedia/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $defaultScheme);
      $this->assertEquals($path . '?APF_tools_media-action:streamMedia=' . $foo . ':1|' . $bar . ':2', $link);

      // without explicit action definition, the registered front controller action makes it into the url
      $urlThree = new Url(null, null, null, $path);
      $link = LinkGenerator::generateUrl($urlThree, $rewriteScheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/APF_tools_media-action/streamMedia', $link);

      $urlFour = new Url(null, null, null, $path);
      $link = LinkGenerator::generateUrl($urlFour, $defaultScheme);
      $this->assertEquals($path . '?APF_tools_media-action:streamMedia', $link);
   }

   public function testExclusionOfNullValueAndInclusionOfZeroValueParameters() {
      $url = Url::fromString('/');
      $url->mergeQuery([
            'foo' => 'bar',
            'exclude' => null,
         // see bug with ignoring zero values in 1.15 (fixed in 1.16)
            'include-one' => '0',
            'include-two' => 0
      ]);

      $link = LinkGenerator::generateUrl($url, new DefaultLinkScheme(false));
      $this->assertEquals('/?foo=bar&include-one=0&include-two=0', $link);
   }

   public function testAnchorWithNormalUrl() {

      $anchor = 'test-anchor';
      $path = '/folder1';
      $foo = 'foo';
      $bar = 'bar';
      $baz = 'baz';
      $url = new Url(null, null, null, $path, [$foo => $bar, $bar => $baz], $anchor);

      $linkScheme = new DefaultLinkScheme(false);
      $link = LinkGenerator::generateUrl($url, $linkScheme);

      // expect anchor at the very end
      $this->assertTrue(preg_match('/#' . $anchor . '$/', $link) === 1);

      // "normal" of URL generation should be left as-is (exclusion/regression test)
      $this->assertEquals(
            $path . '?' . $foo . '=' . $bar . '&' . $bar . '=' . $baz,
            LinkGenerator::generateUrl($url->setAnchor(null), $linkScheme)
      );
   }

   public function testAnchorWithRewriteUrl() {

      $anchor = 'test-anchor';
      $foo = 'foo';
      $bar = 'bar';
      $baz = 'baz';
      $url = new Url(null, null, null, null, [$foo => $bar, $bar => $baz], $anchor);

      $linkScheme = new RewriteLinkScheme(false);
      $link = LinkGenerator::generateUrl($url, $linkScheme);

      // expect anchor at the very end
      $this->assertTrue(preg_match('/#' . $anchor . '$/', $link) === 1);

      // "normal" of URL generation should be left as-is (exclusion/regression test)
      $this->assertEquals(
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
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, [], $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN, $link);

      $urlTwo = new Url(null, null, null, null);
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, [], $scheme);
      $this->assertEquals('?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $link);
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
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN . '/' . $foo . '/1/' . $bar . '/2', $link);

      $urlTwo = new Url(null, null, null, $path);
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlTwo, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $scheme);
      $this->assertEquals($path . '?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN . '=' . $foo . ':1|' . $bar . ':2', $link);
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
      $link = LinkGenerator::generateActionUrl($urlOne, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/~/' . TestableActionUrlMappingRewriteLinkScheme::URL_TOKEN . '/' . $foo . '/1/' . $bar . '/2', $link);

      // mapped action is within url but no keepInUrl=true set for action; generate normal url
      $urlTwo = new Url(null, null, null, '/foo/bar/~/media');
      $link = LinkGenerator::generateUrl($urlTwo, $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/foo/bar', $link);

      // normal action within URL but no keepInUrl=true set for action; generate action url
      $urlThree = new Url(null, null, null, null, ['VENDOR_actions-action:doIt' => '']);
      $scheme = new TestableActionUrlMappingStandardLinkScheme();
      $scheme->addActionMapping(new ActionUrlMapping(TestableActionUrlMappingStandardLinkScheme::URL_TOKEN, $actionNamespace, $actionName));
      $link = LinkGenerator::generateActionUrl($urlThree, $actionNamespace, $actionName, [$foo => '1', $bar => '2'], $scheme);
      $this->assertEquals('?' . TestableActionUrlMappingStandardLinkScheme::URL_TOKEN . '=' . $foo . ':1|' . $bar . ':2', $link);

      // mapped action is within url but no keepInUrl=true set for action; generate normal url
      $urlFour = new Url(null, null, null, '/foo/bar', ['media' => '']);
      $link = LinkGenerator::generateUrl($urlFour, $scheme);
      // the rewrite link scheme supports no path since the path is interpreted as params and their values!
      $this->assertEquals('/foo/bar', $link);

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
      $this->assertEquals('/categories?foo&amp;bar', $link);

      // simple rewrite URL containing mapped actions - w/o params
      $url = new Url(null, null, null, null);
      $link = LinkGenerator::generateUrl($url, $rewriteScheme);
      $this->assertEquals('/~/foo/~/bar', $link);

      // normal action URL containing mapped actions - w/o params
      $url = new Url(null, null, null, '/categories');
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', [], $standardScheme);
      $this->assertEquals('/categories?VENDOR_baz-action:say-baz&amp;foo&amp;bar', $link);

      // rewrite action URL containing mapped actions - w/o params
      $url = new Url(null, null, null, null);
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', [], $rewriteScheme);
      $this->assertEquals('/~/VENDOR_baz-action/say-baz/~/foo/~/bar', $link);

      // simple normal URL containing mapped actions - w/ params
      $url = new Url(null, null, null, '/categories', ['one' => '1', 'two' => '2']);
      $link = LinkGenerator::generateUrl($url, $standardScheme);
      $this->assertEquals('/categories?one=1&amp;two=2&amp;foo&amp;bar', $link);

      // simple rewrite URL containing mapped actions - w/ params
      $url = new Url(null, null, null, null, ['one' => '1', 'two' => '2']);
      $link = LinkGenerator::generateUrl($url, $rewriteScheme);
      $this->assertEquals('/one/1/two/2/~/foo/~/bar', $link);

      // normal action URL containing mapped actions - w/ params
      $url = new Url(null, null, null, '/categories', ['one' => '1', 'two' => '2']);
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', [], $standardScheme);
      $this->assertEquals('/categories?one=1&amp;two=2&amp;VENDOR_baz-action:say-baz&amp;foo&amp;bar', $link);

      // rewrite action URL containing mapped actions - w/ params
      $url = new Url(null, null, null, null, ['one' => '1', 'two' => '2']);
      $link = LinkGenerator::generateActionUrl($url, 'VENDOR\baz', 'say-baz', [], $rewriteScheme);
      $this->assertEquals('/one/1/two/2/~/VENDOR_baz-action/say-baz/~/foo/~/bar', $link);

   }

   /**
    * @throws UrlFormatException
    */
   public function testXssEscape() {

      /** @noinspection Annotator */
      $injection = '<script>alert(\'foo\')</script>';
      $url = Url::fromString('/foo/bar/?\'>' . $injection . '=test');

      $link = LinkGenerator::generateUrl($url, new DefaultLinkScheme());
      $this->assertNotContains($injection, $link, true);

      $url = Url::fromString('/foo/bar/\'>' . $injection . '/test');

      $link = LinkGenerator::generateUrl($url, new RewriteLinkScheme());
      $this->assertNotContains($injection, $link, true);

   }

   public function testSimpleParameterOverwriting() {
      $url = Url::fromString('/?foo=1&amp;foo=2');
      $this->assertEquals('2', $url->getQueryParameter('foo'));
   }

   public function testArrayParameterOverwriting() {
      $url = Url::fromString('/?a[x]=1&a[y]=2&b[]=1&b[]=2&b[2]=3&b[1]=7');

      $a = $url->getQueryParameter('a');
      $this->assertTrue(is_array($a));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertEquals('1', $a['x']);
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertEquals('2', $a['y']);

      $b = $url->getQueryParameter('b');
      $this->assertTrue(is_array($b));
      $this->assertEquals('1', $b[0]);
      $this->assertEquals('7', $b[1]);
      $this->assertEquals('3', $b[2]);
   }

   public function testNestedArrayParameters() {

      // nested arrays with mixed keys (most complex use case)
      $url = Url::fromString('/?a[][foo]=123&a[][foo]=123&a[1][bar]=456&b[c][d][e][f]=123');

      $a = $url->getQueryParameter('a');

      $this->assertTrue(is_array($a));
      $this->assertTrue(is_array($a[0]));
      $this->assertTrue(is_array($a[1]));

      $this->assertEquals('123', $a[0]['foo']);
      $this->assertEquals('123', $a[1]['foo']);
      $this->assertEquals('456', $a[1]['bar']);

      $b = $url->getQueryParameter('b');

      $this->assertTrue(is_array($b));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertTrue(is_array($b['c']));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertTrue(is_array($b['c']['d']));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertTrue(is_array($b['c']['d']['e']));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertEquals('123', $b['c']['d']['e']['f']);
   }

   public function testMixedParameterResolvingEdgeCase() {

      // mixed parameter types --> later data type declaration overwrites previous one
      $url = Url::fromString('/?a=123&a[b]=456');
      $a = $url->getQueryParameter('a');

      $this->assertTrue(is_array($a));
      /** @noinspection PhpIllegalStringOffsetInspection */
      $this->assertEquals('456', $a['b']);

   }

   public function testMixedArrayParameterUrlGeneration() {

      $scheme = new DefaultLinkScheme();
      $url = Url::fromString('/');

      $url->setQuery([
            'a' => [
                  'x' => '1',
                  'y' => '2'
            ],
            'b' => [
                  0 => '1',
                  1 => '2'
            ]
      ]);

      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a[x]=1&amp;a[y]=2&amp;b[0]=1&amp;b[1]=2', $link);

      $url->setQuery([
            'a' => [
                  ['foo' => '123'],
                  [
                        'foo' => '123',
                        'bar' => '456'
                  ],
            ],
            'b' => [
                  'c' => [
                        'd' => [
                              'e' => [
                                    'f' => '123'
                              ]
                        ]
                  ]
            ]
      ]);

      $this->assertEquals(
            '/?a[0][foo]=123&amp;a[1][foo]=123&amp;a[1][bar]=456&amp;b[c][d][e][f]=123',
            LinkGenerator::generateUrl($url, $scheme)
      );

   }

   /**
    * Tests whether blanks are encoded properly within parameters and names.
    * @throws UrlFormatException
    */
   public function testBlanksInParametersAndValues() {

      $url = Url::fromString('/');
      $url->setQuery(['param name' => 'param value']);

      $this->assertEquals(
            '/?param%20name=param%20value',
            LinkGenerator::generateUrl($url, new DefaultLinkScheme())
      );

      $this->assertEquals(
            '/param%20name/param%20value',
            LinkGenerator::generateUrl($url, new RewriteLinkScheme())
      );

   }

   public function testEncodeRfc3986() {

      $scheme = new DefaultLinkScheme(true, true, true);
      $url = Url::fromString('/');

      $url->setQuery([
            'a' => [
                  'x' => '1',
                  'y' => '2'
            ],
            'b' => [
                  0 => '1',
                  1 => '2'
            ]
      ]);

      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a%5Bx%5D=1&amp;a%5By%5D=2&amp;b%5B0%5D=1&amp;b%5B1%5D=2', $link);

      $scheme->setEncodeAmpersands(false);
      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a%5Bx%5D=1&a%5By%5D=2&b%5B0%5D=1&b%5B1%5D=2', $link);

      $url->setQueryParameter('param name', 'param value');
      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a%5Bx%5D=1&a%5By%5D=2&b%5B0%5D=1&b%5B1%5D=2&param%20name=param%20value', $link);

      // encodeBlanks=false, encodeRfc3986=true (RFC encoding overwrites blanks encoding, thus "%20" still included)
      $scheme->setEncodeBlanks(false);
      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a%5Bx%5D=1&a%5By%5D=2&b%5B0%5D=1&b%5B1%5D=2&param%20name=param%20value', $link);

      // encodeBlanks=false, encodeRfc3986=false
      $scheme->setEncodeRfc3986(false);
      $link = $link = LinkGenerator::generateUrl($url, $scheme);
      $this->assertEquals('/?a[x]=1&a[y]=2&b[0]=1&b[1]=2&param name=param value', $link);
   }

}
