<?php
namespace APF\tests\suites\tools\mail;

use APF\tools\mail\Message;
use APF\tools\mail\Recipient;

class MessageTest extends \PHPUnit_Framework_TestCase {

   public function _testSendMessage() {

      $sender = new Recipient('adventure-php-framework.org', 'info@adventure-php-framework.org');
      $message = new Message($sender, 'Subject', 'This is the content');

      $message->setRecipients([]);
      $message->addRecipient(new Recipient('Foo', 'foo@example.com'));

      $message->send();
   }

   public function _testSendCCMessage() {

   }

   public function _testSendBCCMessage() {

   }

}
