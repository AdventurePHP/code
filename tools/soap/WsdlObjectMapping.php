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
namespace APF\tools\soap;

use APF\core\pagecontroller\APFObject;

/**
 * Implements a wrapper class for an WSDL-to-PHP object mapping that can be registered with the APFSoapClient in
 * order to transform WSDL types to PHP objects.
 * <p/>
 * May be used within a code block to explicitly add an object mapping or as a DI service that is injected
 * into the ExtendedSoapClientService. For an example, please have a look at the sample config EXAMPLE_serviceobjects.ini under
 * namespace tools::soap.
 *
 * @example
 * <code>
 * // explicit setter call
 * $mapping = new WsdlObjectMapping();
 * $client->registerWsdlObjectMapping(
 *    $mapping->setWsdlType('login-response')
 *       ->setPhpClassName('VENDOR\sample\namespace\LoginResponse')
 *);
 *
 * // constructor usage
 * $mapping = new WsdlObjectMapping(
 *    'login-response',
 *    'VENDOR\sample\namespace\LoginResponse'
 * );
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.02.2012<br />
 */
class WsdlObjectMapping extends APFObject {

   /**
    * The name of the WSDL type.
    *
    * @var string $wsdlType
    */
   private $wsdlType;

   /**
    * The name of the PHP class.
    *
    * @var string $phpClassName
    */
   private $phpClassName;

   /**
    * Let's you construct a WSDL object mapping to be injected into the
    * ExtendedSoapClientService by the registerWsdlObjectMapping() method.
    *
    * @param string $wsdlType The name of the WSDL type.
    * @param string $phpClassName The fully qualified name of the PHP class.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.02.2012<br />
    */
   public function __construct($wsdlType = null, $phpClassName = null) {
      $this->wsdlType = $wsdlType;
      $this->phpClassName = $phpClassName;
   }

   /**
    * @param string $phpClassName The fully qualified name of the PHP class.
    *
    * @return WsdlObjectMapping This object for further usage.
    */
   public function setPhpClassName($phpClassName) {
      $this->phpClassName = $phpClassName;

      return $this;
   }

   public function getPhpClassName() {
      return $this->phpClassName;
   }

   /**
    * @param string $wsdlType The name of the WSDL type.
    *
    * @return WsdlObjectMapping This object for further usage.
    */
   public function setWsdlType($wsdlType) {
      $this->wsdlType = $wsdlType;

      return $this;
   }

   public function getWsdlType() {
      return $this->wsdlType;
   }

}
