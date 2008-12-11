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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace core::filter
   *  @class AbstractFilter
   *
   *  Represents the description of an APF filter.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.12.2007<br />
   */
   class FilterDefinition extends coreObject
   {

      var $__Namespace = null;
      var $__Class = null;
      var $__FilterInstruction = null;

      function FilterDefinition($namespace,$class,$instruction){
         $this->__Namespace = $namespace;
         $this->__Class = $class;
         $this->__FilterInstruction = $instruction;
       // end function
      }

    // end class
   }


   /**
   *  @namespace core::filter
   *  @class AbstractFilter
   *  @abstract
   *
   *  Abstract filter class.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 08.06.2007<br />
   */
   class AbstractFilter extends coreObject
   {

      function AbstractFilter(){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Abstract filter methode. Must be implemented by concrete filter implementations.
      *
      *  @param string $filterInstruction instructions for the filter
      *  @param void $input the input of the filter
      *  @return void $output the output of the filter
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 08.12.2008 (Added the $filterType argument)<br />
      */
      function filter($filterInstruction,$input = null){
         return $input;
       // end function
      }

    // end class
   }


   /**
   *  @namespace core::filter
   *  @class filterFactory
   *
   *  Implements a simple factory to load filter classes derived ftom the AbstractFilter class.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.06.2007<br />
   */
   class filterFactory
   {

      function filterFactory(){
      }


      /**
      *  @public
      *  @static
      *
      *  Returns an instance of the desired filter .<br />
      *
      *  @param string $namespace the namespace of the filter
      *  @param string $filterName the name of the filter
      *  @return object $filterInstance the instance of the filter or null in case the filter class does not exist
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 13.08.2008 (Removed unused code)<br />
      *  Version 0.3, 07.11.2008 (Bugfix: the namespace of filters outside "core::filter" could not be included)<br />
      */
      function getFilter($namespace,$filterName){

         if(file_exists(APPS__PATH.'/'.str_replace('::','/',$namespace).'/'.$filterName.'.php')){
            import($namespace,$filterName);
            return new $filterName;
          // end if
         }
         else{
            trigger_error('[filterFactory::getFilter()] Requested filter "'.$filterName.'" cannot be loaded from namespace "'.$namespace.'"!',E_USER_ERROR);
            return null;
          // end else
         }

       // end function
      }


      function getFilterByDefinition($def){

         $namespace = $def->get('Namespace');
         $filterName = $def->get('Class');
         if(file_exists(APPS__PATH.'/'.str_replace('::','/',$namespace).'/'.$filterName.'.php')){
            import($namespace,$filterName);
            return new $filterName;
          // end if
         }
         else{
            trigger_error('[filterFactory::getFilter()] Requested filter "'.$filterName.'" cannot be loaded from namespace "'.$namespace.'"!',E_USER_ERROR);
            return null;
          // end else
         }

       // end function
      }

    // end class
   }
?>