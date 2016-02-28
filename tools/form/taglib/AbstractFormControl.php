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

use APF\core\pagecontroller\Document;
use APF\tools\form\filter\FormFilter;
use APF\tools\form\FormControl;
use APF\tools\form\FormException;
use APF\tools\form\HtmlForm;
use APF\tools\form\validator\FormValidator;

/**
 * Implements a base class for all APF form elements.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 02.06.2007 (Added the $__ExclusionArray, moved the __getAttributesAsString() to the APFObject class)<br />
 * Version 0.3, 07.12.2008 (Added the filter functionality, that let's you filter user input)<br />
 * Version 0.4, 07.07.2010 (Added event attributes defined in xhtml 1.0 strict)<br />
 * Version 0.5, 21.07.2010 (Added function for adding attributes to the controls white-list)<br />
 * Version 0.6, 06.11.2015 (ID#273: extracted documentation to FormControl interface)<br />
 */
abstract class AbstractFormControl extends Document implements FormControl {

   public static $CORE_ATTRIBUTES = ['id', 'class', 'style', 'title'];
   public static $EVENT_ATTRIBUTES = ['onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover',
         'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup'];
   public static $I18N_ATTRIBUTES = ['lang', 'xml:lang', 'dir'];

   public static $HTML5_ATTRIBUTES = ['placeholder', 'name', 'disabled', 'form', 'autocomplete', 'autofocus',
         'list', 'maxlength', 'pattern', 'readonly', 'required', 'size', 'min',
         'max', 'step', 'multiple', 'formaction', 'formenctype', 'formmethod',
         'formtarget', 'formnovalidate', 'height', 'width', 'alt', 'src',
         'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone'];

   /**
    * Indicates, whether the form control is valid or not.
    *
    * @var boolean $controlIsValid
    *
    * @since 1.11
    */
   protected $controlIsValid = true;

   /**
    * Indicates, whether the form is sent or not.
    *
    * @var boolean $controlIsSent
    *
    * @since 1.11
    */
   protected $controlIsSent = false;

   /**
    * Indicates, whether the control will be displayed or not (this is *not* visibility state)
    *
    * @var bool $isVisible
    *
    * @since 1.17
    */
   protected $isVisible = true;

   /**
    * The list of validators registered for the current control.
    *
    * @var FormValidator[] $validators
    */
   protected $validators = [];

   /**
    * The list of validators registered for the current control.
    *
    * @var FormFilter[] $filters
    */
   protected $filters = [];

   /**
    * The attributes, that are allowed to render into the XHTML/1.1 strict document.
    *
    * @var string[] $attributeWhiteList
    *
    * @since 1.12
    */
   protected $attributeWhiteList = [
      // core attributes
         'id', 'style', 'class',

      // event attributes
         'accesskey', 'tabindex', 'onfocus', 'onblur', 'onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover',
         'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup',

      // i18n attributes
         'lang', 'xml:lang', 'dir',

      // HTML5 attributes
         'placeholder', 'name', 'disabled', 'form', 'autocomplete', 'autofocus', 'list', 'maxlength', 'pattern', 'readonly',
         'required', 'size', 'min', 'max', 'step', 'multiple', 'formaction', 'formenctype', 'formmethod', 'formtarget',
         'formnovalidate', 'height', 'width', 'alt', 'src', 'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone'
   ];

   /**
    * Initiate presetting of the form control. If you cannot use
    * value presetting, overwrite the protected method
    * <code>presetValue()</code>.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   public function onParseTime() {
      $this->presetValue();
   }

   /**
    * Pre-fills the value of the current control.
    *
    * @throws FormException In case the form control has no name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 08.08.2008 (Fixed bug, that the number "0" was not automatically pre-filled)<br />
    * Version 0.3, 23.11.2010 (Bug-fix: presetting did not work in combination with existing value attributes)<br />
    */
   protected function presetValue() {

      // check, whether the control has a name. if not, complain about that, because presetting
      // is only only possible for named controls!
      $controlName = $this->getAttribute('name');
      if ($controlName === null) {
         $formName = $this->getForm()->getAttribute('name');
         throw new FormException('[' . get_class($this) . '::presetValue()] A form control is missing '
               . ' the required tag attribute "name". Please check the taglib definition of the '
               . 'form with name "' . $formName . '"!', E_USER_ERROR);
      }

      // try to preset the field with the url parameter if applicable (or contains 0)
      $value = $this->getRequest()->getParameter($controlName);

      if ($value !== null) {
         $this->setAttribute('value', $value);
      }
   }

   public function &getForm() {

      $form = $this->getParentObject();

      if ($form instanceof HtmlForm) {
         return $form;
      }

      while (!($form instanceof HtmlForm)) {
         $form = $form->getParentObject();

         if ($form === null) {
            throw new FormException('Cannot find form starting at form control with name '
                  . $this->getAttribute('name') . '! Please check your template setup.');
         }
      }

      return $form;
   }

   public function isValid() {
      return $this->controlIsValid;
   }

   public function &markAsInvalid() {
      $this->controlIsValid = false;

      return $this;
   }

   public function &markAsValid() {
      $this->controlIsValid = true;

      return $this;
   }

   public function &markAsSent() {
      $this->controlIsSent = true;

      return $this;
   }

   public function isSent() {
      return $this->controlIsSent;
   }

   public function reset() {
      // reset value attribute as basic implementation as it applies in several cases
      $this->setAttribute('value', '');
   }

   public function isChecked() {
      return $this->getAttribute('checked') == 'checked';
   }

   public function &check() {
      $this->setAttribute('checked', 'checked');

      return $this;
   }

   public function &uncheck() {
      $this->deleteAttribute('checked');

      return $this;
   }

   public function &disable() {
      $this->setAttribute('disabled', 'disabled');

      return $this;
   }

   public function &enable() {
      $this->deleteAttribute('disabled');

      return $this;
   }

   public function isDisabled() {
      return $this->getAttribute('disabled') == 'disabled';
   }

   public function &setReadOnly() {
      $this->setAttribute('readonly', 'readonly');

      return $this;
   }

   public function &setReadWrite() {
      $this->deleteAttribute('readonly');

      return $this;
   }

   public function isReadOnly() {
      return $this->getAttribute('readonly') == 'readonly';
   }

   public function addFilter(FormFilter &$filter) {

      // ID#166: register filter for further usage.
      $this->filters[] = $filter;

      // Directly execute filter to allow adding filters within tags and
      // document controllers for both static and dynamic form controls.
      if ($filter->isActive()) {
         $value = $this->getValue();
         $filteredValue = $filter->filter($value);
         $this->setValue($filteredValue);
      }
   }

   public function getValue() {
      return $this->getAttribute('value', '');
   }

   public function &setValue($value) {
      $this->setAttribute('value', $value);

      return $this;
   }

   public function addValidator(FormValidator &$validator) {

      // ID#166: register validator for further usage.
      $this->validators[] = $validator;

      // Directly execute validator to allow adding validators within tags and
      // document controllers for both static and dynamic form controls.
      $value = $this->getValue();

      // Check both for validator being active and for mandatory fields to allow optional
      // validation (means: field has a registered validator but is sent with empty value).
      // ID#233: add/execute validators only in case the control is visible. Otherwise, this
      // may break the user flow with hidden mandatory fields and users end up in an endless loop.
      if ($validator->isActive() && $this->isMandatory($value) && $this->isVisible()) {
         if (!$validator->validate($value)) {
            // Execute validator callback to allow notification and validation event propagation.
            $validator->notify();
         }
      }
   }

   /**
    * Indicates, whether validation is mandatory or not. This enables to introduce
    * optional validators that are only active in case a field is filled.
    *
    * @param mixed $value The current form control value.
    *
    * @return bool True in case the field is mandatory, false otherwise.
    *
    * @author Christian Achatz, Ralf Schubert
    * @version
    * Version 0.1, 01.11.2010<br />
    */
   protected function isMandatory($value) {
      if ($this->getAttribute('optional', 'false') === 'true') {
         return !empty($value);
      }

      return true;
   }

   public function isVisible() {
      return $this->isVisible;
   }

   public function &addAttributeToWhiteList($name) {
      $this->attributeWhiteList[] = $name;
   }

   public function &addAttributesToWhiteList(array $names) {
      $this->attributeWhiteList = array_merge($this->attributeWhiteList, $names);

      return $this;
   }

   public function &appendCssClass($class) {
      $this->addAttribute('class', $class, ' ');

      return $this;
   }

   public function isFilled() {
      return false;
   }

   public function isSelected() {
      return false;
   }

   public function &hide() {
      $this->isVisible = false;

      // hide all dependent fields
      $fields = $this->getDependentFields();
      foreach ($fields as $field) {
         $field->hide();
      }

      return $this;
   }

   /**
    * Evaluates the list of controls that should be hidden/displayed in case this control is
    * hidden/displayed again.
    * <p/>
    * The dependent control feature can be used to hide/show controls together with their labels etc.
    *
    * @return FormControl[] The list of controls referred to by the <em>dependent-controls</em> tag attribute.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   protected function &getDependentFields() {
      $dependentFields = $this->getAttribute('dependent-controls');
      if ($dependentFields === null) {
         $fields = [];

         return $fields;
      }

      $form = $this->getForm();

      $fields = [];

      foreach (explode('|', $dependentFields) as $fieldName) {
         $fields[] = $form->getFormElementByName(trim($fieldName));
      }

      return $fields;
   }

   public function &show() {
      $this->isVisible = true;

      // show all dependent fields
      $fields = $this->getDependentFields();
      foreach ($fields as $field) {
         $field->show();
      }

      return $this;
   }

   public function getValidators() {
      return $this->validators;
   }

   public function getFilters() {
      return $this->filters;
   }

   /**
    * Converts an attributes array into a xml string including the black list
    * and white list definition within the taglib instance.
    *
    * @param string[] $attributes The attributes to convert to string.
    *
    * @return string The attributes' xml string representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.02.2010<br />
    */
   protected function getSanitizedAttributesAsString($attributes) {
      return $this->getAttributesAsString($attributes, $this->attributeWhiteList);
   }

}
