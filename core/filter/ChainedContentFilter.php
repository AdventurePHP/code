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
    * This method must be implemented by each single chained filter to
    * influence the result of the filter chain execution.
    *
    * @param FilterChain $chain The instance of the current filter chain.
    * @param string $input The current input to filter.
    *
    * @return string The result of the filter execution.
    */
   public function filter(FilterChain &$chain, $input = null);

}
