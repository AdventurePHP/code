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

   import('tools::form::filter','FormFilter');
   
   /**
    * @namespace tools::form::filter
    * @class MultiplexFormFilter
    *
    * Extends the FormFilter's functionality and enables the developer to filter an input by
    * multiple filter instructions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.12.2008<br />
    */
   class MultiplexFormFilter extends FormFilter
   {

      function MultiplexFormFilter(){
      }


      /**
       * @public
       *
       * Filters an input by multiple instructions, that are separated by pipe.
       *
       * @param string $filterInstruction the filter instruction
       * @param string $input the filter's input
       * @return string $output the output
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.12.2008<br />
       * Version 0.2, 17.07.2009 (Applied the filter refactoring to the form filters)<br />
       */
      function filter($input){

         // create instruction set
         $instructionSet = explode('|',$this->__Instruction);

         // initialize output
         $output = $input;

         // apply filter
         $filterMethods = get_class_methods(get_class($this));
         foreach($instructionSet as $filterInstruction){

            $filterMethod = '__'.trim($filterInstruction);

            if(in_array($filterMethod,$filterMethods)){
               $output = $this->$filterMethod($output);
             // end if
            }
            else{
               trigger_error('[MultiplexFormFilter::filter()] The filter instruction "'.$this->__Instruction.'" cannot be applied! Please consult the manual to get the supported instructions.');
             // end else
            }

          // end foreach
         }

         return $output;

       // end function
      }

    // end class
   }
?>