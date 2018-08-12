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
namespace APF\modules\usermanagement\biz\provider;

use APF\modules\usermanagement\biz\model\UmgtUser;

/**
 * Provider for encrypting user data
 *
 * @author Ralf Schubert
 * @version
 * Version 1.0, 24.06.2013<br />
 * Version 1.1, 12.08.2018 (ID#336: migrated from mcrypt to OpenSSL)<br />
 */
class UserFieldEncryptionProvider {

   /**
    * @var array List of field names to be encrypted.
    */
   public static $encryptedFieldNames = null;

   /**
    * @var string Encryption key to be configured by application.
    */
   public static $encryptionConfigKey = null;

   /**
    * @var string Cipher method. Can be defined from the list of i.e. openssl_get_cipher_methods()
    */
   public static $cipherMethod = 'AES-128-CBC';

   /**
    * @const string Hard-coded encryption key to generate initialization vector.
    */
   const HARD_CODED_ENCRYPTION_KEY = 'sjhdjhaDSAHKHSLdäASÖdo75&$/6923598(&)(3k;;';

   /**
    * Encrypts the given value.
    *
    * @param string $string Plain value which should be encrypted.
    * @return string Encrypted value.
    */
   public static function encrypt($string) {

      if (empty($string)) {
         return '';
      }

      return self::encryptOpenSSL($string);
   }

   /**
    * Decrypts the given encrypted value.
    *
    * @param string $string Encrypted value which should be decrypted.
    * @return string The decrypted plain value.
    */
   public static function decrypt($string) {

      if (empty($string)) {
         return '';
      }

      return self::decryptOpenSSL($string);
   }

   /**
    * Checks whether the given property is configured to be encrypted
    *
    * @param String $propertyName
    *
    * @return boolean Returns true if property has encryption enabled
    */
   public static function propertyHasEncryptionEnabled($propertyName) {
      if (self::$encryptedFieldNames === null) {
         return false;
      }

      return in_array($propertyName, self::$encryptedFieldNames);
   }

   /**
    * Encrypts all properties of the given user which have encryption enabled
    *
    * @param UmgtUser $user
    */
   public static function encryptProperties(UmgtUser $user) {
      if (self::$encryptedFieldNames === null) {
         return;
      }
      $properties = $user->getProperties();
      foreach ($properties as $key => $value) {
         if (self::propertyHasEncryptionEnabled($key)) {
            $user->setProperty($key, self::encrypt($value));
         }
      }
   }

   /**
    * Decrypts all properties of the given user which have encryption enabled
    *
    * @param UmgtUser $user
    */
   public static function decryptProperties(UmgtUser $user) {
      if (self::$encryptedFieldNames === null) {
         return;
      }

      $properties = $user->getProperties();
      foreach ($properties as $key => $value) {
         if (self::propertyHasEncryptionEnabled($key)) {
            $user->setProperty($key, self::decrypt($value));
         }
      }
   }

   /**
    * Generates an initialization vector (IV) for encryption and decryption.
    *
    * @param int $length Desired length of the initialization vector (IV).
    * @return string The initialization vector (IV).
    */
   protected static function generateInitializationVector($length) {
      $iv = self::$encryptionConfigKey . self::HARD_CODED_ENCRYPTION_KEY;
      while (strlen($iv) < $length) {
         $iv .= $iv;
      }
      return substr($iv, 0, $length);
   }

   protected static function encryptOpenSSL($value) {

      $length = openssl_cipher_iv_length(self::$cipherMethod);
      $iV = self::generateInitializationVector($length);

      return base64_encode(
            openssl_encrypt(
                  $value,
                  self::$cipherMethod,
                  self::$encryptionConfigKey,
                  OPENSSL_RAW_DATA,
                  $iV
            )
      );
   }

   protected static function decryptOpenSSL($value) {

      $length = openssl_cipher_iv_length(self::$cipherMethod);
      $iV = self::generateInitializationVector($length);

      return openssl_decrypt(
            base64_decode($value),
            self::$cipherMethod,
            self::$encryptionConfigKey,
            OPENSSL_RAW_DATA,
            $iV
      );
   }

}
