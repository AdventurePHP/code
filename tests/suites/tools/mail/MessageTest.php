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
namespace APF\tests\suites\tools\mail;

use APF\core\registry\Registry;
use APF\tools\mail\Message;
use APF\tools\mail\MessageException;
use APF\tools\mail\Recipient;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionMethod;

/**
 * Tests the capabilities of the e-mail message.
 */
class MessageTest extends \PHPUnit_Framework_TestCase {

   const SUBJECT = 'Subject';
   const CONTENT = 'This is the content';

   public function testMessageConstruction() {

      $sender = $this->getRecipient();

      $message = new Message($sender, self::SUBJECT, self::CONTENT);

      $this->assertEquals(
            $sender,
            $message->getSender()
      );
      $this->assertEquals(
            self::SUBJECT,
            $message->getSubject()
      );
      $this->assertEquals(
            self::CONTENT,
            $message->getContent()
      );
      $this->assertEquals(
            'plain/text; charset=' . Registry::retrieve('APF\core', 'Charset'),
            $message->getContentType()
      );
   }

   /**
    * @return Recipient
    */
   private function getRecipient() {
      return new Recipient('adventure-php-framework.org', 'info@adventure-php-framework.org');
   }

   public function testContentType() {

      $message = new Message($this->getRecipient(), self::SUBJECT, self::CONTENT);

      $contentType = 'text/html; charset=UTF-8';
      $message->setContentType($contentType);

      $this->assertEquals(
            $contentType,
            $message->getContentType()
      );
   }

   public function testRecipients() {

      $sender = $this->getRecipient();
      $message = new Message($sender, self::SUBJECT, self::CONTENT);

      $this->assertEmpty($message->getRecipients());
      $this->assertEmpty($message->getCopyRecipients());
      $this->assertEmpty($message->getBlindCopyRecipients());

      // Add recipients
      $secondRecipient = new Recipient('Foo', 'foo@example.com');
      $message->setRecipients([$sender]);
      $message->addRecipient($secondRecipient);
      $recipients = $message->getRecipients();

      $this->assertCount(2, $recipients);

      $this->assertEquals($sender, $recipients[0]);
      $this->assertEquals($secondRecipient, $recipients[1]);

      $this->assertEmpty($message->getCopyRecipients());
      $this->assertEmpty($message->getBlindCopyRecipients());

      // Add copy recipients
      $message->addCopyRecipient($sender);

      $copyRecipients = $message->getCopyRecipients();

      $this->assertCount(1, $copyRecipients);

      $this->assertEmpty($message->getBlindCopyRecipients());

      // Add blind copy recipients
      $message->setBlindCopyRecipients([$sender, $secondRecipient]);
      $message->addBlindCopyRecipient($sender);

      $blindCopyRecipients = $message->getBlindCopyRecipients();

      $this->assertCount(3, $blindCopyRecipients);

      $this->assertEquals($sender, $blindCopyRecipients[0]);
      $this->assertEquals($secondRecipient, $blindCopyRecipients[1]);
      $this->assertEquals($sender, $blindCopyRecipients[2]);

      // Clear recipient lists
      $this->assertNotEmpty($message->getRecipients());
      $this->assertNotEmpty($message->getCopyRecipients());
      $this->assertNotEmpty($message->getBlindCopyRecipients());

      $message->clearBlindCopyRecipients();

      $this->assertNotEmpty($message->getRecipients());
      $this->assertNotEmpty($message->getCopyRecipients());
      $this->assertEmpty($message->getBlindCopyRecipients());

      $message->clearCopyRecipients();

      $this->assertNotEmpty($message->getRecipients());
      $this->assertEmpty($message->getCopyRecipients());
      $this->assertEmpty($message->getBlindCopyRecipients());

      $message->clearRecipients();

      $this->assertEmpty($message->getRecipients());
      $this->assertEmpty($message->getCopyRecipients());
      $this->assertEmpty($message->getBlindCopyRecipients());
   }

   public function testPriority() {

      $message = new Message($this->getRecipient(), self::SUBJECT, self::CONTENT);

      $this->assertEquals(Message::PRIORITY_NORMAL, $message->getPriority());

      $message->setPriority(Message::PRIORITY_HIGH);
      $this->assertEquals(Message::PRIORITY_HIGH, $message->getPriority());
   }

   public function testReturnPath() {

      $sender = $this->getRecipient();
      $message = new Message($sender, self::SUBJECT, self::CONTENT);

      $this->assertNull($message->getReturnPath());

      $message->setReturnPath($sender);
      $this->assertEquals($sender, $message->getReturnPath());
   }

   public function testHeaderCreation() {

      $method = new ReflectionMethod(Message::class, 'getAdditionalHeaders');
      $method->setAccessible(true);

      $sender = $this->getRecipient();
      $message = new Message($sender, self::SUBJECT, self::CONTENT);
      $message->addRecipient($sender);
      $message->addCopyRecipient($sender);
      $message->addBlindCopyRecipient($sender);
      $message->setReturnPath($sender);

      $this->assertEquals('From: ' . $sender . PHP_EOL
            . 'CC: ' . $sender . PHP_EOL
            . 'BCC: ' . $sender . PHP_EOL
            . 'Content-Type: plain/text; charset=UTF-8' . PHP_EOL
            . 'Return-Path: ' . $sender->getEmail() . PHP_EOL
            . 'X-Priority: 1' . PHP_EOL
            . 'MIME-Version: 1.0' . PHP_EOL
            . 'X-Sender: APF-E-Mail-Client' . PHP_EOL
            . 'X-Mailer: PHP/' . phpversion(),
            $method->invoke($message));
   }

   /**
    * Test sending message throws exception for empty recipient lists.
    */
   public function testSend1() {

      $this->expectException(MessageException::class);
      $this->expectExceptionMessage('Cannot send e-mail w/o any recipients!');

      $message = new Message($this->getRecipient(), self::SUBJECT, self::CONTENT);
      $message->send();
   }

   /**
    * Test sending simple message leads to message exception in case mail() fails.
    */
   public function testSend2() {

      $exceptionMessage = 'Sending e-mail failed!';

      $this->expectException(MessageException::class);
      $this->expectExceptionMessage($exceptionMessage);

      $recipient = $this->getRecipient();

      /* @var $message Message|PHPUnit_Framework_MockObject_MockObject */
      $message = $this->getMockBuilder(Message::class)
            ->setMethods(['mail'])
            ->setConstructorArgs([$recipient, self::SUBJECT, self::CONTENT])
            ->getMock();

      $message->method('mail')
            ->willThrowException(new MessageException($exceptionMessage));

      $message->addRecipient($recipient);

      $message->send();
   }


   /**
    * Test happy case sending simple message.
    */
   public function testSend3() {

      $recipient = $this->getRecipient();

      /* @var $message Message|PHPUnit_Framework_MockObject_MockObject */
      $message = $this->getMockBuilder(Message::class)
            ->setMethods(['mail'])
            ->setConstructorArgs([$recipient, self::SUBJECT, self::CONTENT])
            ->getMock();

      $message->method('mail')
            ->willReturn(true);

      $message->addRecipient($recipient);

      $message->send();
   }

}
