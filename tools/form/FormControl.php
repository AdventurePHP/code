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
namespace APF\tools\form;

use APF\tools\form\filter\FormFilter;
use APF\tools\form\validator\FormValidator;

/**
 * Defines the basic structure/functionality of an APF form control.
 * <p/>
 * It contains the basic methods the APF form tag needs to operate on.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 27.11.2012 <br />
 */
interface FormControl extends FormElement {

   /**
    * Hides a form control from the HTML output of the form it is contained in. Together with
    * it's <em>dependent controls</em> you can hide entire parts of a form from being displayed
    * on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return FormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function &hide();

   /**
    * Shows a previously hidden form control from the HTML output of the form it is contained in.
    * Together with it's <em>dependent controls</em> you can show entire parts of a form from being
    * displayed on transformation.
    * <p/>
    * This feature can be used to build up forms that display fields that are only displayed
    * at certain conditions evaluated within custom form controls or document controllers.
    *
    * @return FormControl This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function &show();

   /**
    * Convenience method to obtain the form a control is located in. Lays the foundation for
    * recursive form structure and form group support.
    *
    * @return HtmlForm The desired form instance.
    *
    * @throws FormException In case no form can be found within the document tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.08.2014<br />
    */
   public function &getForm();

   /**
    * Applies the given filter to the present input element.
    *
    * @param FormFilter $filter The desired filter.
    *
    * @since 1.11
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    * Version 0.2, 27.03.2014 (Filters are now collected internally to allow retrieval for e.g. client validation rule generation. See ID#166.)<br />
    */
   public function addFilter(FormFilter &$filter);

   /**
    * Executes the given form validator in context of the current form element.
    *
    * @param FormValidator $validator The desired validator.
    *
    * @since 1.11
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.08.2009<br />
    * Version 0.2, 01.11.2010 (Added support for optional validators)<br />
    * Version 0.3, 27.03.2014 (Filters are now collected internally to allow retrieval for e.g. client validation rule generation. See ID#166.)<br />
    * Version 0.4, 05.09.2014 (ID#233: Added support to omit validators for hidden fields)<br />
    */
   public function addValidator(FormValidator &$validator);

   /**
    * Allows you to retrieve all registered validators for this form control added within this form
    * instance. May be used to generate client-side validation rules.
    *
    * @return FormValidator[] The validators registered for the current control.
    *
    * @since 2.1
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.03.2014 (see ID#166)<br />
    */
   public function getValidators();

   /**
    * Allows you to retrieve all registered filters for this form control registered within this form
    * instance. May be used to generate client-side validation rules.
    *
    * @return FormFilter[] The filters registered for the current control.
    *
    * @since 2.1
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.03.2014 (see ID#166)<br />
    */
   public function getFilters();

   /**
    * Allows you to mark this form control as invalid.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.08.2009<br />
    */
   public function &markAsInvalid();

   /**
    * Allows you to mark this form control as valid (again).
    *
    * @return $this This instance for further usage.
    *
    * @since 1.17
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.01.2013<br />
    */
   public function &markAsValid();

   /**
    * Marks a form as sent.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function &markAsSent();

   /**
    * Let's you check, if a radio button was checked.
    *
    * @return boolean True in case the radio button is checked, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function isChecked();

   /**
    * Method for checking the checkbox.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function &check();

   /**
    * Method for un-checking the checkbox.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   public function &uncheck();

   /**
    * Disables a form control for usage.
    *
    * @return $this This instance for further usage.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function &disable();

   /**
    * Enables a form control for user access.
    *
    * @return $this This instance for further usage.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function &enable();

   /**
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
   public function isDisabled();

   /**
    * Sets a form control to read only.
    *
    * @return $this This instance for further usage.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function &setReadOnly();

   /**
    * Enables a form control for write access.
    *
    * @return $this This instance for further usage.
    *
    * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.02.2010<br />
    */
   public function &setReadWrite();

   /**
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
   public function isReadOnly();

   /**
    * Returns the value of the form control. Does not always return the 'value'
    * attribute. This returns the attribute/content which contains the user input.
    * (For example text areas store the input in the content, not in the value
    * attribute)
    *
    * @return mixed The current value or content of the control.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function getValue();

   /**
    * Set's the value of the form control. Should not always set the 'value'
    * attribute. This set's the same attribute/content as the user would type it.
    *
    * @param string $value The value to set.
    *
    * @return $this This instance for further usage.
    *
    * @since 1.14
    *
    * @author Ralf Schubert
    * @version
    * Version 0.1, 26.07.2011<br />
    */
   public function &setValue($value);

   /**
    * Returns the current control's visibility status.
    *
    * @return bool True in case the control is visible, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 16.12.2012<br />
    */
   public function isVisible();

   /**
    * Let's check if something was selected in form:select or form:multiselect.
    *
    * @return bool True in case the control is selected, false otherwise.
    * @since 1.15
    *
    * @author dave
    * @version
    * Version 0.1, 22.09.2011<br />
    */
   public function isSelected();

   /**
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
   public function isFilled();

   /**
    * Safely appends a css class. Resolves missing attribute.
    *
    * @param string $class The css class to append.
    *
    * @return $this This instance for further usage.
    *
    * @since 1.14
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.02.2010<br />
    */
   public function &appendCssClass($class);

   /**
    * Adds an additional attribute to the white list of the control.
    *
    * @param string $name The attribute which should be added to the white list.
    *
    * @return $this This instance for further usage.
    *
    * @since 1.13
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.07.2010<br />
    */
   public function &addAttributeToWhiteList($name);

   /**
    * Adds a set of additional attributes to the white list of the control.
    *
    * @param string[] $names The attributes to add to the white list.
    *
    * @return $this This instance for further usage.
    *
    * @since 1.13
    * @author Ralf Schubert
    * @version
    * Version 0.1, 21.07.2010<br />
    */
   public function &addAttributesToWhiteList(array $names);

   /**
    * Define this field as optional. This allows to exclude it from validation e.g. from within a controller.
    *
    * @return $this This instance for further usage.
    *
    * @since 3.2
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.08.2016<br />
    */
   public function &setOptional();

   /**
    * Let's you determine whether or not a field is optional (regarding validation).
    *
    * @return bool True in case the field is mandatory, false otherwise.
    *
    * @since 3.2
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.08.2016<br />
    */
   public function isOptional();

   /**
    * Define this field as mandatory. This allows to include it from validation e.g. from within a controller.
    *
    * @return $this This instance for further usage.
    *
    * @since 3.2
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.08.2016<br />
    */
   public function &setMandatory();

   /**
    * Let's you determine whether or not a field is mandatory (regarding validation).
    *
    * @return bool True in case the field is mandatory, false otherwise.
    *
    * @since 3.2
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.08.2016<br />
    */
   public function isMandatory();

}
