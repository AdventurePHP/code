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
    * @package tools::form::filter
    * @class AbstractFormValidator
    * 
    * This class defines the scheme of form validators. Implements some basic
    * parts of the validator's implementation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    */
   abstract class AbstractFormValidator extends coreObject {

      /**
       * Includes a reference on the control to validate.
       * @var form_control The control to validate.
       */
      protected $__Control;

      /**
       * Includes a reference on the button of the form,
       * that initiates the validation event.
       * @var form_control The button that triggers the event.
       */
      protected $__Button;

      /**
       * @public
       *
       * Injects the control to validate and the button, that triggers the validation.
       *
       * @param form_control $control The control, that should be validated.
       * @param form_control $button The button, that triggers the validate event.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.08.2009<br />
       */
      public function AbstractFormValidator(form_control &$control,form_control &$button){
         $this->__Control = &$control;
         $this->__Button = &$button;
       // end function
      }

      /**
       * @public
       * @abstract
       *
       * Method, that is called to validate the element.
       *
       * @param string $input The input to validate.
       * @return boolean True, in case the control is valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.08.2009<br />
       */
      public abstract function validate($input);

      /**
       * @public
       * @abstract
       * 
       * Method, that is called, when the validation fails.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.08.2009<br />
       */
      public abstract function notify();

      /**
       * @public
       *
       * Indicates, whether the current validator is active.
       *
       * @return boolean True, in case the validator is active, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 28.08.2009<br />
       */
      public function isActive(){
         return $this->__Button->isSent();
       // end function
      }
      
    // end class
   }
?>