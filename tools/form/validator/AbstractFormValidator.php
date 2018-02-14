<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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

use APF\core\http\mixins\GetRequestResponse;
use APF\core\pagecontroller\APFObject;
use APF\tools\form\FormControl;

/**
 * Defines the base class for all form validators. In case you want to implement your
 * own form validator, derive from this class.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.08.2009<br />
 * Version 0.2, 06.11.2015 (ID#273: introduced interface)<br />
 */
abstract class AbstractFormValidator extends APFObject implements FormValidator {

   use GetRequestResponse;

   /**
    * The default css class to mark invalid form controls.
    *
    * @var string $DEFAULT_MARKER_CLASS
    *
    * @since 1.12
    */
   public static $DEFAULT_MARKER_CLASS = 'apf-form-error';

   /**
    * The attribute, that can be used to define custom marker classes.
    *
    * @var string $CUSTOM_MARKER_CLASS_ATTRIBUTE
    *
    * @since 1.12
    */
   public static $CUSTOM_MARKER_CLASS_ATTRIBUTE = 'valmarkerclass';

   /**
    * Indicates the special validator behaviour.
    *
    * @var string $SPECIAL_VALIDATOR_INDICATOR
    *
    * @since 1.12
    */
   protected static $SPECIAL_VALIDATOR_INDICATOR = 'special';

   /**
    * Includes a reference on the control to validate.
    *
    * @var FormControl $control
    */
   protected $control;

   /**
    * Includes a reference on the button of the form,
    * that initiates the validation event.
    *
    * @var FormControl $button
    */
   protected $button;

   /**
    * Indicates the type of validator listeners, that should be notified.
    * In case the type is set to <em>special</em>, only listeners having
    * the <em>validator</em> attribute specified should be notified.
    *
    * @since 1.12
    * @var string $type
    */
   protected $type = null;

   public function __construct(FormControl $control, FormControl $button, $type = null) {
      $this->control = $control;
      $this->button = $button;
      $this->type = $type;
   }

   public function isActive() {
      return $this->button->isSent();
   }

}
