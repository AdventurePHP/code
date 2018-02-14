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
namespace APF\core\filter;

/**
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
    * Executes the filter chain including all registered filters. The given
    * argument takes the input that is to be filtered and the return value is
    * the result of the filter chain execution.
    * <p/>
    * To register custom filters, use the <em>addFilter()</em> method.
    *
    * @param string $input The input to filter.
    *
    * @return string The output of the filter chain
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function filter($input);

   /**
    * Let's you add a filter to the end of the chain. Please note, that
    * the execution order corresponds to the order the filters are added.
    *
    * @param ChainedContentFilter $filter The filter implementation to add.
    *
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function &appendFilter(ChainedContentFilter $filter);

   /**
    * Let's you add a filter to the beginning of the chain. Please note, that
    * the execution order corresponds to the order the filters are added.
    *
    * @param ChainedContentFilter $filter The filter implementation to add.
    *
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2011<br />
    */
   public function &prependFilter(ChainedContentFilter $filter);

   /**
    * This method can be used to remove a filter from the chain. The filter
    * to remove is addressed by it's implementation class' name.
    *
    * @param string $class The class name of the filter to remove from the chain.
    *
    * @return FilterChain The current filter chain instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.01.2011<br />
    */
   public function &removeFilter($class);

}
