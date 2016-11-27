<?php
namespace APF\tests\suites\tools\mail;

use APF\tools\mail\Recipient;
use InvalidArgumentException;

/**
 * Tests the capabilities of an e-mail recipient (sender, recipient, CC recipient, or BCC recipient).
 */
class RecipientTest extends \PHPUnit_Framework_TestCase {

   public function testConstructor1() {
      $this->expectException(InvalidArgumentException::class);
      new Recipient(null, null);
   }

   public function testConstructor2() {
      $this->expectException(InvalidArgumentException::class);
      new Recipient(null, 'foo');
   }

   public function testConstructor3() {
      $recipient = new Recipient('', 'foo@bar.com');
      $this->assertNull($recipient->getName());
   }

   public function testStringRepresentation1() {
      $recipient = new Recipient(null, 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());

      $recipient = new Recipient('', 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());
   }

   public function testStringRepresentation2() {
      $recipient = new Recipient('Test name', 'foo@bar.com');
      $this->assertEquals('"Test name" <foo@bar.com>', $recipient->__toString());
   }

   public function testGetter() {
      $name = 'Test name';
      $email = 'foo@bar.com';
      $recipient = new Recipient($name, $email);
      $this->assertEquals($name, $recipient->getName());
      $this->assertEquals($email, $recipient->getEmail());
   }

}
