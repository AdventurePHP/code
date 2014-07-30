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
namespace APF\tests\suites\tools\request;

use APF\tools\request\RequestHandler;

/**
 * Tests the RequestHandler. Written due to bug with "0" values.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1<br />
 * Version 0.2, 26.08.2013 (Refactoring or RequestHandler to include functionality of PostHandler)<br />
 */
class RequestHandlerTest extends \PHPUnit_Framework_TestCase {

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
         self::PARAMETER_FOO => self::PARAMETER_BAR,
         self::PARAMETER_BAR => '!"§$%&/()=}][{1223{[]}',
         '4356J76tiojoigztfgu' => 'µ0815{[]}\][€edrftgzhujhgvcdewrtzujhgfdsaölkjhiug u qwhph phpihpiqw ph p9h pwoq',
         'urlencoded-data' => urlencode('This is a message with spaces and other content like "/", "&", "%", "$", and "§".'),
         0 => 'zero',
         '1' => 'one'
      );

      $_REQUEST = array();
      foreach ($testData as $key => $value) {

         $_GET[$key] = $value;
         assertEquals(
            RequestHandler::getValue($key, null, RequestHandler::USE_GET_PARAMS),
            $value
         );

         $_POST[$key] = $value;
         assertEquals(
            RequestHandler::getValue($key, null, RequestHandler::USE_POST_PARAMS),
            $value
         );

         $_REQUEST[$key] = $value;
         assertEquals(
            RequestHandler::getValue($key),
            $value
         );
      }

   }

   public function testEmptyValues() {

      $_GET = array();
      assertNull(
         RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_GET_PARAMS)
      );

      $_POST = array();
      assertNull(
         RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_POST_PARAMS)
      );

      $_REQUEST = array();
      assertNull(
         RequestHandler::getValue(self::PARAMETER_FOO)
      );

      $_GET[self::PARAMETER_BAR] = '';
      assertNull(
         RequestHandler::getValue(self::PARAMETER_BAR, null, RequestHandler::USE_GET_PARAMS)
      );

      $_POST[self::PARAMETER_BAR] = '';
      assertNull(
         RequestHandler::getValue(self::PARAMETER_BAR, null, RequestHandler::USE_POST_PARAMS)
      );

      $_REQUEST[self::PARAMETER_BAR] = '';
      assertNull(
         RequestHandler::getValue(self::PARAMETER_BAR)
      );
   }

   public function testZeroValues() {

      $_GET = array();

      $_GET[self::PARAMETER_FOO] = 0;
      assertEquals(0, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_GET_PARAMS));

      $_GET[self::PARAMETER_BAR] = '0';
      assertEquals('0', RequestHandler::getValue(self::PARAMETER_BAR, null, RequestHandler::USE_GET_PARAMS));

      $_POST = array();

      $_POST[self::PARAMETER_FOO] = 0;
      assertEquals(0, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_POST_PARAMS));

      $_POST[self::PARAMETER_BAR] = '0';
      assertEquals('0', RequestHandler::getValue(self::PARAMETER_BAR, null, RequestHandler::USE_POST_PARAMS));

      $_REQUEST = array();

      $_REQUEST[self::PARAMETER_FOO] = 0;
      assertEquals(0, RequestHandler::getValue(self::PARAMETER_FOO));

      $_REQUEST[self::PARAMETER_BAR] = '0';
      assertEquals('0', RequestHandler::getValue(self::PARAMETER_BAR));

   }

   public function testParameterBoundaries() {

      // separation of GET and POST - starting at GET
      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
      $_GET[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_GET[self::PARAMETER_FOO];

      assertEquals(self::PARAMETER_BAR, RequestHandler::getValue(self::PARAMETER_FOO));
      assertEquals(self::PARAMETER_BAR, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_GET_PARAMS));
      assertEquals(null, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_POST_PARAMS));

      // separation of GET and POST - starting at POST
      $_GET = array();
      $_POST = array();
      $_REQUEST = array();
      $_POST[self::PARAMETER_FOO] = self::PARAMETER_BAR;
      $_REQUEST[self::PARAMETER_FOO] = $_POST[self::PARAMETER_FOO];

      assertEquals(self::PARAMETER_BAR, RequestHandler::getValue(self::PARAMETER_FOO));
      assertEquals(self::PARAMETER_BAR, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_POST_PARAMS));
      assertEquals(null, RequestHandler::getValue(self::PARAMETER_FOO, null, RequestHandler::USE_GET_PARAMS));

   }

}
