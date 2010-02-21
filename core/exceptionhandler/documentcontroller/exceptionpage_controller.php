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
    * @package core::exceptionhandler::documentcontroller
    * @class exceptionpage_controller
    *
    * Implements the exception page's document controller.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   class exceptionpage_controller extends base_controller {

      public function exceptionpage_controller(){
      }

      /**
       * @public
       *
       * Displays the exception page.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2009<br />
       */
      public function transformContent(){

         // get the exception trace, init output buffer
         $exceptions = $this->__Attributes['trace'];
         $buffer = (string)'';

         // get template
         $templateExceptionEntry = &$this->__getTemplate('ExceptionEntry');

         // generate stacktrace
         for($i = 0; $i < count($exceptions); $i++){

            if(isset($exceptions[$i]['function'])){
               $templateExceptionEntry->setPlaceHolder('Function',$exceptions[$i]['function']);
             // end if
            }

            if(isset($exceptions[$i]['line'])){
               $templateExceptionEntry->setPlaceHolder('Line',$exceptions[$i]['line']);
             // end if
            }

            if(isset($exceptions[$i]['file'])){
               $templateExceptionEntry->setPlaceHolder('File',$exceptions[$i]['file']);
             // end if
            }

            if(isset($exceptions[$i]['class'])){
               $templateExceptionEntry->setPlaceHolder('Class',$exceptions[$i]['class']);
             // end if
            }

            if(isset($exceptions[$i]['type'])){
               $templateExceptionEntry->setPlaceHolder('Type',$exceptions[$i]['type']);
             // end if
            }

            $buffer .= $templateExceptionEntry->transformTemplate();

          // end for
         }

         $this->setPlaceHolder('Stacktrace',$buffer);

         $this->setPlaceHolder('ExceptionID',$this->__Attributes['id']);
         $this->setPlaceHolder('ExceptionType',$this->__Attributes['type']);
         $this->setPlaceHolder('ExceptionMessage',$this->__Attributes['message']);
         $this->setPlaceHolder('ExceptionNumber',$this->__Attributes['number']);
         $this->setPlaceHolder('ExceptionFile',$this->__Attributes['file']);
         $this->setPlaceHolder('ExceptionLine',$this->__Attributes['line']);

       // end function
      }

    // end class
   }
?>