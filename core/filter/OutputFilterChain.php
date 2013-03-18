<?php
namespace APF\core\filter;

use APF\core\filter\AbstractFilterChain;

/**
 * @package core::filter
 * @class OutputFilterChain
 *
 * Represents the singleton instance of the output filter chain.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
class OutputFilterChain extends AbstractFilterChain {

   /**
    * @var OutputFilterChain
    */
   private static $CHAIN;

   private function __construct() {
   }

   /**
    * @return OutputFilterChain The instance of the current output filter chain.
    */
   public static function &getInstance() {
      if (self::$CHAIN === null) {
         self::$CHAIN = new OutputFilterChain();
      }
      return self::$CHAIN;
   }

}