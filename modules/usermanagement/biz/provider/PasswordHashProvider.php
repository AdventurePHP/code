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

/**
 * This interface defines the structure of a password hash provider
 * used for the usermanagement manager.
 *
 * @author Christian Achatz, Tobias Lückel
 * @version
 * Version 0.1, 12.10.2009<br />
 * Version 0.2, 04.04.2011 (Adapted from usermanagement)<br />
 */
interface PasswordHashProvider {

   /**
    * Creates a password hash and returns it. This PasswordHashProvider is used
    * for hashed passwords before version 1.14
    *
    * @param string $password The password to create a hash from.
    * @param string $dynamicSalt Dynamic salt for the hash algorithm.
    *
    * @return string The password hash.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public function createPasswordHash($password, $dynamicSalt);

}
