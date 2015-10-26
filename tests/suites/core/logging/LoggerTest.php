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
namespace APF\tests\suites\core\logging;

use APF\core\logging\entry\SimpleLogEntry;
use APF\core\logging\LogEntry;
use APF\core\logging\Logger;
use APF\core\logging\LoggerException;
use APF\core\logging\writer\FileLogWriter;
use APF\core\logging\writer\StdOutLogWriter;
use APF\core\registry\Registry;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionProperty;

class LoggerTest extends \PHPUnit_Framework_TestCase {

   /**
    * Tests whether a initial file log writer is created.
    */
   public function testConstruction() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');
      $this->assertContains($target, $logger->getRegisteredTargets());
      $this->assertInstanceOf(FileLogWriter::class, $logger->getLogWriter($target));

      $threshold = new ReflectionProperty(Logger::class, 'logThreshold');
      $threshold->setAccessible(true);
      $this->assertEquals(Logger::$LOGGER_THRESHOLD_WARN, $threshold->getValue($logger));

   }

   /**
    * Tests adding a log writer.
    */
   public function testAddLogWriter() {

      $logger = new Logger();

      $writer = new StdOutLogWriter();
      $target = 'foo';
      $logger->addLogWriter($target, $writer);
      $this->assertContains($target, $logger->getRegisteredTargets());
      $this->assertInstanceOf(StdOutLogWriter::class, $logger->getLogWriter($target));

   }

   /**
    * Tests removing a log writer.
    */
   public function testRemoveLogWriter() {

      $logger = new Logger();

      $writer = new StdOutLogWriter();
      $targetOne = 'foo';
      $targetTwo = 'bar';

      $logger->addLogWriter($targetOne, clone $writer);
      $logger->addLogWriter($targetTwo, clone $writer);

      $this->assertContains($targetOne, $logger->getRegisteredTargets());
      $this->assertInstanceOf(StdOutLogWriter::class, $logger->getLogWriter($targetOne));
      $this->assertContains($targetTwo, $logger->getRegisteredTargets());
      $this->assertInstanceOf(StdOutLogWriter::class, $logger->getLogWriter($targetTwo));

      $logger->removeLogWriter($targetOne);

      $this->assertNotContains($targetOne, $logger->getRegisteredTargets());

      try {
         $this->assertInstanceOf(StdOutLogWriter::class, $logger->getLogWriter($targetOne));
         $this->fail('Log target "' . $targetOne . '" should have been removed!');
      } catch (LoggerException $e) {
         // expected behaviour
      }

      $this->assertContains($targetTwo, $logger->getRegisteredTargets());
      $this->assertInstanceOf(StdOutLogWriter::class, $logger->getLogWriter($targetTwo));

      $logger->removeLogWriter($targetTwo);

      $this->assertNotContains($targetOne, $logger->getRegisteredTargets());

      try {
         $logger->getLogWriter($targetOne);
         $this->fail('Log target "' . $targetOne . '" should have been removed!');
      } catch (LoggerException $e) {
         // expected behaviour
      }

      $this->assertNotContains($targetTwo, $logger->getRegisteredTargets());

      try {
         $logger->getLogWriter($targetTwo);
         $this->fail('Log target "' . $targetTwo . '" should have been removed!');
      } catch (LoggerException $e) {
         // expected behaviour
      }

   }

   /**
    * Tests string-based logging interface.
    */
   public function testLogEntry() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $messageOne = 'This is a log message!';
      $logger->logEntry($target, $messageOne);

      $messageTwo = 'This is another log message!';
      $logger->logEntry($target, $messageTwo, LogEntry::SEVERITY_FATAL);

      $entries = new ReflectionProperty(Logger::class, 'logEntries');
      $entries->setAccessible(true);

      $actual = $entries->getValue($logger);

      // test internal structure
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);
      $this->assertCount(2, $actual[$target]);

      /* @var $entryOne SimpleLogEntry */
      $entryOne = $actual[$target][0];
      $this->assertEquals($messageOne, $entryOne->getMessage());

      /* @var $entryTwo SimpleLogEntry */
      $entryTwo = $actual[$target][1];
      $this->assertEquals($messageTwo, $entryTwo->getMessage());
   }

   /**
    * Tests OO logging interface.
    */
   public function testAddEntry1() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $messageOne = 'This is a log message!';
      $logger->addEntry(new SimpleLogEntry($target, $messageOne, LogEntry::SEVERITY_INFO));

      $messageTwo = 'This is another log message!';
      $logger->addEntry(new SimpleLogEntry($target, $messageTwo, LogEntry::SEVERITY_FATAL));

      $entries = new ReflectionProperty(Logger::class, 'logEntries');
      $entries->setAccessible(true);

      $actual = $entries->getValue($logger);

      // test internal structure
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);
      $this->assertCount(2, $actual[$target]);

      /* @var $entryOne SimpleLogEntry */
      $entryOne = $actual[$target][0];
      $this->assertEquals($messageOne, $entryOne->getMessage());

      /* @var $entryTwo SimpleLogEntry */
      $entryTwo = $actual[$target][1];
      $this->assertEquals($messageTwo, $entryTwo->getMessage());

   }

   /**
    * Test auto-flush.
    */
   public function testAddEntry2() {

      /* @var $logger Logger|PHPUnit_Framework_MockObject_MockObject */
      $logger = $this->getMock(Logger::class, ['flushLogBuffer']);

      $logger->expects($this->exactly(2))
            ->method('flushLogBuffer')
            ->will($this->returnCallback(
                  function () use ($logger) {
                     // simply reset entry count to mimic normal behaviour
                     $entryCount = new ReflectionProperty(Logger::class, 'logEntryCount');
                     $entryCount->setAccessible(true);
                     $entryCount->setValue($logger, 0);
                  })
            );
      $logger->setMaxBufferLength(2);

      // 3rd log entry triggers an auto-flush, hence double
      // log entry number for double-flush.
      for ($i = 1; $i <= 6; $i++) {
         $logger->logEntry('foo', 'bar');
      }

   }

   /**
    * Test flushing. Check whether log entries are delegated to their
    * respective writer.
    */
   public function testFlush() {

      $logger = new Logger();

      $bufferOne = '';
      $counterOne = 0;

      /* @var $writerOne StdOutLogWriter|PHPUnit_Framework_MockObject_MockObject */
      $writerOne = $this->getMock(StdOutLogWriter::class, ['writeLogEntries']);
      $writerOne->expects($this->once())
            ->method('writeLogEntries')
            ->will($this->returnCallback(
                  function (array $entries) use (&$bufferOne, &$counterOne) {
                     /* @var $entries LogEntry[] */
                     foreach ($entries as $entry) {
                        $bufferOne .= $entry->__toString();
                        $counterOne++;
                     }
                  }
            ));

      $targetOne = 'foo';
      $logger->addLogWriter($targetOne, $writerOne);

      $bufferTwo = '';
      $counterTwo = 0;

      /* @var $writerTwo StdOutLogWriter|PHPUnit_Framework_MockObject_MockObject */
      $writerTwo = $this->getMock(StdOutLogWriter::class, ['writeLogEntries']);
      $writerTwo->expects($this->once())
            ->method('writeLogEntries')
            ->will($this->returnCallback(
                  function (array $entries) use (&$bufferTwo, &$counterTwo) {
                     /* @var $entries LogEntry[] */
                     foreach ($entries as $entry) {
                        $bufferTwo .= $entry->__toString();
                        $counterTwo++;
                     }
                  }
            ));

      $targetTwo = 'bar';
      $logger->addLogWriter($targetTwo, $writerTwo);

      $logMessage = 'Log Message!';

      for ($i = 0; $i < 10; $i++) {
         $logger->logEntry($targetOne, $logMessage);
      }

      for ($i = 0; $i < 15; $i++) {
         $logger->logEntry($targetTwo, $logMessage);
      }

      $logger->flushLogBuffer();

      $this->assertEquals(10, $counterOne);
      $this->assertEquals(10, substr_count($bufferOne, $logMessage));

      $this->assertEquals(15, $counterTwo);
      $this->assertEquals(15, substr_count($bufferTwo, $logMessage));

   }

   /**
    * Test whether threshold level definition is resulting in the correct
    * log entries.
    */
   public function testThresholds() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $entries = new ReflectionProperty(Logger::class, 'logEntries');
      $entries->setAccessible(true);

      // test hiding messages below/not within threshold
      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_DEBUG);

      $actual = $entries->getValue($logger);
      $this->assertEmpty($actual);
      $this->assertCount(0, $actual);

      // test message will be logged once threshold adjusted
      $logger->setLogThreshold(Logger::$LOGGER_THRESHOLD_ALL);

      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_DEBUG);

      $actual = $entries->getValue($logger);
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);

      // test hiding all messages with empty threshold definition
      $logger = new Logger();
      $logger->setLogThreshold([]);

      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_DEBUG);
      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_INFO);
      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_WARNING);
      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_ERROR);
      $logger->logEntry($target, 'This is a log message!', LogEntry::SEVERITY_FATAL);

      $actual = $entries->getValue($logger);
      $this->assertEmpty($actual);
      $this->assertCount(0, $actual);
   }

   /**
    * Tests whether a given amount of severe log entries overwrites
    * the log threshold settings to all. This is used to gather all
    * relevant information in case something severe happens.
    */
   public function testOverwriteLogThreshold() {
      // new feature ID#269
   }

}
