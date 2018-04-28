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
namespace APF\extensions\htmlheader\biz\filter;

use APF\core\filter\AbstractFilterChain;

/**
 * This FilterChain allows it to use special output filters for js and css
 * files. The files will be parsed from every applied filter before delivering
 * to the user.
 *
 * @author Ralf Schubert <<a href="http://develovision.de">Develovision Webentwicklung</a>>
 * @version 0.1,  25.07.2011<br />
 */
class JsCssInclusionFilterChain extends AbstractFilterChain {

   /**
    * @var JsCssInclusionFilterChain $CHAIN
    */
   private static $CHAIN;

   private function __construct() {
   }

   /**
    * @return JsCssInclusionFilterChain The instance of the current jscssinclusion filter chain.
    */
   public static function getInstance() {
      if (self::$CHAIN === null) {
         self::$CHAIN = new JsCssInclusionFilterChain();
      }

      return self::$CHAIN;
   }

}
