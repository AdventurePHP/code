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

   import('tools::form::validator','AbstractFormValidator');

   /**
    * @namespace tools::form::validator
    * @class SimpleDateControlValidator
    * 
    * Implements a base class for all text field validators.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   class SimpleDateControlValidator extends AbstractFormValidator {

      /**
       * Validates the date contained in the date control. Checks, whether the
       * date is greater than or equal to the current date.
       * 
       * @param string $input The content of the form control (YYYY-MM-DD).
       * @return boolean True, in case the control is valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function validate($input){

         $date = (int) str_replace('-','',$input);
         $today = (int) date('Ymd');

         if($today > $date){
            return false;
         }
         return true;
         
       // end function
      }

      /**
       * @public
       * 
       * Notifies the form control to be invalid.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function notify(){

         $this->__Control->markAsInvalid();

         $day = &$this->__Control->getDayControl();
         $day->addAttribute('style','; background-color: red;');
         $month = &$this->__Control->getMonthControl();
         $month->addAttribute('style','; background-color: red;');
         $year = &$this->__Control->getYearControl();
         $year->addAttribute('style','; background-color: red;');

         $this->__Control->notifyValidationListeners();

       // end function
      }

    // end function
   }
?>