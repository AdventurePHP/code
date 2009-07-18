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
    * @namespace tools::form::filter
    * @class AbstractFormFilter
    * 
    * Defines the base class for all form filters. In case you want to implement your
    * own form filter, derive from this class.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.07.2009 (Implemented a form filter base class because of the PHP bug 48804)<br />
    */
   abstract class AbstractFormFilter extends AbstractFilter {

      /**
       * @var string Defines the filter instruction, that should be executed.
       */
      protected $__Instruction = null;


      public function AbstractFormFilter(){
      }


      /**
       * Defines the filter method, that is applied to the filter input.
       *
       * @param <type> $instruction The filter to apply to the input value.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.07.2009<br />
       */
      public function setInstruction($instruction){
         $this->__Instruction = $instruction;
       // end function
      }
      
    // end class
   }
?>