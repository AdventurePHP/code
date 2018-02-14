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

/**
 * Tests all capabilities of the Logger.
 */
class LoggerTest extends \PHPUnit_Framework_TestCase {

   const LOG_MESSAGE_ONE = 'This is a log message!';
   const LOG_MESSAGE_TWO = 'This is another log message!';

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

      $logger->logEntry($target, self::LOG_MESSAGE_ONE);
      $logger->logEntry($target, self::LOG_MESSAGE_TWO, LogEntry::SEVERITY_FATAL);

      $entries = new ReflectionProperty(Logger::class, 'logEntries');
      $entries->setAccessible(true);

      $actual = $entries->getValue($logger);

      // test internal structure
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);
      $this->assertCount(2, $actual[$target]);

      /* @var $entryOne SimpleLogEntry */
      $entryOne = $actual[$target][0];
      $this->assertEquals(self::LOG_MESSAGE_ONE, $entryOne->getMessage());

      /* @var $entryTwo SimpleLogEntry */
      $entryTwo = $actual[$target][1];
      $this->assertEquals(self::LOG_MESSAGE_TWO, $entryTwo->getMessage());
   }

   /**
    * Tests OO logging interface.
    */
   public function testAddEntry1() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $logger->addEntry(new SimpleLogEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_INFO));
      $logger->addEntry(new SimpleLogEntry($target, self::LOG_MESSAGE_TWO, LogEntry::SEVERITY_FATAL));

      $entries = new ReflectionProperty(Logger::class, 'logEntries');
      $entries->setAccessible(true);

      $actual = $entries->getValue($logger);

      // test internal structure
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);
      $this->assertCount(2, $actual[$target]);

      /* @var $entryOne SimpleLogEntry */
      $entryOne = $actual[$target][0];
      $this->assertEquals(self::LOG_MESSAGE_ONE, $entryOne->getMessage());

      /* @var $entryTwo SimpleLogEntry */
      $entryTwo = $actual[$target][1];
      $this->assertEquals(self::LOG_MESSAGE_TWO, $entryTwo->getMessage());

   }

   /**
    * Test auto-flush.
    */
   public function testAddEntry2() {

      /* @var $logger Logger|PHPUnit_Framework_MockObject_MockObject */
      $logger = $this->getMockBuilder(Logger::class)
            ->setMethods(['flushLogBuffer'])
            ->getMock();

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
      $writerOne = $this->getMockBuilder(StdOutLogWriter::class)
            ->setMethods(['writeLogEntries'])
            ->getMock();
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
      $writerTwo = $this->getMockBuilder(StdOutLogWriter::class)
            ->setMethods(['writeLogEntries'])
            ->getMock();
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

      for ($i = 0; $i < 10; $i++) {
         $logger->logEntry($targetOne, self::LOG_MESSAGE_ONE);
      }

      for ($i = 0; $i < 15; $i++) {
         $logger->logEntry($targetTwo, self::LOG_MESSAGE_ONE);
      }

      $logger->flushLogBuffer();

      $this->assertEquals(10, $counterOne);
      $this->assertEquals(10, substr_count($bufferOne, self::LOG_MESSAGE_ONE));

      $this->assertEquals(15, $counterTwo);
      $this->assertEquals(15, substr_count($bufferTwo, self::LOG_MESSAGE_ONE));

   }

   /**
    * Test whether threshold level definition is resulting in the correct
    * log entries.
    */
   public function testThresholds() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $writer = new RecordingLogWriter();
      $logger->addLogWriter($target, $writer);

      // test hiding messages below/not within threshold
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_DEBUG);

      $logger->flushLogBuffer();

      $actual = $writer->getEntries();
      $this->assertEmpty($actual);
      $this->assertCount(0, $actual);

      // test message will be logged once threshold adjusted
      $logger->setLogThreshold(Logger::$LOGGER_THRESHOLD_ALL);

      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_DEBUG);

      $logger->flushLogBuffer();

      $actual = $writer->getEntries();
      $this->assertNotEmpty($actual);
      $this->assertCount(1, $actual);

      // test hiding all messages with empty threshold definition
      $logger = new Logger();

      $writer = new RecordingLogWriter();
      $logger->addLogWriter($target, $writer);

      $logger->setLogThreshold([]);

      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_DEBUG);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_INFO);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_WARNING);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_ERROR);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_FATAL);

      $logger->flushLogBuffer();

      $actual = $writer->getEntries();
      $this->assertEmpty($actual);
      $this->assertCount(0, $actual);
   }

   /**
    * Tests whether a given amount of error log entries overwrites
    * the log threshold settings to all. This is used to gather all
    * relevant information in case something severe happens.
    */
   public function testThresholdOverride1() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $writer = new RecordingLogWriter();
      $logger->addLogWriter($target, $writer);

      $logger->setLogThreshold(Logger::$LOGGER_THRESHOLD_ERROR);

      // log all in case more than 5 errors or more than 1 fatal comes up
      $logger->setThresholdOverride([
            LogEntry::SEVERITY_ERROR => 5
      ]);

      // test 6 errors overriding the threshold of 5
      for ($i = 1; $i < 7; $i++) {
         $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_ERROR);
      }

      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_DEBUG);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_INFO);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_WARNING);

      $logger->flushLogBuffer();

      $actual = $writer->getEntries();
      $this->assertNotEmpty($actual);
      $this->assertCount(9, $actual);

   }

   /*
    * Tests whether one fatal error overwrites the log threshold
    * settings to all. This is used to gather all relevant
    * information in case something severe happens.
    */
   public function testThresholdOverride2() {

      $logger = new Logger();

      $target = Registry::retrieve('APF\core', 'InternalLogTarget');

      $writer = new RecordingLogWriter();
      $logger->addLogWriter($target, $writer);

      $logger->setLogThreshold(Logger::$LOGGER_THRESHOLD_ERROR);

      // log all in case more than 20 errors or more than 1 fatal comes up
      $logger->setThresholdOverride([
            LogEntry::SEVERITY_ERROR => 20,
            LogEntry::SEVERITY_FATAL => 1
      ]);

      // test than one fatal overrides
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_FATAL);

      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_DEBUG);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_INFO);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_WARNING);
      $logger->logEntry($target, self::LOG_MESSAGE_ONE, LogEntry::SEVERITY_ERROR);

      $logger->flushLogBuffer();

      $actual = $writer->getEntries();
      $this->assertNotEmpty($actual);
      $this->assertCount(5, $actual);
   }

}
