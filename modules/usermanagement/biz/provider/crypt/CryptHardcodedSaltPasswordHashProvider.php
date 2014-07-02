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
namespace APF\modules\usermanagement\biz\provider\crypt;

use APF\modules\usermanagement\biz\provider\DefaultPasswordHashProvider;

/**
 * This is the default PasswordHashProvider of the usermanagement
 *
 * @author Tobias Lückel
 * @version
 * Version 0.1, 04.04.2011<br />
 */
class CryptHardcodedSaltPasswordHashProvider extends DefaultPasswordHashProvider {

   public function createPasswordHash($password, $dynamicSalt) {
      // Added $2a$07$ at the beginning of the salt and $ at the end, so that blowfish is used
      return crypt($password . $this->getHardCodedSalt(), '$2a$07$' . $dynamicSalt . '$');
   }

}
