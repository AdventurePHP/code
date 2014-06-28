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
use APF\core\filter\FilterChain;

/**
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

   /**
    * @param ChainedContentFilter $filter The filter implementation to add.
    *
    * @return FilterChain The current filter chain instance for further usage.
    */
   public function &appendFilter(ChainedContentFilter $filter) {
      $this->filters[] = $filter;
      $this->count++;

      return $this;
   }

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
   public function &prependFilter(ChainedContentFilter $filter) {

      array_unshift($this->filters, $filter);
      $this->count++;

      // since it is possible to prepend a filter during chain execution,
      // the current offset must be corrected to execute the remaining
      // filters correctly (do not execute the filter at the current offset twice!)
      if ($this->offset !== 0) {
         $this->offset++;
      }

      return $this;
   }


   /**
    * @param string $class The class name of the filter to remove from the chain.
    *
    * @return FilterChain The current filter chain instance for further usage.
    */
   public function &removeFilter($class) {

      // since it is possible to remove a filter during chain execution,
      // start at the current offset to disallow removal of filters that
      // have already been executed.
      for ($i = $this->offset; $i < $this->count; $i++) {
         if ($this->filters[$i] instanceof $class) {
            array_splice($this->filters, $i, 1);
            $this->count--;
         }
      }

      return $this;
   }

   /**
    * This method can be used to check, whether a filter is registered on the chain.
    * The filter is addressed by it's implementation class' name.
    *
    * @param string $class The class name of the filter to check for it's status on the stack.
    *
    * @return boolean True in case the filter is registered, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.09.2011<br />
    */
   public function isFilterRegistered($class) {
      for ($i = $this->offset; $i < $this->count; $i++) {
         if ($this->filters[$i] instanceof $class) {
            return true;
         }
      }

      return false;
   }

   /**
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