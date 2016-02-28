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
use APF\tools\form\taglib\ValidationListenerTag;

/**
 * Implements a base class for all text field validators.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.08.2009<br />
 */
abstract class TextFieldValidator extends AbstractFormValidator {

   /**
    * Notifies the form control to be invalid.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function notify() {
      $this->control->markAsInvalid();
      $this->markControl($this->control);
      $this->notifyValidationListeners($this->control);
   }

   /**
    * Marks a form control als invalid using a css class. See
    * http://wiki.adventure-php-framework.org/Weiterentwicklung_Formular-Validierung
    * for details.
    *
    * @param FormControl $control The control to mark as invalid.
    *
    * @since 1.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.02.2010<br />
    * Version 0.2, 07.03.2011 (use control's appendCssClass() now)<br />
    */
   protected function markControl(FormControl &$control) {
      $marker = $this->getCssMarkerClass($control);
      $control->appendCssClass($marker);
   }

   /**
    * Evaluates the css class used to mark an invalid form control.
    *
    * @param FormControl $control The control to validate.
    *
    * @return string The css marker class for validation notification.
    *
    * @since 1.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.02.2010<br />
    */
   protected function getCssMarkerClass(FormControl &$control) {
      $marker = $control->getAttribute(self::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
      if (empty($marker)) {
         $marker = self::$DEFAULT_MARKER_CLASS;
      }

      return $marker;
   }

   /**
    * Notifies all validation listeners, who's control attribute is the same
    * as the name of the present control. This enables you to insert listener
    * tags, that output special content if notified. Hence, you can realize
    * special error formatting.
    * <p/>
    * In case the name of the special validator is unlike <em>null</em>, all
    * listeners will be notified, that have the <em>validator</em> attribute
    * specified. This lets you define dedicated listener, that are only
    * displayed when triggered by a special validator.
    *
    * @param FormControl $control The control who's listeners should be notified.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    * Version 0.2, 12.02.2010 (Moved to TextFieldValidator and introduced special listener notification)<br />
    */
   protected function notifyValidationListeners(FormControl &$control) {

      $form = $control->getForm();

      /* @var $listeners ValidationListenerTag[] */
      $listeners = $form->getFormElementsByTagName('form:listener');
      $count = count($listeners);
      $controlName = $control->getAttribute('name');
      $validatorName = $this->getValidatorName();

      for ($i = 0; $i < $count; $i++) {
         // Here, we're using a little trick: empty attributes are considered "null"
         // by the XmlParser. Thus, we can set the validator name to null to
         // indicate, that we want a "normal" listener (=no special listener) to be
         // notified!
         if ($listeners[$i]->getAttribute('control') === $controlName
               && $listeners[$i]->getAttribute('validator') === $validatorName
         ) {
            $listeners[$i]->notify();
         }
      }

   }

   /**
    * Evaluates the name of the validator to be used during listener notification.
    *
    * @return string Indicates the name of the special validator to notify.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.02.2010<br />
    */
   protected function getValidatorName() {
      if ($this->type === self::$SPECIAL_VALIDATOR_INDICATOR) {
         return get_class($this);
      }

      return null;
   }

}
