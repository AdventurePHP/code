<?php
namespace APF\tests\suites\tools\mail;

use APF\tools\mail\MailAddress;
use InvalidArgumentException;

/**
 * Tests the capabilities of an e-mail recipient (sender, recipient, CC recipient, or BCC recipient).
 */
class MailAddressTest extends \PHPUnit_Framework_TestCase {

   public function testConstructor1() {
      $this->expectException(InvalidArgumentException::class);
      new MailAddress(null, null);
   }

   public function testConstructor2() {
      $this->expectException(InvalidArgumentException::class);
      new MailAddress(null, 'foo');
   }

   public function testConstructor3() {
      $recipient = new MailAddress('', 'foo@bar.com');
      $this->assertNull($recipient->getName());
   }

   public function testStringRepresentation1() {
      $recipient = new MailAddress(null, 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());

      $recipient = new MailAddress('', 'foo@bar.com');
      $this->assertEquals('foo@bar.com', $recipient->__toString());
   }

   public function testStringRepresentation2() {
      $recipient = new MailAddress('Test name', 'foo@bar.com');
      $this->assertEquals('"Test name" <foo@bar.com>', $recipient->__toString());
   }

   public function testGetter() {
      $name = 'Test name';
      $email = 'foo@bar.com';
      $recipient = new MailAddress($name, $email);
      $this->assertEquals($name, $recipient->getName());
      $this->assertEquals($email, $recipient->getEmail());
   }

}
