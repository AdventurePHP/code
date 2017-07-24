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

use APF\core\pagecontroller\ApplicationContext;
use APF\tools\form\FormControl;

/**
 * Defines the structure of form validators.
 * <p/>
 * In case you want to implement your own form validator, ist is recommended to
 * derive from AbstractFormValidator class implementing this interface.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.11.2015 (ID#273: introduced interface)<br />
 */
interface FormValidator extends ApplicationContext {

   /**
    * Injects the control to validate and the button, that triggers the validation.
    *
    * @param FormControl $control The control, that should be validated.
    * @param FormControl $button The button, that triggers the validate event.
    * @param string $type The validator type regarding the listener notification.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    */
   public function __construct(FormControl $control, FormControl $button, $type = null);

   /**
    * Method, that is called to validate the element.
    *
    * @param string $input The input to validate.
    *
    * @return boolean True, in case the control is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function validate($input);

   /**
    * Method, that is called, when the validation fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function notify();

   /**
    * Indicates, whether the current validator is active.
    *
    * @return boolean True, in case the validator is active, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.08.2009<br />
    */
   public function isActive();

}
