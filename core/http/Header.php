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
namespace APF\core\http;

use InvalidArgumentException;

/**
 * Defines a generic HTTP header.
 * <p/>
 * Constants <em>HEADER_*</em> define common HTTP header names used in your application. In most cases there
 * is a convenience method defined allowing you to set the header without knowing about the internal structure.
 */
interface Header {

   const CONTENT_TYPE = 'Content-Type';
   const COOKIE = 'Cookie';
   const SET_COOKIE = 'Set-Cookie';

   /**
    * Static factory method to create an instance of an HTTP header from a raw string.
    *
    * @param string $string The raw HTTP header string.
    *
    * @return Header An instance of this interface.
    *
    * @throws InvalidArgumentException In case of an invalid format given.
    */
   public static function fromString($string);

   /**
    * Creates a HTTP header.
    *
    * @param string $name The name of the header.
    * @param string $value The value of the header.
    */
   public function __construct($name, $value);

   /**
    * @return string The name of the header.
    */
   public function getName();

   /**
    * @return string The value of the header.
    */
   public function getValue();

   /**
    * @return string The string representation of an HTTP header.
    */
   public function __toString();

} 