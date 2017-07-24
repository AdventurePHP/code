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
namespace APF\tools\form\validator;

use APF\tools\form\FormControl;
use APF\tools\form\FormException;

/**
 * Implements a validator, that compares the content of two text fields. In
 * order to apply the validator, the form element that is added the validator
 * must specify the <em>ref</em> attribute to specify the reference field.
 * <p/>
 * In case you have two password fields - one for the password and one to double
 * check the input - the validator must be applied to the main password field, that
 * specifies the <em>ref</em> attribute containing the name of the second password
 * field. The code looks as follows:
 * <pre>
 *   <form:password name="pass" ref="pass2" />
 *   <form:password name="pass2" />
 *   <form:addvalidator
 *     class="APF\tools\form\validator\FieldCompareValidator"
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
    * The reference form control.
    *
    * @var FormControl $refControl
    */
   protected $refControl = null;

   public function __construct(FormControl $control, FormControl $button, $type = null) {
      parent::__construct($control, $button, $type);
      $this->initializeReferenceControl();
   }

   /**
    * Initializes the reference control field for the validator. Takes
    * care, that the main field specifies the "ref" attribute.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   private function initializeReferenceControl() {

      $refControlName = $this->control->getAttribute('ref');
      $form = $this->control->getForm();
      if ($refControlName === null) {
         throw new FormException('[FieldCompareValidator::initializeReferenceControl()] The main field '
               . 'definition does not include the "ref" attribute. This attribute must be specified '
               . 'to tell the validator, which form control can be used as reference. Please '
               . 'check taglib definition of control "' . $this->control->getAttribute('name') . '" '
               . 'within form "' . $form->getAttribute('name') . '"!',
               E_USER_ERROR);
      }

      $this->refControl = $form->getFormElementByName($refControlName);
   }

   /**
    * Validates the values of the main and the reference control against each other.
    *
    * @param string $input The input of the *main* password field.
    *
    * @return bool True, in case both controls are equal, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function validate($input) {
      $refValue = $this->refControl->getAttribute('value');
      if ($input === $refValue) {
         return true;
      }

      return false;
   }

   /**
    * Re-implements the notify() method to both mark the main field as well as the
    * reference field as invalid and notify both control's listeners.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function notify() {
      $this->control->markAsInvalid();
      $this->refControl->markAsInvalid();
      $this->markControl($this->control);
      $this->markControl($this->refControl);
      $this->notifyValidationListeners($this->control);
      $this->notifyValidationListeners($this->refControl);
   }

}
