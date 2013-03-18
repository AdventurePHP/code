<?php
namespace APF\tools\soap;

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
 * @package tools::soap
 * @class SoapHttpBaseAuthCredentials
 *
 * Represents the HTTP BASE AUTH credentials that can be applied to the ExtendedSoapClientService.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.05.2012<br />
 */
class SoapHttpBaseAuthCredentials extends APFObject {

   /**
    * @var string The HTTP BASE AUTH user name.
    */
   private $username;

   /**
    * @var string The HTTP BASE AUTH password.
    */
   private $password;

   /**
    * Let's you construct HTTP BASE AUTH credential representation that can be
    * injected into the ExtendedSoapClientService by the setHttpAuthCredentials() method.
    *
    * @param string $username The HTTP BASE AUTH user name.
    * @param string $password The HTTP BASE AUTH password.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.05.2012<br />
    */
   public function __construct($username = null, $password = null) {
      $this->username = $username;
      $this->password = $password;
   }

   /**
    * @param string $password The HTTP BASE AUTH password.
    */
   public function setPassword($password) {
      $this->password = $password;
   }

   /**
    * @return string The HTTP BASE AUTH password.
    */
   public function getPassword() {
      return $this->password;
   }

   /**
    * @param string $username The HTTP BASE AUTH user name.
    */
   public function setUsername($username) {
      $this->username = $username;
   }

   /**
    * @return string The HTTP BASE AUTH user name.
    */
   public function getUsername() {
      return $this->username;
   }

}
