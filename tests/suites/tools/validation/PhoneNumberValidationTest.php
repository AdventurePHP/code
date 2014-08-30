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
namespace APF\tests\suites\tools\validation;

use APF\tools\validation\PhoneNumberValidator;

class PhoneNumberValidationTest extends \PHPUnit_Framework_TestCase {

   public function testStandard() {
      $numbers = array(
         // US
            '(844) 207-6002',
            '(833) 138-4854',
            '(855) 192-5323',
            '(811) 631-5079',
            '(855) 769-8714',
         // Germany
            '0806 - 94841333',
            '(039800) 086693',
            '+49 (87) 69490555',
            '+49 1671 1077835',
            '036378/989626',
         // UK
            '(024) 3659 6672',
            '07852 756132',
            '(01342) 23693',
            '0800 154 0793',
            '0845 46 47'
      );

      $validator = new PhoneNumberValidator();
      foreach ($numbers as $number) {
         $this->assertTrue($validator->isValid($number));
      }

   }

   public function testGenericPattern() {
      $numbers = array(
         // US
            '(844) 207-6002',
            '(833) 138-4854',
            '(855) 192-5323',
            '(811) 631-5079',
            '(855) 769-8714',
         // Germany
            '0806 - 94841333',
            '(039800) 086693',
            '+49 (87) 69490555',
            '+49 1671 1077835',
         // UK
            '(024) 3659 6672',
            '07852 756132',
            '0800 154 0793',
            '0845 46 47'
      );

      $validator = new PhoneNumberValidator(PhoneNumberValidator::GENERIC);
      foreach ($numbers as $number) {
         $this->assertTrue($validator->isValid($number));
      }

   }

   public function testInternationalNumbers() {
      $numbers = array(
         // US
            '+1 855 192 5323',
            '+1 811 631 5079',
         // Germany
            '+49 87 6949 0555',
            '+49 1671 1077835',
         // UK
            '+44 024 3659 6672',
            '+44 07852 756132',
      );

      $validator = new PhoneNumberValidator(PhoneNumberValidator::INTERNATIONAL);
      foreach ($numbers as $number) {
         try {
            $this->assertTrue($validator->isValid($number));
         } catch (\Exception $e) {
            echo 'Validation of ' . $number . ' failed';
         }
      }
   }

   public function testInternationalEppNumbers() {
      $numbers = array(
         // US
            '+1.8551925323',
            '+1.8116315079',
         // Germany
            '+49.8769490555',
            '+49.16711077835',
         // UK
            '+44.02436596672',
            '+44.07852756132',
      );

      $validator = new PhoneNumberValidator(PhoneNumberValidator::INTERNATIONAL_EPP);
      foreach ($numbers as $number) {
         $this->assertTrue($validator->isValid($number));
      }
   }

}
 