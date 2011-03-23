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
 * @package core::filter
 * @class AbstractFilter
 * @abstract
 *
 * Abstract filter class.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 08.06.2007<br />
 */
abstract class AbstractFilter extends APFObject {

   /**
    * @public
    * @abstract
    *
    * Abstract filter methode. Must be implemented by concrete filter implementations.
    *
    * @param string $input the input of the filter.
    * @return string The output of the filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.06.2007<br />
    * Version 0.2, 08.12.2008 (Added the $filterInstruction argument)<br />
    * Version 0.3, 18.07.2009 (Removed the $filterInstruction argument and refactored the filters)<br />
    */
   abstract function filter($input);
}

/**
 * @package core::filter
 * @class FilterChain
 *
 * Defines the structure of an APF filter chain used for input and output
 * filter chains.
 * <p/>
 * A filter chain is a list of filters that is executed step-by-step to
 * filter the input (request) or the output (html content) to transform
 * to an internal (input) or external (output) representation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
interface FilterChain {

   /**
    * @public
    *
    * Executes the filter chain including all registered filters. The given
    * argument takes the input that is to be filtered and the return value is
    * the result of the filter chain execution.
    * <p/>
    * To register custom filters, use the <em>addFilter()</em> method.
    *
    * @param string $input The input to filter.
    * @return string The output of the filter chain
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function filter($input);

   /**
    * @public
    *
    * Let's you add a filter to the chain. Please note, that the execution 
    * order corresponds to the order the filters are added.
    *
    * @param ChainedContentFilter $filter The filter implementation to add.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function addFilter(ChainedContentFilter $filter);

   /**
    * @public
    *
    * This method can be used to remove a filter from the chain. The filter
    * to remove is addressed by it's implementation class' name.
    *
    * @param string $class The class name of the filter to remove from the chain.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function removeFilter($class);
}

/**
 * @package core::filter
 * @class ChainedContentFilter
 *
 * Defines the structure of an APF filter that is executed with a
 * filter chain.
 * <p/>
 * Each filter is applied the chain to be able to handle the filters
 * that are registered there (e.g. stop or remain chain execution).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
interface ChainedContentFilter {

   /**
    * @public
    *
    * This method must be implemented by each single chained filter to
    * influence the result of the filter chain execution.
    *
    * @param FilterChain $chain The instance of the current filter chain.
    * @param string $input The current input to filter.
    * @return The result of the filter execution.
    */
   function filter(FilterChain &$chain, $input = null);
}

/**
 * @package core::filter
 * @class AbstractFilterChain
 * @abstract
 *
 * Implements the basic functionality of an input and output filter chain.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 */
abstract class AbstractFilterChain implements FilterChain {

   /**
    * @var ChainedContentFilter[] The filter stack.
    */
   private $filters = array();

   /**
    * @var int Marks the offset of the filter that is currently executed.
    */
   private $offset = 0;

   /**
    * @var int The number of filters on the stack.
    */
   private $count = 0;

   public function filter($input) {
      return $this->offset < $this->count ? $this->filters[$this->offset++]->filter($this, $input) : $input;
   }

   public function addFilter(ChainedContentFilter $filter) {
      $this->filters[] = $filter;
      $this->count++;
   }

   public function removeFilter($class) {

      // since it is possible to remove a filter during chaing execution,
      // start at the current offset to disallow removal of filters that
      // have already been executed.
      for ($i = $this->offset; $i < $this->count; $i++) {
         if ($this->filters[$i] instanceof $class) {
            array_splice($this->filters, $i, 1);
            $this->count--;
         }
      }
   }

   /**
    * @public
    *
    * Resets the filter chain to the first filter to be executed again.
    *
    * @return AbstractFilterChain The current filter chain for further configuration.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function &reset() {
      $this->offset = 0;
      return $this;
   }

   /**
    * @public
    *
    * This method clears the filter chain and resets it's state.
    * <p/>
    * Please use this method in case you want to re-order the registered
    * filters to fit your custom requirements. After clearing the chain
    * you can re-add the desired filters by using the <em>addFilter()</em>
    * method.
    *
    * @return AbstractFilterChain The current filter chain for further configuration.
    * 
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.03.2011<br />
    */
   public function &clear() {
      $this->filters = array();
      $this->count = 0;
      $this->offset = 0;
      return $this;
   }

}

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
?>