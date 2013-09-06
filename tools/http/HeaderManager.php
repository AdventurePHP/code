<?php
namespace APF\tools\http;

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

/**
 * @package tools::http
 * @class HeaderManager
 * @see http://forum.adventure-php-framework.org/viewtopic.php?p=243#p243
 * @see http://tracker.adventure-php-framework.org/view.php?id=72
 *
 * The HeaderManager implements a wrapper of PHP's header() function and let's
 * you easily forward or send generic headers.
 * <p/>
 * To allow easy Unit Test execution exit()'s used within the forward() and
 * redirect() methods can be switched of globally. In case you intend to not
 * stop script execution within a certain controller, you may pass an optional
 * argument to forward() or redirect().
 * <p />
 * Configuration of the code execution stopping feature can be influenced as
 * follows:
 * <pre>
 * self::$EXIT_AFTER_FORWARD | $exitAfterForward | result
 * true                      | true              | exit()
 * true                      | false             | -
 * false                     | true              | -
 * false                     | false             | -
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.10.2008<br />
 * Version 0.2, 27.08.2013 (Introduced exit after forward to allow easy Unit Test execution)<br />
 */
class HeaderManager {

   /**
    * @var bool True in case code execution is stopped after forward() or redirect(), false otherwise.
    */
   private static $EXIT_AFTER_FORWARD = true;

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Activates behaviour to stop code execution after forward() or redirect().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2013<br />
    */
   public static function activateExitAfterForward() {
      self::$EXIT_AFTER_FORWARD = true;
   }

   /**
    * @public
    * @static
    *
    * Deactivates behaviour to stop code execution after forward() or redirect().
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2013<br />
    */
   public static function deactivateExitAfterForward() {
      self::$EXIT_AFTER_FORWARD = false;
   }

   /**
    * @public
    * @static
    * @see http://www.faqs.org/rfcs/rfc2616 (section 10.3.4 303 See Other)
    *
    * Forwards to a given target.
    *
    * @param string $url The target URL.
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function forward($url, $exitAfterForward = true) {
      header('Location: ' . self::decodeUrl($url), 303);

      if (self::$EXIT_AFTER_FORWARD === true && $exitAfterForward === true) {
         exit(0);
      }

   }

   /**
    * @public
    * @static
    * @see http://www.faqs.org/rfcs/rfc2616 (sections 10.3.2 301 Moved Permanently and 10.3.3 302 Found)
    *
    * Redirects to a given target.
    *
    * @param string $url The target URL.
    * @param bool $permanent indicates, if the redirect is permanent (true) or not (false)
    * @param bool $exitAfterForward True in case code execution is stopped after this action, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function redirect($url, $permanent = false, $exitAfterForward = true) {
      header('Location: ' . self::decodeUrl($url), false, $permanent === true ? 301 : 302);

      if (self::$EXIT_AFTER_FORWARD === true && $exitAfterForward === true) {
         exit(0);
      }
   }

   /**
    * @public
    * @static
    * @see http://www.faqs.org/rfcs/rfc2616
    *
    * Sends a generic header.
    *
    * @param string $content The content of the header.
    * @param bool $replacePrevHeaders Indicates, if previous headers should be overwritten.
    * @param int|bool $httpStatus The HTTP status code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function send($content, $replacePrevHeaders = false, $httpStatus = false) {
      if ($httpStatus === false) {
         header($content, $replacePrevHeaders);
      } else {
         header($content, $replacePrevHeaders, $httpStatus);
      }
   }

   /**
    * @param string $url The URL possibly containing encoded ampersands.
    * @return string The decoded url.
    */
   private static function decodeUrl($url) {
      return str_replace('&amp;', '&', $url);
   }

}
