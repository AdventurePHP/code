<?php
namespace APF\tests\suites\tools\request;

use APF\tools\request\RequestHandler;

/**
 * @package APF\tests\suites\tools\request
 * @class RequestHandlerTest
 *
 * Tests the RequestHandler. Written due to bug with "0" values.
 *
 * @author Christian Achatz
 */
class RequestHandlerTest extends PHPUnit_Framework_TestCase {

   protected function setUp() {
      $_REQUEST = array();
   }

   protected function tearDown() {
      unset($_REQUEST);
   }

   public function testSimpleValues() {

      $testData = array(
         'foo' => 'bar',
         'bar' => '!"§$%&/()=}][{1223{[]}',
         '4356J76tiojoigztfgu' => 'µ0815{[]}\][€edrftgzhujhgvcdewrtzujhgfdsaölkjhiug u qwhph phpihpiqw ph p9h pwoq',
         'urlencoded-data' => urlencode('This is a message with spaces and other content like "/", "&", "%", "$", and "§".'),
         0 => 'zero',
         '1' => 'one'
      );

      $_REQUEST = array();
      foreach ($testData as $key => $value) {
         $_REQUEST[$key] = $value;
         assertEquals(
            RequestHandler::getValue($key),
            $value
         );
      }

   }

   public function testEmptyValues() {

      $_REQUEST = array();

      assertNull(
         RequestHandler::getValue('foo')
      );

      $_REQUEST['bar'] = '';

      assertNull(
         RequestHandler::getValue('bar')
      );
   }

   public function testZeroValues() {

      $_REQUEST = array();

      $_REQUEST['foo'] = 0;
      assertEquals(0, RequestHandler::getValue('foo'));

      $_REQUEST['bar'] = '0';
      assertEquals('0', RequestHandler::getValue('bar'));

   }

}
