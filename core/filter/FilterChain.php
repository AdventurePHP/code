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
 * @class FilterDefinition
 *
 * Represents the description of an APF filter.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.12.2007<br />
 * Version 0.2, 14.12.2010 (Is now standalone class)<br />
 */
final class FilterDefinition {

   /**
    * @private
    * @var string The namespace of the filter class.
    */
   private $namespace = null;

   /**
    * @private
    * @var string The name of the filter class (and file name as well).
    */
   private $class = null;

   /**
    * @public
    *
    * Constructor of the filter description. Taks the namespace and the class as an argument.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.12.2007<br />
    */
   public function __construct($namespace, $class) {
      $this->namespace = $namespace;
      $this->class = $class;
   }

   public function getNamespace() {
      return $this->namespace;
   }

   public function getClass() {
      return $this->class;
   }

}

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
 * @class FilterFactory
 *
 * Implements a simple factory to load filter classes derived from the AbstractFilter class.
 * Each filter is described by the FilterDefinition class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 08.06.2007<br />
 */
final class FilterFactory {

   /**
    * @public
    * @static
    *
    * Returns an instance of the desired filter.
    *
    * @param FilterDefinition $filterDefinition the definition of the APF style filter.
    * @return AbstractFilter The instance of the filter or null in case the filter class does not exist.
    * @throws InvalidArgumentException In case of configuration errors for the applied filter definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.06.2007<br />
    * Version 0.2, 13.08.2008 (Removed unused code)<br />
    * Version 0.3, 07.11.2008 (Bugfix: the namespace of filters outside "core::filter" could not be included)<br />
    * Version 0.4, 11.12.2008 (Switched to FilterDefinition for addressing a filter)<br />
    */
   public static function getFilter(FilterDefinition $filterDefinition) {

      // gather the filter information
      $namespace = $filterDefinition->getNamespace();
      $filterName = $filterDefinition->getClass();

      // check, if the filter exists and include it
      try {
         import($namespace, $filterName);
         return new $filterName;
      } catch (IncludeException $ie) {
         throw new InvalidArgumentException('[FilterFactory::getFilter()] Requested filter "'
                 . $filterName . '" cannot be loaded from namespace "' . $namespace . '"!', E_USER_ERROR);
      }
   }

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
    * @param string $input The input to filter.
    * @return string The output of the filter chain
    */
   public function filter($input);

   /**
    * @param ChainedContentFilter $filter The filter implementation to add.
    */
   public function addFilter(ChainedContentFilter $filter);

   /**
    * @param string $class The class name of the filter to remove from the chain.
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

   public function reset() {
      $this->offset = 0;
   }

   public function isStarted() {
      return $this->offset > 0;
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