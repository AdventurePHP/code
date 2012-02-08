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
 * @package tools::soap
 * @class WsdlObjectMapping
 *
 * Implements a wrapper class for an WSDL-to-PHP object mapping that can be registered with the APFSoapClient in
 * order to transform WSDL types to PHP objects.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.02.2012<br />
 */
class WsdlObjectMapping {

   /**
    * @var string The name of the WSDL type.
    */
   private $wsdlType;

   /**
    * @var string The name of the PHP class.
    */
   private $phpClassName;

   /**
    * @param string $wsdlType The name of the WSDL type.
    * @param string $phpClassName The name of the PHP class.
    */
   public function __construct($wsdlType, $phpClassName) {
      $this->wsdlType = $wsdlType;
      $this->phpClassName = $phpClassName;
   }

   public function getPhpClassName() {
      return $this->phpClassName;
   }

   public function getWsdlType() {
      return $this->wsdlType;
   }

}
