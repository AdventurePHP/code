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
      public function __construct($namespace, $class){
         $this->namespace = $namespace;
         $this->class = $class;
      }

      /**
       * @public
       *
       * Returns the namespace of the filter implementation class.
       *
       * @return string The namespace of the filter implementation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getNamespace(){
         return $this->namespace;
      }

      /**
       * @public
       *
       * Returns the class name of the filter implementation class.
       *
       * @return string The class name of the filter implementation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getClass(){
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
?>