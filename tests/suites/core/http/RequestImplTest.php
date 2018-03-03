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
namespace APF\tests\suites\core\http;

use APF\core\http\RequestImpl;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Request implementation. Written due to bug with "0" values.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1<br />
 * Version 0.2, 26.08.2013 (Refactoring or RequestHandler to include functionality of PostHandler)<br />
 */
class RequestImplTest extends TestCase {

   const PARAMETER_FOO = 'foo';
   const PARAMETER_BAR = 'bar';

   public function testSimpleValues() {

      $testData = [
            self::PARAMETER_FOO   => self::PARAMETER_BAR,
            self::PARAMETER_BAR   => '!"§$%&/()=}][{1223{[]}',
            '4356J76tiojoigztfgu' => 'µ0815{[]}\][€edrftgzhujhgvcdewrtzujhgfdsaölkjhiug u qwhph phpihpiqw ph p9h pwoq',
            'urlencoded-data'     => urlencode('This is a message with spaces and other content like "/", "&", "%", "$", and "§".'),
            0                     => 'zero',
            '1'                   => 'one'
      ];

      $request = new RequestImpl();

      $_REQUEST = [];
      foreach ($testData as $key => $value) {

         $_GET[$key] = $value;
         $this->assertEquals($request->getGetParameter($key), $value);

         $_POST[$key] = $value;
         $this->assertEquals($request->getPostParameter($key), $value);

         $_REQUEST[$key] = $value;
         $this->assertEquals($request->getParameter($key), $value);
      }

   }

   public function testEmptyValues() {

      $request = new RequestImpl();

      $_GET = [];
      $this->assertNull($request->getGetParameter(self::PARAMETER_FOO));

      $_POST = [];
      $this->assertNull($request->getPostParameter(self::PARAMETER_FOO));

      $_REQUEST = [];
      $this->assertNull($request->getParameter(self::PARAMETER_FOO));

      $_GET[self::PARAMETER_BAR] = '';
      $this->assertNull($request->getGetParameter(self::PARAMETER_BAR));

      $_POST[self::PARAMETER_BAR] = '';
      $this->assertNull($request->getPostParameter(self::PARAMETER_BAR));

      $_REQUEST[self::PARAMETER_BAR] = '';
      $this->assertNull($request->getParameter(self::PARAMETER_BAR));
   }

   public function testZeroValues() {

      $request = new RequestImpl();

      $_GET = [];

      $_GET[self::PARAMETER_FOO] = 0;
      $this->assertEquals(0, $request->getGetParameter(self::PARAMETER_FOO));

      $_GET[self::PARAMETER_BAR] = '0';
      $this->assertEquals('0', $request->getGetParameter(self::PARAMETER_BAR));

      $_POST = [];

      $_POST[self::PARAMETER_FOO] = 0;
      $this->assertEquals(0, $request->getPostParameter(self::PARAMETER_FOO));

      $_POST[self::PARAMETER_BAR] = '0';
      $this->assertEquals('0', $request->getPostParameter(self::PARAMETER_BAR));

      $_REQUEST = [];

      $_REQUEST[self::PARAMETER_FOO] = 0;
      $this->assertEquals(0, $request->getParameter(self::PARAMETER_FOO));

      $_REQUEST[self::PARAMETER_BAR] = '0';
      $this->assertEquals('0', $request->getParameter(self::PARAMETER_BAR));

   }

   public function testParameterBoundaries() {

      // separation of GET and POST - starting at GET
      $_GET = [];
      $_POST = [];
      $_REQUEST = [];
      $_GET[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_GET[self::PARAMETER_FOO];

      $request = new RequestImpl();

      $this->assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO));
      $this->assertEquals(self::PARAMETER_BAR, $request->getGetParameter(self::PARAMETER_FOO));
      $this->assertEquals(null, $request->getPostParameter(self::PARAMETER_FOO));

      // separation of GET and POST - starting at POST
      $_GET = [];
      $_POST = [];
      $_REQUEST = [];
      $_POST[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_POST[self::PARAMETER_FOO];

      $this->assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO));
      $this->assertEquals(self::PARAMETER_BAR, $request->getPostParameter(self::PARAMETER_FOO));
      $this->assertEquals(null, $request->getGetParameter(self::PARAMETER_FOO));

   }

   public function testDefaultValue() {

      $_GET = [];
      $_POST = [];
      $_REQUEST = [];
      $_GET[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_GET[self::PARAMETER_FOO];

      $request = new RequestImpl();

      $this->assertNull($request->getPostParameter(self::PARAMETER_FOO));

      $default = 'OneTwoThree';
      $this->assertEquals($default, $request->getPostParameter(self::PARAMETER_FOO, $default));

      $this->assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO, $default));

   }

   public function testNestedRequestParameters() {

      $request = new RequestImpl();

      // ?a[x]=1&a[y]=2&b[]=1&b[]=2&b[2]=3&b[1]=7
      $_REQUEST = [
            'a' => [
                  'x' => '1',
                  'y' => '2'
            ],
            'b' => [1, 7, 3]
      ];

      $a = $request->getParameter('a');
      $this->assertTrue(is_array($a));
      $this->assertEquals('1', $a['x']);
      $this->assertEquals('2', $a['y']);

      $b = $request->getParameter('b');
      $this->assertTrue(is_array($b));
      $this->assertEquals('1', $b[0]);
      $this->assertEquals('7', $b[1]);
      $this->assertEquals('3', $b[2]);

      // ?a[][foo]=123&a[][foo]=123&a[1][bar]=456&b[c][d][e][f]=123
      $_REQUEST = [
            'a' => [
                  ['foo' => '123'],
                  [
                        'foo' => '123',
                        'bar' => '456'
                  ]
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
      ];

      $a = $request->getParameter('a');
      $this->assertTrue(is_array($a));
      $this->assertEquals('123', $a[0]['foo']);
      $this->assertEquals('123', $a[1]['foo']);
      $this->assertEquals('456', $a[1]['bar']);

      $b = $request->getParameter('b');
      $this->assertTrue(is_array($b));
      $this->assertTrue(is_array($b['c']));
      $this->assertTrue(is_array($b['c']['d']));
      $this->assertTrue(is_array($b['c']['d']['e']));
      $this->assertEquals('123', $b['c']['d']['e']['f']);

   }

   protected function setUp() {
      $_GET = [];
      $_POST = [];
      $_REQUEST = [];
   }

   protected function tearDown() {
      unset($_REQUEST);
      unset($_GET);
      unset($_POST);
   }

}
