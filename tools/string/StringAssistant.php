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
namespace APF\tools\string;

use APF\core\registry\Registry;

/**
 * Provides methods for string manipulation and generation.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 12.02.2006<br />
 */
class StringAssistant {

   private function __construct() {
      // utility class
   }

   /**
    * Escapes special characters with respect to the php.ini settings.
    *
    * @param string $string The string to escape the special characters within.
    *
    * @return string The escaped string.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 11.01.2005<br />
    */
   public static function escapeSpecialCharacters(string $string) {
      return addslashes(htmlspecialchars($string, ENT_QUOTES, Registry::retrieve('APF\core', 'Charset'), false));
   }

   /**
    * Encodes a given string to html entities.
    *
    * @param string $string The string to encode.
    *
    * @return string The html entity encoded string.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.06.2007<br />
    */
   public static function encodeCharactersToHTML(string $string) {

      $content = trim($string);

      $encodedContent = (string)'';

      for ($i = 0; $i < strlen($content); $i++) {
         $encodedContent .= '&#' . ord($content[$i]) . ';';
      }

      return $encodedContent;

   }

   /**
    * Generates a string that can be used as captcha competition.
    *
    * @param int $length Length of the string.
    *
    * @return string Captcha string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2007<br />
    */
   public static function generateCaptchaString(int $length) {

      // shuffles random numbers
      srand(StringAssistant::generateSeed());

      $characterBase = 'ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';

      $string = (string)'';

      while (strlen($string) < $length) {
         $string .= substr($characterBase, (rand() % (strlen($characterBase))), 1);
      }

      return $string;
   }

   /**
    * Generates a random start number for the srand() function.
    *
    * @return int Random start value for the srand() function.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2007<br />
    */
   protected static function generateSeed() {
      list($usec, $sec) = explode(' ', microtime());

      return (float)$sec + ((float)$usec * 100000);
   }

}
