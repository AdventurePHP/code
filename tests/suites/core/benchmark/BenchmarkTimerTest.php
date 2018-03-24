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
namespace APF\tests\suites\core\benchmark;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\benchmark\PlainTextReport;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BenchmarkTimerTest extends TestCase {

   public function testDisabling() {
      $timer = new BenchmarkTimer();
      $timer->disable();

      $name = 'Process Name';
      $timer->start($name);
      $timer->stop($name);

      $this->assertContains('Stop watch is currently disabled.', $timer->createReport());
   }

   public function testMeasurement() {

      $timer = new BenchmarkTimer();

      $timer->disable();
      $timer->enable();

      $name = 'Process Name';
      $timer->start($name);
      sleep(1);
      $timer->stop($name);

      $this->assertEquals(1, round($timer->getTotalTime(), 0));

      // test HTML report
      $report = $timer->createReport();
      $this->assertContains('<dt class="even">Root</dt>', $report);
      $this->assertContains('<dt class="odd">' . $name . '</dt>', $report);
      $this->assertContains('<dd class="even warn">1.', $report); // 1 sec duration

      // test plain text report
      $report = $timer->createReport(new PlainTextReport());
      $this->assertContains('Root 1.', $report);
      $this->assertContains('' . $name . ' 1.', $report);
   }

   public function testNotStartedProcess() {
      $this->expectException(InvalidArgumentException::class);
      $timer = new BenchmarkTimer();
      $timer->stop('Foo');
   }

}
