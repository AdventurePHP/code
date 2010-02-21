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
    * @class FieldCompareValidator
    *
    * Implements a validator, that compares the content of two text fields. In
    * order to apply the validator, the form element that is added the validator
    * must specify the <em>ref</em> attribute to specify the reference field.
    * <p/>
    * In case you have two password fields - one for the password and one to double
    * check the input - the validator must be applied to the main password field, that
    * specifies the <em>ref</em> attribute containg the name of the second password
    * field. The code looks as follows:
    * <pre>
    *   <form:password name="pass" ref="pass2" />
    *   <form:password name="pass2" />
    *   <form:addvalidator
    *     class="FieldCompareValidator"
    *     control="pass"
    *     button="login"
    *   />
    *   <form:button name="login" value="login" />
    * </pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 11.09.2009<br />
    */
   class FieldCompareValidator extends TextFieldValidator {

      /**
       * @var form_control The reference form control.
       */
      private $__RefControl = null;

      /**
       * @public
       *
       * Re-implements the AbstractValidator's constructor to initialize the reference
       * form control.
       *
       * @param form_control $control The main control, that is to validate.
       * @param form_control $button The button, that triggers the validation event.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function FieldCompareValidator(form_control &$control,form_control &$button){
         $this->__Control = &$control;
         $this->__Button = &$button;
         $this->initializeReferenceControl();
       // end function
      }
      
      /**
       * @public
       *
       * Validates the values of the main and the reference control against each other.
       *
       * @param string $input The input of the *main* password field.
       * @return True, in case both controls are equal, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function validate($input){
         $refValue = $this->__RefControl->getAttribute('value');
         if($input === $refValue){
            return true;
         }
         return false;
       // end function
      }

      /**
       * @public
       *
       * Re-implements the notify() method to both mark the main field as well as the
       * reference field as invalid and notify both control's listeners.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function notify(){
         $this->__Control->markAsInvalid();
         $this->__RefControl->markAsInvalid();
         $this->markControl($this->__Control);
         $this->markControl($this->__RefControl);
         $this->notifyValidationListeners($this->__Control);
         $this->notifyValidationListeners($this->__RefControl);
       // end function
      }

      /**
       * @private
       *
       * Initializes the reference control field for the validator. Takes
       * care, that the main field specifies the "ref" attribute.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      private function initializeReferenceControl(){

         $refControlName = $this->__Control->getAttribute('ref');
         if($refControlName === null){
            $form = &$this->__Control->getParentObject();
            $formName = $form->getAttribute('name');
            trigger_error('[FieldCompareValidator::__initializeReferenceControl()] The main field '
               .'definition does not include the "ref" attribute. This attribute must be specified '
               .'to tell the validator, which form control can be used as reference. Please '
               .'check taglib definition of control "'.$this->__Control->getAttribute('name').'" '
               .'within form "'.$formName.'"!',
               E_USER_ERROR);
            exit(1);
         }

         $form = &$this->__Control->getParentObject();
         $this->__RefControl = &$form->getFormElementByName($refControlName);
         
       // end function
      }

    // end class
   }
?>