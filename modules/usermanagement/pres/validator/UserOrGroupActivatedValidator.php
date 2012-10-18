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
import('tools::form::validator', 'TextFieldValidator');

/**
 * @package modules::usermanagement::pres::validator
 * @class UserOrGroupActivatedValidator
 *
 * Validator for for combined user and group multi select fields within the proxy forms.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 05.06.2010<br />
 */
class UserOrGroupActivatedValidator extends TextFieldValidator {

   /**
    * @var form_taglib_multiselect The alternative control to validate.
    */
   private $alternativeControl;

   public function validate($input) {

      // validation has to be done using the request, because we have
      // dynamically filled form elements!
      $controlName = $this->__Control->getAttribute('name');
      $altControlName = $this->__Control->getAttribute('alt');

      // initialize alternative control for marking
      $form = &$this->__Control->getParentObject();
      /* @var $form html_taglib_form */
      $this->alternativeControl = &$form->getFormElementByName($altControlName);

      if (!isset($_REQUEST[$controlName]) && !isset($_REQUEST[$altControlName])) {
         return false;
      }
      return true;

   }

   /**
    * @private
    *
    * Re-implements form control marking for combined user and group multi select fields.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.06.2010<br />
    */
   public function notify() {

      $this->__Control->markAsInvalid();
      $this->alternativeControl->markAsInvalid();
      $this->markControl($this->__Control);
      $this->markControl($this->alternativeControl);

      // only mark one listener, because we need to display one error message
      $this->notifyValidationListeners($this->__Control);

   }

}
