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
   *  @package core::errorhandler::documentcontroller
   *  @class errorpage_v1_controller
   *
   *  Implements the error page's document controller.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.01.2007<br />
   */
   class errorpage_v1_controller extends baseController
   {

      function errorpage_v1_controller(){
      }


      /**
      *  @public
      *
      *  Displays the error page.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      *  Version 0.2, 26.12.2008 (Messages after the trigger_error() are not displayed any more)<br />
      */
      function transformContent(){

         // build stacktrace
         $Errors = $this->__buildStackTrace();
         $buffer = (string)'';

         // get template
         $Template__ErrorEntry = & $this->__getTemplate('ErrorEntry');

         // generate stacktrace
         for($i = 0; $i < count($Errors); $i++){

            // don't display any further messages, because these belong to the error manager
            if(isset($Errors[$i]['function']) && preg_match('/errorHandler|trigger_error/i',$Errors[$i]['function'])){
               break;
             // end if
            }

            if(isset($Errors[$i]['function'])){
               $Template__ErrorEntry->setPlaceHolder('Function',$Errors[$i]['function']);
             // end if
            }

            if(isset($Errors[$i]['line'])){
               $Template__ErrorEntry->setPlaceHolder('Line',$Errors[$i]['line']);
             // end if
            }

            if(isset($Errors[$i]['file'])){
               $Template__ErrorEntry->setPlaceHolder('File',$Errors[$i]['file']);
             // end if
            }

            if(isset($Errors[$i]['class'])){
               $Template__ErrorEntry->setPlaceHolder('Class',$Errors[$i]['class']);
             // end if
            }

            if(isset($Errors[$i]['type'])){
               $Template__ErrorEntry->setPlaceHolder('Type',$Errors[$i]['type']);
             // end if
            }

            $buffer .= $Template__ErrorEntry->transformTemplate();

          // end for
         }

         $this->setPlaceHolder('Stacktrace',$buffer);
         $this->setPlaceHolder('ErrorID',$this->__Attributes['id']);
         $this->setPlaceHolder('ErrorMessage',$this->__Attributes['message']);
         $this->setPlaceHolder('ErrorNumber',$this->__Attributes['number']);
         $this->setPlaceHolder('ErrorFile',$this->__Attributes['file']);
         $this->setPlaceHolder('ErrorLine',$this->__Attributes['line']);

       // end function
      }


      /**
      *  @private
      *
      *  Creates the stacktrace.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      private function __buildStackTrace(){
         return array_reverse(debug_backtrace());
       // end function
      }

    // end class
   }
?>