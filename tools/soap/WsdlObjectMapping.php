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
 * <p/>
 * May be used within a code block to explicitly add an object mapping or as a DI service that is injected
 * into the ExtendedSoapClientService.
 *
 * @example
 * <code>
 * $mapping = new WsdlObjectMapping();
 * $client->registerWsdlObjectMapping(
 *    $mapping->setWsdlType('login-response')
 *       ->setPhpClassNamespace('sample::namespace')
 *       ->setPhpClassName('LoginResponse')
 *);
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.02.2012<br />
 */
class WsdlObjectMapping extends APFObject {

   /**
    * @var string The name of the WSDL type.
    */
   private $wsdlType;

   /**
    * @var string The namespace of the PHP class.
    */
   private $phpClassNamespace;

   /**
    * @var string The name of the PHP class.
    */
   private $phpClassName;

   /**
    * @param string $phpClassName The name of the PHP class.
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
    * @param string $phpClassNamespace The namespace of the PHP class.
    * @return WsdlObjectMapping This object for further usage.
    */
   public function setPhpClassNamespace($phpClassNamespace) {
      $this->phpClassNamespace = $phpClassNamespace;
      return $this;
   }

   public function getPhpClassNamespace() {
      return $this->phpClassNamespace;
   }

   /**
    * @param string $wsdlType The name of the WSDL type.
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
