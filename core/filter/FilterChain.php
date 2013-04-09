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
use APF\core\pagecontroller\APFObject;

/**
 * @package APF\core\filter
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
    * Abstract filter method. Must be implemented by concrete filter implementations.
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
   abstract public function filter($input);
}

/**
 * @package APF\core\filter
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
    * Let's you add a filter to the end of the chain. Please note, that
    * the execution order corresponds to the order the filters are added.
    *
    * @param ChainedContentFilter $filter The filter implementation to add.
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function &appendFilter(ChainedContentFilter $filter);

   /**
    * @public
    *
    * Let's you add a filter to the beginning of the chain. Please note, that
    * the execution order corresponds to the order the filters are added.
    *
    * @param ChainedContentFilter $filter The filter implementation to add.
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function &prependFilter(ChainedContentFilter $filter);

   /**
    * @public
    *
    * This method can be used to remove a filter from the chain. The filter
    * to remove is addressed by it's implementation class' name.
    *
    * @param string $class The class name of the filter to remove from the chain.
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function &removeFilter($class);
}

/**
 * @package APF\core\filter
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
    * @return string The result of the filter execution.
    */
   public function filter(FilterChain &$chain, $input = null);
}
