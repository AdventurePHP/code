<?php
namespace APF\core\filter;

use APF\core\filter\AbstractFilterChain;

/**
 * @package core::filter
 * @class InputFilterChain
 *
 * Represents the singleton instance of the input filter chain.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
class InputFilterChain extends AbstractFilterChain {

   /**
    * @var InputFilterChain
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
