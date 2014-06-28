<?php
namespace APF\core\filter;

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
use APF\core\filter\AbstractFilterChain;

/**
 * Represents the singleton instance of the input filter chain.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
class InputFilterChain extends AbstractFilterChain {

   /**
    * @var InputFilterChain $CHAIN
    */
   private static $CHAIN;

   private function __construct() {
   }

   /**
    * @return InputFilterChain The instance of the current input filter chain.
    */
   public static function &getInstance() {
      if (self::$CHAIN === null) {
         self::$CHAIN = new InputFilterChain();
      }

      return self::$CHAIN;
   }

}
