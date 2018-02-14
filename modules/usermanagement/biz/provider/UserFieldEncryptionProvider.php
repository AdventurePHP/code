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
 */
class UserFieldEncryptionProvider {

   public static $encryptedFieldNames = null;
   public static $encryptionConfigKey = null;
   protected static $encryptionHardCodedKey = 'sjhdjhaDSAHKHSLdäASÖdo75&$/6923598(&)(3k;;';
   protected static $encryptionConcatenatedKey = null;
   protected static $encryptionIV = null;


   /**
    * Concatenates all encryption key parts to the final key with correct length.
    * If key is smaller then all key parts together, a new key will be generated,
    * containing parts of each single key part.
    *
    * @param string $handler The mcrypt handler.
    *
    * @return String The final encryption/decryption key
    */
   protected static function getConcatenatedEncryptionKey($handler) {
      if (self::$encryptionConcatenatedKey === null) {
         $key = '';
         $size = mcrypt_enc_get_key_size($handler);
         $currentSize = 0;

         $sizeHardcoded = strlen(self::$encryptionHardCodedKey);
         $sizeConfig = strlen(self::$encryptionConfigKey);

         if ($size >= ($sizeHardcoded + $sizeConfig)) {
            $key = self::$encryptionHardCodedKey . self::$encryptionConfigKey;
            while ($size > strlen($key)) {
               $key .= $key;
            }
            if ($size < strlen($key)) {
               $key = substr($key, 0, $size);
            }
         } else {

            $posHardcoded = 0;
            $posConfig = 0;

            while ($currentSize < $size) {
               if (($posHardcoded + 1) < $sizeHardcoded) {
                  $key .= substr(self::$encryptionHardCodedKey, $posHardcoded, 1);
                  $posHardcoded++;
                  $currentSize++;
               }
               if ((($posConfig + 1) < $sizeConfig) && ($currentSize < $size)) {
                  $key .= substr(self::$encryptionConfigKey, $posConfig, 1);
                  $posConfig++;
                  $currentSize++;
               }
            }
         }

         self::$encryptionConcatenatedKey = $key;
      }

      return self::$encryptionConcatenatedKey;
   }

   /**
    * Returns encryption handler and creates an IV (or uses cached one)
    * For the IV the same parts are used as for the encryption key, but they are concatenated differently.
    *
    * @return type
    */
   public static function getEncryptionHandler() {
      $td = mcrypt_module_open('tripledes', '', 'ecb', '');
      if (self::$encryptionIV === null) {
         $ivSize = mcrypt_enc_get_iv_size($td);
         $iv = self::$encryptionConfigKey . self::$encryptionHardCodedKey;
         while (strlen($iv) < $ivSize) {
            $iv .= $iv;
         }
         $iv = substr($iv, 0, $ivSize);
         self::$encryptionIV = $iv;
      }

      return $td;
   }

   /**
    * Closes the encryption handler
    *
    * @param type $handler
    */
   protected static function closeEncryptionhandler($handler) {
      mcrypt_module_close($handler);
      self::$encryptionIV = null;
   }

   /**
    * Encrypts the given value.
    * If encryption handler is provided it will be used. Otherwise
    * one will be generated.
    *
    * @param String $value Plain value which should be encrypted
    * @param type $encryptionHandler
    *
    * @return String encrypted value
    */
   public static function encrypt($value, $encryptionHandler = null) {

      if (empty($value)) {
         return $value;
      }

      $closeHandler = false;
      if ($encryptionHandler === null) {
         $encryptionHandler = self::getEncryptionHandler();
         $closeHandler = true;
      }

      mcrypt_generic_init($encryptionHandler, self::getConcatenatedEncryptionKey($encryptionHandler), self::$encryptionIV);
      $crypted = base64_encode(mcrypt_generic($encryptionHandler, $value));
      mcrypt_generic_deinit($encryptionHandler);

      if ($closeHandler) {
         self::closeEncryptionhandler($encryptionHandler);
      }

      return $crypted;
   }

   /**
    * Decrypts the given encrypted value
    *
    * @param String $crypted Encrypted value which should be decrypted.
    * @param type $encryptionHandler
    *
    * @return String The decrypted plain value
    */
   public static function decrypt($crypted, $encryptionHandler = null) {

      if (empty($crypted)) {
         return $crypted;
      }

      $closeHandler = false;
      if ($encryptionHandler === null) {
         $encryptionHandler = self::getEncryptionHandler();
         $closeHandler = true;
      }

      mcrypt_generic_init($encryptionHandler, self::getConcatenatedEncryptionKey($encryptionHandler), self::$encryptionIV);
      $plain = mdecrypt_generic($encryptionHandler, base64_decode($crypted));
      mcrypt_generic_deinit($encryptionHandler);

      if ($closeHandler) {
         self::closeEncryptionhandler($encryptionHandler);
      }
      // the trim is needed, because sometimes there appear some
      // invisible characters which lead to string comparison fails
      return trim($plain);
   }

   /**
    * Checks wether the given property is configured to be encrypted
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
      $encryptionHandler = self::getEncryptionHandler();
      $properties = $user->getProperties();
      foreach ($properties as $key => $value) {
         if (self::propertyHasEncryptionEnabled($key)) {
            $user->setProperty($key, self::encrypt($value, $encryptionHandler));
         }
      }
      self::closeEncryptionhandler($encryptionHandler);
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

      $encryptionHandler = self::getEncryptionHandler();
      $properties = $user->getProperties();
      foreach ($properties as $key => $value) {
         if (self::propertyHasEncryptionEnabled($key)) {
            $user->setProperty($key, self::decrypt($value, $encryptionHandler));
         }
      }
      self::closeEncryptionhandler($encryptionHandler);
   }

}
