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

/**
 * @package tools::http
 * @class HeaderManager
 * @see http://forum.adventure-php-framework.org/de/viewtopic.php?p=243#p243
 *
 *  The HeaderManager implements a wrapper on PHP's header() function and let's
 *  you easily forward, relocate or send generic headers.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.10.2008<br />
 */
class HeaderManager {

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    *  Forwards to a given target.
    *
    * @param string $targetURL the target URL
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function forward($targetURL) {
      header('Location: ' . str_replace('&amp;', '&', $targetURL));
   }

   /**
    * @public
    * @static
    * @see http://www.faqs.org/rfcs/rfc2616
    *
    *  Redirects to a given target.
    *
    * @param string $targetURL the target URL
    * @param bool $permanent indicates, if the redirect is permanent (true) or not (false)
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function redirect($targetURL, $permanent = false) {
      $statusCode = $permanent === true ? 301 : 302;
      header('Location: ' . str_replace('&amp;', '&', $targetURL), false, $statusCode);
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
    * @param integer $httpStatus The HTTP status code.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.10.2008<br />
    */
   public static function send($content, $replacePrevHeaders = false, $httpStatus = null) {

      if ($httpStatus === false) {
         header($content, $replacePrevHeaders);
      } else {
         header($content, $replacePrevHeaders, $httpStatus);
      }

   }

}
