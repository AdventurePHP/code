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
namespace APF\tools\form\taglib;

use APF\tools\form\filter\FormFilter;
use APF\tools\form\FormException;
use APF\tools\form\validator\FormValidator;

/**
 * Implements a base class for the <em>form:addfilter</em> and
 * <em>form:addvalidator</em> taglibs. Constructs a form control
 * observer (filter or validator) using the tag attributes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.08.2009<br />
 */
abstract class FormControlObserverBase extends AbstractFormControl {

   /**
    * The name of the attribute, that specifies the validator type.
    *
    * @var string $TYPE_ATTRIBUTE_NAME
    *
    * @since 1.12
    */
   private static $TYPE_ATTRIBUTE_NAME = 'type';

   /**
    * Overwrite the parent's onParseTime() method to not
    * initiate presetting.
    */
   public function onParseTime() {
   }

   /**
    * Overwrites the transform() method to generate empty output.
    *
    * @return string Empty string, because the tag generates no output.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   public function transform() {
      return '';
   }

   public function reset() {
      // nothing to do as observer tags create no visible output
   }

   /**
    * Constructs the desired form control observer using tag attributes.
    *
    * @@param string $injectionMethod The name of the method to inject the observer with.
    * @return FormValidator The form control observer.
    * @throws FormException In case mandatory attributes are missing.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.08.2009<br />
    */
   protected function addObserver($injectionMethod) {

      // validate the required attributes
      $controlDef = $this->getAttribute('control');
      $buttonName = $this->getAttribute('button');
      $class = $this->getAttribute('class');

      /* @var $parent HtmlFormTag */
      $form = $this->getForm();

      if (empty($controlDef) || empty($buttonName) || empty($class)) {
         $formName = $form->getAttribute('name');
         throw new FormException('[' . get_class($this) . '::onAfterAppend()] Required attribute '
               . '"control", "button", or "class" missing. Please review your '
               . '&lt;form:addvalidator /&gt; or &lt;form:addfilter /&gt; taglib definition in form "'
               . $formName . '"!');
      }

      // handle multiple controls, that are separated by pipe to make form definition easier.
      $controlNames = explode('|', $controlDef);

      foreach ($controlNames as $controlName) {

         // sanitize control name to avoid errors while addressing a control!
         $controlName = trim($controlName);

         // retrieve elements to pass to the validator and validate them
         $control = $form->getFormElementByName($controlName);
         $button = $form->getFormElementByName($buttonName);
         $type = $this->getAttribute(self::$TYPE_ATTRIBUTE_NAME);

         if ($control === null || $button === null) {
            $formName = $form->getAttribute('name');
            throw new FormException('[' . get_class($this) . '::onAfterAppend()] The form with name '
                  . '"' . $formName . '" does not contain a control with name "' . $controlName . '" or '
                  . 'a button with name "' . $buttonName . '". Please check your taglib definition!');
         }

         // Construct observer injecting the control to validate and the button,
         // that triggers the event. As of 1.12, the type is presented to the
         // validator to enable special listener notification.
         /* @var $observer FormValidator|FormFilter */
         $observer = new $class($control, $button, $type);
         $observer->setContext($this->context);
         $observer->setLanguage($this->language);

         // inject the observer into the form control
         $control->{$injectionMethod}($observer);

      }

   }

}
