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
namespace APF\core\http;

use InvalidArgumentException;

class HeaderImpl implements Header {

   /**
    * @const string Separator between the header name and value creating the string representation of the header.
    */
   const SEPARATOR = ':';

   /**
    * @var string The name of this header instance (e.g. Content-Type).
    */
   protected $name;

   /**
    * @var string The value of this header instance (e.g. text/html; charset=utf-8).
    */
   protected $value;

   public static function fromString(string $string) {
      $parts = explode(self::SEPARATOR, $string);
      if (count($parts) != 2) {
         throw new InvalidArgumentException('Construction of HTTP header from string "' . $string
               . '" failed due to invalid format!');
      }

      return new HeaderImpl(trim($parts[0]), trim($parts[1]));
   }

   public function __construct(string $name, string $value) {
      $this->name = $name;
      $this->value = $value;
   }

   public function getName() {
      return $this->name;
   }

   public function getValue() {
      return $this->value;
   }

   public function __toString() {
      return $this->name . self::SEPARATOR . ' ' . $this->value;
   }

}
