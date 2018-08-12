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
namespace APF\tests\suites\modules\usermanagement\biz\provider;

use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\biz\provider\UserFieldEncryptionProvider;
use PHPUnit\Framework\TestCase;

class UserFieldEncryptionProviderTest extends TestCase {

   public static function setUpBeforeClass() {
      ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
   }

   public static function tearDownAfterClass() {
      ini_set('error_reporting', E_ALL);
   }

   public function testEncrypt() {
      $this->assertEquals('Q7N1fzCJIAczFsyzG+Mz5w==', UserFieldEncryptionProvider::encrypt('foo'));
      $this->assertEquals('', UserFieldEncryptionProvider::encrypt(''));
   }

   public function testDecrypt() {
      $this->assertEquals('foo', UserFieldEncryptionProvider::decrypt('Q7N1fzCJIAczFsyzG+Mz5w=='));
      $this->assertEquals('', UserFieldEncryptionProvider::decrypt(''));
   }

   public function testPropertyHasEncryptionEnabled() {
      $this->assertFalse(UserFieldEncryptionProvider::propertyHasEncryptionEnabled('foo'));

      UserFieldEncryptionProvider::$encryptedFieldNames = ['Username'];
      $this->assertFalse(UserFieldEncryptionProvider::propertyHasEncryptionEnabled('FirstName'));
      $this->assertTrue(UserFieldEncryptionProvider::propertyHasEncryptionEnabled('Username'));

      UserFieldEncryptionProvider::$encryptedFieldNames = null;
   }

   public function testEncryptProperties1() {

      $user = new UmgtUser();

      $firstName = 'First name';
      $userName = 'User name';
      $user->setFirstName($firstName);
      $user->setUsername($userName);
      UserFieldEncryptionProvider::$encryptedFieldNames = ['Username'];
      UserFieldEncryptionProvider::encryptProperties($user);

      $this->assertEquals($firstName, $user->getFirstName());
      $this->assertNotEquals($userName, $user->getUsername());
      $this->assertEquals('8Xx3rHFKdWCnAwUrV0rq9A==', $user->getUsername());

      UserFieldEncryptionProvider::$encryptedFieldNames = null;

   }

   public function testEncryptProperties2() {

      $user = new UmgtUser();

      $firstName = 'First name';
      $userName = 'User name';
      $user->setFirstName($firstName);
      $user->setUsername($userName);
      UserFieldEncryptionProvider::$encryptedFieldNames = null;
      UserFieldEncryptionProvider::encryptProperties($user);

      $this->assertEquals($firstName, $user->getFirstName());
      $this->assertEquals($userName, $user->getUsername());

      UserFieldEncryptionProvider::$encryptedFieldNames = null;

   }

   public function testDecryptProperties1() {

      $user = new UmgtUser();

      $firstName = 'First name';
      $userName = '8Xx3rHFKdWCnAwUrV0rq9A==';
      $user->setFirstName($firstName);
      $user->setUsername($userName);
      UserFieldEncryptionProvider::$encryptedFieldNames = ['Username'];
      UserFieldEncryptionProvider::decryptProperties($user);

      $this->assertEquals($firstName, $user->getFirstName());
      $this->assertNotEquals($userName, $user->getUsername());
      $this->assertEquals('User name', $user->getUsername());

      UserFieldEncryptionProvider::$encryptedFieldNames = null;
   }

   public function testDecryptProperties2() {

      $user = new UmgtUser();

      $firstName = 'First name';
      $userName = 's0FvKrPjZPkY1mXKhU2tHQ==';
      $user->setFirstName($firstName);
      $user->setUsername($userName);
      UserFieldEncryptionProvider::$encryptedFieldNames = null;
      UserFieldEncryptionProvider::decryptProperties($user);

      $this->assertEquals($firstName, $user->getFirstName());
      $this->assertEquals($userName, $user->getUsername());

      UserFieldEncryptionProvider::$encryptedFieldNames = null;
   }

}
