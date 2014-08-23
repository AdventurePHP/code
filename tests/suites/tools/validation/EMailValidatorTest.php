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
namespace APF\tools\validation;

class EMailValidatorTest extends \PHPUnit_Framework_TestCase {

   private $validList = array(
         'email@example.com',
         'firstname.lastname@example.com',
         'email@subdomain.example.com',
         '1234567890@example.com',
         'email@example-one.com',
         '_______@example.com',
         'email@example.name',
         'email@example.museum',
         'email@example.co.jp',
         'firstname-lastname@example.com'
   );

   private $specialList = array(
         'firstname+lastname@example.com',
         'email@123.123.123.123'
   );

   private $invalidList = array(
         '            plainaddress',
         '#@%^%#$@#$@#.com',
         '@example . com',
         'Joe Smith < email@example . com >',
         '            email . example . com',
         'email@example@example . com',
         '            . email@example . com',
         'email . @example . com',
         'email ..email@example . com',
         'あいうえお@example . com',
         'email@example . com(Joe Smith)',
         'email@example',
         'email@-example . com',
         'email@example . web',
         'email@111.222.333.44444',
         'email@example ..com',
         'Abc . .123@example . com'
   );

   public function testValidEMailAddresses() {
      $validator = new EMailValidator();
      foreach ($this->validList as $eMail) {
         if (!$validator->validate($eMail)) {
            $this->fail('Considered e-mail address "' . $eMail . '" as invalid though it is not!');
         }
      }

      $this->assertTrue(true);
   }

   public function testSpecialEMailAddresses() {
      $validator = new EMailValidator(EMailValidator::RULE_COMPLEX);
      foreach (array_merge($this->validList, $this->specialList) as $eMail) {
         if (!$validator->validate($eMail)) {
            $this->fail('Considered e-mail address "' . $eMail . '" as invalid though it is not!');
         }
      }

      $this->assertTrue(true);
   }

   public function testInvalidEMailAddresses() {
      $validator = new EMailValidator();
      foreach ($this->invalidList as $eMail) {
         if ($validator->validate($eMail)) {
            $this->fail('Considered e-mail address "' . $eMail . '" as valid though it is not!');
         }
      }

      $this->assertTrue(true);
   }

}
 