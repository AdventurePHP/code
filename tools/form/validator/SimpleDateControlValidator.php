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

   import('tools::form::validator','TextFieldValidator');

   /**
    * @package tools::form::validator
    * @class SimpleDateControlValidator
    * 
    * Implements a simple date control validator. It expects the selected date to
    * be greater than today.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   class SimpleDateControlValidator extends TextFieldValidator {

      /**
       * @private
       * 
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

         // for the date control, we have to obtain the special
         // marker class from the date control and then apply
         // them to the included select controls
         $day = &$this->__Control->getDayControl();
         $this->markControl($day);

         $month = &$this->__Control->getMonthControl();
         $this->markControl($month);

         $year = &$this->__Control->getYearControl();
         $this->markControl($year);

         $this->notifyValidationListeners($this->__Control);

       // end function
      }

      /**
       * @protected
       *
       * Overwrites the method to adapt the behavior to the date control. Here,
       * we have to take the marker class from the surrounding date control
       * instead of the single select controls
       *
       * @param form_control $control The control to validate.
       * @return string The css marker class for validation notification.
       *
       * @since 1.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.02.2010<br />
       */
      protected function getCssMarkerClass(&$control){
         $marker = $this->__Control->getAttribute(self::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
         if(empty($marker)){
            $marker = self::$DEFAULT_MARKER_CLASS;
         }
         return $marker;
       // end function
      }

    // end function
   }
?>