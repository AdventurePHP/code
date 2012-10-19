<?php
import('tools::request', 'PostHandler');

/**
 * @package tests::suites::tools::request
 * @class PostHandlerTest
 *
 * Tests the PostHandler. Written due to bug with "0" values.
 *
 * @author Christian Achatz
 */
class PostHandlerTest extends PHPUnit_Framework_TestCase {

   protected function setUp() {
      $_POST = array();
   }

   protected function tearDown() {
      unset($_POST);
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

      $_POST = array();
      foreach ($testData as $key => $value) {
         $_POST[$key] = $value;
         assertEquals(
            PostHandler::getValue($key),
            $value
         );
      }

   }

   public function testEmptyValues() {

      $_POST = array();

      assertNull(
         PostHandler::getValue('foo')
      );

      $_POST['bar'] = '';

      assertNull(
         PostHandler::getValue('bar')
      );
   }

   public function testZeroValues() {

      $_POST = array();

      $_POST['foo'] = 0;
      assertEquals(0, PostHandler::getValue('foo'));

      $_POST['bar'] = '0';
      assertEquals('0', PostHandler::getValue('bar'));

   }

}
