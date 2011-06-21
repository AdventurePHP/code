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
    import('modules::usermanagement::biz::provider', 'DefaultPasswordHashProvider');
   /**
    * This is the old PasswordHashProvider which was used before 1.14
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 04.04.2011<br />
    */
    class OldMD5PasswordHashProvider extends DefaultPasswordHashProvider {

       /**
        * Creates a password hash and returns it. This PasswordHashProvider is used
        * for hashed passwords before version 1.14
        *
        * @param string $password The password to creata a hash from.
        * @param string $dynamicSalt Dynamic salt for the hash algo.
        * @return string The password hash.
        *
        * @author Tobias Lückel
        * @version
        * Version 0.1, 04.04.2011<br />
        */
        public function createPasswordHash($password, $dynamicSalt) {
            return md5($password);
        }

    //end class
    }
?>
