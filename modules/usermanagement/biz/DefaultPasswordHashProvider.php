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
    
   import('modules::usermanagement::biz','PasswordHashProvider');

   /**
    * @package modules::usermanagement::biz
    * @class DefaultPasswordHashProvider
    *
    * Implements the default password hash provider for the UmgtManager.
    * See interface description for details on the functionility.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.10.2009<br />
    */
   class DefaultPasswordHashProvider implements PasswordHashProvider {

      /**
       * Creates a password hash and returns it.
       *
       * @param string $password The password to creata a hash from.
       * @return string The password hash.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.10.2009<br />
       */
      public function createPasswordHash($password){
         return md5($password);
       // end function
      }

    // end class
   }
?>