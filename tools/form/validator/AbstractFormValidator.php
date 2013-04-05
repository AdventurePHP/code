<?php
namespace APF\tools\form\validator;

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
use APF\core\pagecontroller\APFObject;
use APF\tools\form\taglib\AbstractFormControl;

/**
 * @package APF\tools\form\filter
 * @class AbstractFormValidator
 *
 * This class defines the scheme of form validators. Implements some basic
 * parts of the validator implementation.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.08.2009<br />
 */
abstract class AbstractFormValidator extends APFObject {

   /**
    * @var string The default css class to mark invalid form controls.
    * @since 1.12
    */
   public static $DEFAULT_MARKER_CLASS = 'apf-form-error';

   /**
    * @var string The attribute, that can be used to define custom marker classes.
    * @since 1.12
    */
   public static $CUSTOM_MARKER_CLASS_ATTRIBUTE = 'valmarkerclass';

   /**
    * Includes a reference on the control to validate.
    * @var AbstractFormControl The control to validate.
    */
   protected $control;

   /**
    * Includes a reference on the button of the form,
    * that initiates the validation event.
    * @var AbstractFormControl The button that triggers the event.
    */
   protected $button;

   /**
    * Indicates the type of validator listeners, that should be notified.
    * In case the type is set to <em>special</em>, only listeners having
    * the <em>validator</em> attribute specified should be notified.
    * @since 1.12
    * @var string The validator type.
    */
   protected $type = null;

   /**
    * @since 1.12
    * @var string Indicates the special validator behaviour.
    */
   protected static $SPECIAL_VALIDATOR_INDICATOR = 'special';

   /**
    * @public
    *
    * Injects the control to validate and the button, that triggers the validation.
    *
    * @param AbstractFormControl $control The control, that should be validated.
    * @param AbstractFormControl $button The button, that triggers the validate event.
    * @param string $type The validator type regarding the listener notification.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    */
   public function __construct(AbstractFormControl &$control, AbstractFormControl &$button, $type = null) {
      $this->control = &$control;
      $this->button = &$button;
      $this->type = $type;
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
   public function isActive() {
      return $this->button->isSent();
   }

}
