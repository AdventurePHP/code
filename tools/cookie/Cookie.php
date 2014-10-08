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
namespace APF\tools\cookie;

use APF\core\http\Cookie as CoreCookie;
use APF\core\http\mixins\GetRequestResponseTrait;

/**
 * Compatibility class maintain pre-3.0 state of cookie handling.
 *
 * @deprecated Use APF\core\http\Cookie instead.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.10.2014<br />
 */
class Cookie extends CoreCookie {

   use GetRequestResponseTrait;

   /**
    * @return bool True in case the operation has been successful, false otherwise.
    */
   public function delete() {
      self::getResponse()->setCookie(parent::delete());

      return true;
   }

   /**
    * @param string $value The value of the cookie.
    *
    * @return bool True, if cookie was set correctly, false, if something was wrong.
    */
   public function setValue($value) {
      self::getResponse()->setCookie(parent::setValue($value));

      return true;
   }

}
