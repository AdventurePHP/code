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
import('tools::form::provider::csrf', 'CSRFHashProvider');

/**
 * @package tools::form::provider::csrf
 * @class EncyptedSIDHashProvider
 *
 * Generates a hash based on the user's SID.
 *
 * @author Daniel Seemaier
 * @version
 * Version 0.1, 29.10.2010
 */
class EncryptedSIDHashProvider extends APFObject implements CSRFHashProvider {

   /**
    * @public
    *
    * Generates a SID based hash.
    *
    * @param string $salt The salt.
    * @return string The SID based hash.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 29.10.2010
    */
   public function generateHash($salt) {
      if (!defined('SID')) {
         session_start();
      }

      return md5($salt . SID);
   }

}
