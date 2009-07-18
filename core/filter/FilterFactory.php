<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
    * @namespace core::filter
    * @class FilterDefinition
    *
    * Represents the description of an APF filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.12.2007<br />
    */
   final class FilterDefinition extends coreObject
   {

      /**
      *  @protected
      *  The namespace of the filter class.
      */
      protected $__Namespace = null;


      /**
      *  @protected
      *  The name of the filter class (and file name as well).
      */
      protected $__Class = null;


      /**
       * @public
       *
       * Constructor of the filter description. Taks the namespace and the class as an argument.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.12.2007<br />
       */
      function FilterDefinition($namespace,$class){
         $this->__Namespace = $namespace;
         $this->__Class = $class;
       // end function
      }

    // end class
   }


   /**
    * @namespace core::filter
    * @class AbstractFilter
    * @abstract
    *
    * Abstract filter class.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 08.06.2007<br />
    */
   abstract class AbstractFilter extends coreObject
   {

      function AbstractFilter(){
      }


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

    // end class
   }


   /**
    * @namespace core::filter
    * @class FilterFactory
    *
    * Implements a simple factory to load filter classes derived from the AbstractFilter class.
    * Each filter is described by the FilterDefinition class.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.06.2007<br />
    */
   class FilterFactory
   {

      function FilterFactory(){
      }


      /**
      *  @public
      *  @static
      *
      *  Returns an instance of the desired filter.<br />
      *
      *  @param FilterDefinition $filterDefinition the definition of the APF style filter
      *  @return AbstractFilter $filterInstance the instance of the filter or null in case the filter class does not exist
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 13.08.2008 (Removed unused code)<br />
      *  Version 0.3, 07.11.2008 (Bugfix: the namespace of filters outside "core::filter" could not be included)<br />
      *  Version 0.4, 11.12.2008 (Switched to FilterDefinition for addressing a filter)<br />
      */
      static function getFilter($filterDefinition){

         // check definition
         $defClassName = get_class($filterDefinition);
         if($defClassName !== 'FilterDefinition'){
            trigger_error('[FilterFactory::getFilter()] The given filter definition (class name: "'.$defClassName.'") is not an instance of the "FilterDefinition" class!',E_USER_ERROR);
            return null;
          // end if
         }

         // gather the filter information
         $namespace = $filterDefinition->get('Namespace');
         $filterName = $filterDefinition->get('Class');

         // check, if the filter exists and include it
         if(file_exists(APPS__PATH.'/'.str_replace('::','/',$namespace).'/'.$filterName.'.php')){
            import($namespace,$filterName);
            return new $filterName;
          // end if
         }
         else{
            trigger_error('[FilterFactory::getFilter()] Requested filter "'.$filterName.'" cannot be loaded from namespace "'.$namespace.'"!',E_USER_ERROR);
            return null;
          // end else
         }

       // end function
      }

    // end class
   }
?>