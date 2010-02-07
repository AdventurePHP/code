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
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

    import('tools::form::validator','TextFieldValidator');

   /**
    * @package modules::captcha::pres::validator
    * @class CaptchaValidator
    * 
    * Implements a validator for the captcha field.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   class CaptchaValidator extends TextFieldValidator {

      /**
       * @public
       *
       * Checks the input of the captcha field.
       *
       * @param string $input The input to validate.
       * @return boolean True, in case the control is valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function validate($input){

         // get the captcha content of the current request
         $captcha = $this->__Control->getCurrentCaptcha();

         // validate field
         if(strlen($input) == 5
            && preg_match('/^([A-Za-z0-9]+)$/',$input) 
            && $input === $captcha){
            return true;
          // end if
         }
         return false;
         
       // end function
      }

      /**
       * @public
       *
       * Re-implements the notify() to care about the special
       * structure of the captcha control.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function notify(){

         // add validation style to the text field
         $textField = &$this->__Control->getCaptchaTextField();
         $textField->markAsInvalid();
         $this->markControl($textField);

         // clear captcha field, if desired
         if($this->__Control->getAttribute('clearonerror') === 'true'){
            $textField->setAttribute('value','');
          // end if
         }

         // notify listeners to be able to handle the validation event
         $this->__Control->notifyValidationListeners();

       // end function
      }

     // end class
    }
?>