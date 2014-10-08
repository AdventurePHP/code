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
namespace APF\tests\suites\core\http;

use APF\core\http\RequestImpl;

/**
 * Tests the RequestHandler. Written due to bug with "0" values.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1<br />
 * Version 0.2, 26.08.2013 (Refactoring or RequestHandler to include functionality of PostHandler)<br />
 */
class RequestImplTest extends \PHPUnit_Framework_TestCase {

   const PARAMETER_FOO = 'foo';
   const PARAMETER_BAR = 'bar';

   protected function setUp() {
      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
   }

   protected function tearDown() {
      unset($_REQUEST);
      unset($_GET);
      unset($_POST);
   }

   public function testSimpleValues() {

      $testData = array(
            self::PARAMETER_FOO   => self::PARAMETER_BAR,
            self::PARAMETER_BAR   => '!"§$%&/()=}][{1223{[]}',
            '4356J76tiojoigztfgu' => 'µ0815{[]}\][€edrftgzhujhgvcdewrtzujhgfdsaölkjhiug u qwhph phpihpiqw ph p9h pwoq',
            'urlencoded-data'     => urlencode('This is a message with spaces and other content like "/", "&", "%", "$", and "§".'),
            0                     => 'zero',
            '1'                   => 'one'
      );

      $request = new RequestImpl();

      $_REQUEST = array();
      foreach ($testData as $key => $value) {

         $_GET[$key] = $value;
         assertEquals($request->getGetParameter($key), $value);

         $_POST[$key] = $value;
         assertEquals($request->getPostParameter($key), $value);

         $_REQUEST[$key] = $value;
         assertEquals($request->getParameter($key), $value);
      }

   }

   public function testEmptyValues() {

      $request = new RequestImpl();

      $_GET = array();
      assertNull($request->getGetParameter(self::PARAMETER_FOO));

      $_POST = array();
      assertNull($request->getPostParameter(self::PARAMETER_FOO));

      $_REQUEST = array();
      assertNull($request->getParameter(self::PARAMETER_FOO));

      $_GET[self::PARAMETER_BAR] = '';
      assertNull($request->getGetParameter(self::PARAMETER_BAR));

      $_POST[self::PARAMETER_BAR] = '';
      assertNull($request->getPostParameter(self::PARAMETER_BAR));

      $_REQUEST[self::PARAMETER_BAR] = '';
      assertNull($request->getParameter(self::PARAMETER_BAR));
   }

   public function testZeroValues() {

      $request = new RequestImpl();

      $_GET = array();

      $_GET[self::PARAMETER_FOO] = 0;
      assertEquals(0, $request->getGetParameter(self::PARAMETER_FOO));

      $_GET[self::PARAMETER_BAR] = '0';
      assertEquals('0', $request->getGetParameter(self::PARAMETER_BAR));

      $_POST = array();

      $_POST[self::PARAMETER_FOO] = 0;
      assertEquals(0, $request->getPostParameter(self::PARAMETER_FOO));

      $_POST[self::PARAMETER_BAR] = '0';
      assertEquals('0', $request->getPostParameter(self::PARAMETER_BAR));

      $_REQUEST = array();

      $_REQUEST[self::PARAMETER_FOO] = 0;
      assertEquals(0, $request->getParameter(self::PARAMETER_FOO));

      $_REQUEST[self::PARAMETER_BAR] = '0';
      assertEquals('0', $request->getParameter(self::PARAMETER_BAR));

   }

   public function testParameterBoundaries() {

      // separation of GET and POST - starting at GET
      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
      $_GET[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_GET[self::PARAMETER_FOO];

      $request = new RequestImpl();

      assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO));
      assertEquals(self::PARAMETER_BAR, $request->getGetParameter(self::PARAMETER_FOO));
      assertEquals(null, $request->getPostParameter(self::PARAMETER_FOO));

      // separation of GET and POST - starting at POST
      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
      $_POST[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_POST[self::PARAMETER_FOO];

      assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO));
      assertEquals(self::PARAMETER_BAR, $request->getPostParameter(self::PARAMETER_FOO));
      assertEquals(null, $request->getGetParameter(self::PARAMETER_FOO));

   }

   public function testDefaultValue() {

      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
      $_GET[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_GET[self::PARAMETER_FOO];

      $request = new RequestImpl();

      assertNull($request->getPostParameter(self::PARAMETER_FOO));

      $default = 'OneTwoThree';
      assertEquals($default, $request->getPostParameter(self::PARAMETER_FOO, $default));

      assertEquals(self::PARAMETER_BAR, $request->getParameter(self::PARAMETER_FOO, $default));

   }

}
