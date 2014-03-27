<?php
namespace APF\tools\form\taglib;

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
use APF\core\pagecontroller\Document;
use APF\tools\form\filter\AbstractFormFilter;
use APF\tools\form\FormException;
use APF\tools\form\validator\AbstractFormValidator;

/**
 * @package APF\tools\form\taglib
 * @class AbstractFormControl
 * @abstract
 *
 * Implements a base class for all APF form elements.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 02.06.2007 (Added the $__ExclusionArray, moved the __getAttributesAsString() to the APFObject class)<br />
 * Version 0.3, 07.12.2008 (Added the filter functionality, that let's you filter user input)<br />
 * Version 0.4, 07.07.2010 (Added event attributes defined in xhtml 1.0 strict)<br />
 * Version 0.5, 21.07.2010 (Added function for adding attributes to the controls white-list)<br />
 */
abstract class AbstractFormControl extends Document implements FormControl {

   public static $CORE_ATTRIBUTES = array('id', 'class', 'style', 'title');
   public static $EVENT_ATTRIBUTES = array('onclick', 'ondblclick', 'onmousedown', 'onmouseup', 'onmouseover',
         'onmousemove', 'onmouseout', 'onkeypress', 'onkeydown', 'onkeyup');
   public static $I18N_ATTRIBUTES = array('lang', 'xml:lang', 'dir');

   public static $HTML5_ATTRIBUTES = array('placeholder', 'name', 'disabled', 'form', 'autocomplete', 'autofocus',
         'list', 'maxlength', 'pattern', 'readonly', 'required', 'size', 'min',
         'max', 'step', 'multiple', 'formaction', 'formenctype', 'formmethod',
         'formtarget', 'formnovalidate', 'height', 'width', 'alt', 'src',
         'contenteditable', 'contextmenu', 'dir', 'draggable', 'dropzone');

   /**
    * @since 1.11
    * @var boolean Indicates, whether the form control is valid or not.
    */
   protected $controlIsValid = true;

   /**
    * @since 1.11
    * @var boolean Indicates, whether the form is sent or not.
    */
   protected $controlIsSent = false;

   /**
    * @since 1.17
    * @var bool Indicates, whether the control will be displayed or not (this is *not* visibility state)
    */
   protected $isVisible = true;

   /**
    * @var AbstractFormValidator[] The list of validators registered for the current control.
    */
   protected $validators = array();

   /**
    * @var AbstractFormFilter[] The list of validators registered for the current control.
    */
   protected $filters = array();

   /**
    * @since 1.12
    * @var array{string} The attributes, that are allowed to render into the XHTML/1.1 strict document.
    */
   protected $attributeWhiteList = array(
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
   );

   /**
    * @public
    *
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
    * @public
    *
    * Returns true in case the form is valid and false otherwise.
    *
    * @return boolean The validity status.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   public function isValid() {
      return $this->controlIsValid;
   }

   /**
    * @public
    *
    * Allows you to mark this form control as invalid.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   public function markAsInvalid() {
      $this->controlIsValid = false;
   }

   /**
    * @public
    *
    * Allows you to mark this form control as valid (again).
    *
    * @since 1.17
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.01.2013<br />
    */
   public function markAsValid() {
      $this->controlIsValid = true;
   }

   /**
    * @public
    *
    * Marks a form as sent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function markAsSent() {
      $this->controlIsSent = true;
   }

   /**
    * @public
    *
    * Returns the sending status of the form.
    *
    * @return boolean True in case the form was sent, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function isSent() {
      return $this->controlIsSent;
   }

   /**
    * @public
    *
    * Let's you check, if a radio button was checked.
    *
    * @return boolean True in case the radio button is checked, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function isChecked() {
      return $this->getAttribute('checked') == 'checked';
   }

   /**
    * @public
    *
    * Method for checking the checkbox.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function check() {
      $this->setAttribute('checked', 'checked');
   }

   /**
    * @public
    *
    * Method for un-checking the checkbox.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function uncheck() {
      $this->deleteAttribute('checked');
   }

   /**
    * @public
    *
    * Disables a form control for usage.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function disable() {
      $this->setAttribute('disabled', 'disabled');
   }

   /**
    * @public
    *
    * Enables a form control for user access.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function enable() {
      $this->deleteAttribute('disabled');
   }

   /**
    * @public
    *
    * Let's you query the user access status.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @return bool True in case the control is read only, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function isDisabled() {
      return $this->getAttribute('disabled') == 'disabled';
   }

   /**
    * @public
    *
    * Sets a form control to read only.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function setReadOnly() {
      $this->setAttribute('readonly', 'readonly');
   }

   /**
    * @public
    *
    * Enables a form control for write access.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function setReadWrite() {
      $this->deleteAttribute('readonly');
   }

   /**
    * @public
    *
    * Let's you query the read only status.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @return bool True in case the control is read only, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function isReadOnly() {
      return $this->getAttribute('readonly') == 'readonly';
   }

   /**
    * @public
    * @since 1.11
    *
    * Applies the given filter to the present input element.
    *
    * @param AbstractFormFilter $filter The desired filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    * Version 0.2, 27.03.2014 (Filters are now collected internally to allow retrieval for e.g. client validation rule generation. See ID#166.)<br />
    */
   public function addFilter(AbstractFormFilter &$filter) {

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

   /**
    * @public
    * @since 1.11
    *
    * Executes the given form validator in context of the current form element.
    *
    * @param AbstractFormValidator $validator The desired validator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    * Version 0.2, 01.11.2010 (Added support for optional validators)<br />
    * Version 0.3, 27.03.2014 (Filters are now collected internally to allow retrieval for e.g. client validation rule generation. See ID#166.)<br />
    */
   public function addValidator(AbstractFormValidator &$validator) {

      // ID#166: register validator for further usage.
      $this->validators[] = $validator;

      // Directly execute validator to allow adding validators within tags and
      // document controllers for both static and dynamic form controls.
      $value = $this->getValue();

      // Check both for validator being active and for mandatory fields to allow optional
      // validation (means: field has a registered validator but is sent with empty value).
      if ($validator->isActive() && $this->isMandatory($value)) {
         if (!$validator->validate($value)) {
            // Execute validator callback to allow notification and validation event propagation.
            $validator->notify();
         }
      }
   }

   /**
    * @protected
    *
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

   /**
    * @protected
    *
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
         $formName = $this->getParentObject()->getAttribute('name');
         throw new FormException('[' . get_class($this) . '::presetValue()] A form control is missing '
               . ' the required tag attribute "name". Please check the taglib definition of the '
               . 'form with name "' . $formName . '"!', E_USER_ERROR);
      }

      // try to preset the field with the url parameter if applicable (or contains 0)
      if (isset($_REQUEST[$controlName]) || (isset($_REQUEST[$controlName]) && $_REQUEST[$controlName] === '0')) {
         $this->setAttribute('value', $_REQUEST[$controlName]);
      }
   }

   /**
    * @protected
    *
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

   /**
    * @public
    * @since 1.13
    *
    * Adds an additional attribute to the white list of the control.
    *
    * @param string $name The attribute which should be added to the white list.
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.07.2010<br />
    */
   public function addAttributeToWhitelist($name) {
      $this->attributeWhiteList[] = $name;
   }

   /**
    * @public
    *
    * Savely appends a css class. Resolves missing attribute.
    *
    * @param string $class The css class to append.
    *
    * @since 1.14
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.02.2010<br />
    */
   public function appendCssClass($class) {

      $attr = $this->getAttribute('class');

      // initialize empty attribute
      if (empty($attr)) {
         $attr = $class;
      } else {
         $attr .= ' ' . $class;
      }
      $this->setAttribute('class', $attr);
   }

   /**
    * @public
    *
    * Returns the value of the form control. Does not always return the 'value'
    * attribute. This returns the attribute/content which contains the user input.
    * (For example text areas store the input in the content, not in the value
    * attribute)
    *
    * @return string The current value or content of the control.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function getValue() {
      return $this->getAttribute('value', '');
   }

   /**
    * @public
    *
    * Set's the value of the form control. Should not always set the 'value'
    * attribute. This set's the same attribute/content as the user would type it.
    *
    * @param string $value The value to set.
    *
    * @return AbstractFormControl This instance for further usage.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function setValue($value) {
      $this->setAttribute('value', $value);

      return $this;
   }

   /**
    * @public
    *
    * Let's check if the form:text or form:area was filled with content.
    *
    * @return bool True in case the control is filled, false otherwise.
    *
    * @since 1.15
    *
    * @author dave
    * @version
    * Version 0.1, 20.09.2011<br />
    */
   public function isFilled() {
      return false;
   }

   /**
    * @public
    *
    * Let's check if something was selected in form:select or form:multiselect.
    *
    * @return bool True in case the control is selected, false otherwise.
    * @since 1.15
    *
    * @author dave
    * @version
    * Version 0.1, 22.09.2011<br />
    */
   public function isSelected() {
      return false;
   }

   /**
    * @public
    *
    * Hides a form control from the HTML output of the form it is contained in. Together with
    * it's <em>dependent controls</em> you can hide entire parts of a form from being displayed
    * on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return AbstractFormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
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
    * @public
    *
    * Shows a previously hidden form control from the HTML output of the form it is contained in.
    * Together with it's <em>dependent controls</em> you can show entire parts of a form from being
    * displayed on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return AbstractFormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function &show() {
      $this->isVisible = true;

      // show all dependent fields
      $fields = $this->getDependentFields();
      foreach ($fields as $field) {
         $field->show();
      }

      return $this;
   }

   /**
    * @public
    *
    * Returns the current control's visibility status.
    *
    * @return bool True in case the control is visible, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function isVisible() {
      return $this->isVisible;
   }

   /**
    * @protected
    *
    * Evaluates the list of controls that should be hidden/displayed in case this control is
    * hidden/displayed again.
    * <p/>
    * The dependent control feature can be used to hide/show controls together with their labels etc.
    *
    * @return AbstractFormControl[] The list of controls referred to by the <em>dependent-controls</em> tag attribute.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   protected function &getDependentFields() {
      $dependentFields = $this->getAttribute('dependent-controls');
      if ($dependentFields === null) {
         $fields = array();

         return $fields;
      }

      /* @var $form HtmlFormTag */
      $form = & $this->getParentObject();

      $fields = array();

      foreach (explode('|', $dependentFields) as $fieldName) {
         $fields[] = & $form->getFormElementByName(trim($fieldName));
      }

      return $fields;
   }

   /**
    * @public
    * @since 2.1
    *
    * Allows you to retrieve all registered validators for this form control added within this form
    * instance. May be used to generate client-side validation rules.
    *
    * @return AbstractFormValidator[] The validators registered for the current control.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.03.2014 (see ID#166)<br />
    */
   public function getValidators() {
      return $this->validators;
   }

   /**
    * @public
    * @since 2.1
    *
    * Allows you to retrieve all registered filters for this form control registered within this form
    * instance. May be used to generate client-side validation rules.
    *
    * @return AbstractFormFilter[] The filters registered for the current control.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.03.2014 (see ID#166)<br />
    */
   public function getFilters() {
      return $this->filters;
   }

}
