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
namespace APF\tools\form;

/**
 * Represents a APF form element (DOM node).
 * <p/>
 * Allows custom implementations to be used with <em>BaseDocumentController::getForm()</em>.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 05.01.2007<br />
 * Version 0.2, 12.01.2007 (Form is now handled as a template)<br />
 * Version 0.3, 13.01.2007 (Added mode taglibs)
 * Version 0.4, 15.01.2007 (Added the "form:multiselect" taglib)<br />
 * Version 0.5, 11.02.2007 (Added the "form:validate" taglib)<br />
 * Version 0.6, 25.06.2007 (Replaced "form:validate" with "form:valgroup")<br />
 * Version 0.7, 14.04.2007 (Added "isSent" attribute)<br />
 * Version 0.8, 22.09.2007 (Added the generic validator)<br />
 * Version 0.9, 01.06.2008 (Added the getFormElementsByType() method)<br />
 * Version 1.0, 16.06.2008 (API change: added getFormElementsByTagName())<br />
 * Version 1.1, 30.12.2009 (Added the form:success tag)<br />
 * Version 1.2, 15.12.2012 (Separated from form_control and refactored tag naming to 1.16 concept)<br />
 */
interface HtmlForm extends FormControlFinder, FormElement {

   const METHOD_ATTRIBUTE_NAME = 'method';
   const METHOD_POST_VALUE_NAME = 'post';
   const METHOD_GET_VALUE_NAME = 'get';

   /**
    * Add a form-control-to-model-mapping to the <em>global</em> list of known mapping expressions.
    * <p />
    * Global (and thus configurable) mappers ensures that future enhancements are made easy and
    * custom form controls can be handled exactly the same way: either implementing
    * <em>FormControl::getValue()</em> to return the "real" (string) value or establish a
    * combination of <em>getValue()</em> returning an object representation as appropriate and a
    * FormControlToModelMapper implementation transforming it into the "real" value to be mapped to the model.
    *
    * @param string $mapper The fully qualified class name of the mapper (e.g. <em>APF\tools\form\mapping\StandardControlToModelMapper</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2016 (ID#275: introduced value data mappers to be able to customize form to model mappings)<br />
    */
   public static function addFormControlToModelMapper(string $mapper);

   /**
    * Use this method to clear the list of form-to-model mappers.
    * <p/>
    * Please don't forget to build up a list of mappers using <em>addFormControlToModelMapper()</em> again. Otherwise,
    * calls to <em>fillModel()</em> will most likely fail or produce inconsistent results.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.04.2016 (ID#275: introduced value data mappers to be able to customize form to model mappings)<br />
    */
   public static function clearFormControlToModelMappers();

   /**
    * Add a model-to-form-control-mapping to the <em>global</em> list of known mapping expressions.
    * <p />
    * Global (and thus configurable) mappers ensures that future enhancements are made easy and
    * custom form controls can be handled exactly the same way: either implementing
    * <em>FormControl::setValue()</em> to inject the "real" (string) value or establish a
    * combination of the model returning an object representation as appropriate and a
    * ModelToFormControlMapper implementation transforming it into the "real" value to be injected
    * into the form control.
    *
    * @param string $mapper The fully qualified class name of the mapper (e.g. <em>APF\tools\form\mapping\StandardModelToFormControlMapper</em>).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.07.2016 (ID#297: introduced form control data mappers to be able to customize model to form mappings)<br />
    */
   public static function addModelToFormControlMapper(string $mapper);

   /**
    * Use this method to clear the list of model-to-form mappers.
    * <p/>
    * Please don't forget to build up a list of mappers using <em>addModelToFormControlMapper()</em> again. Otherwise,
    * calls to <em>fillForm()</em> will most likely fail or produce inconsistent results.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.07.2016 (ID#297: introduced form control data mappers to be able to customize model to form mappings)<br />
    */
   public static function clearModelToFormControlMapper();

   /**
    * Sets the action url of the form.
    *
    * @param string $action The action URL of the form.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    */
   public function setAction($action);

   /**
    * Returns the content of the transformed form.
    *
    * @return string The content of the transformed form.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 20.01.2007 (Changed action attribute handling)<br />
    * Version 0.3, 27.07.2009 (Attribute "name" is not rendered into HTML tag, because of XHTML 1.1 strict)<br />
    */
   public function transformForm();

   /**
    * This method allows you to fill a model/DTO/etc. with the current values of the associated form controls.
    * <p/>
    * For convenience purposes, the properties of the model instance are interpreted as form field names. During
    * mapping, each property name is used to find the associated form control and to retrieve an associated form
    * control's value.
    * <p/>
    * Retrieving values FormControl::getValue() will be invoked on each form control. In case the implementation
    * of your custom form control does not return the "real" value that should be written to the model please
    * register a custom FormControlToModelMapper using HtmlForm::addFormControlToModelMapper() to transform the form control's
    * return value into the value suitable for the model.
    * <p/>
    * The optional mapping list allows to specify a dedicated list of form fields to be filled. This allows
    * re-use of existing models/DTOs/etc. without implementing form models.
    *
    * @param object $model An instance of your DTO/model/etc to be filled with form values.
    * @param array $mapping Optional list of fields to be mapped.
    *
    * @return $this This instance for further usage.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2016 (ID#275: introducing automated form value mapping to models/DTOs)<br />
    */
   public function fillModel($model, array $mapping = []);

   /**
    * This method allows you to fill a form with the current values of a model/DTO/etc.
    * <p/>
    * The optional mapping list allows to specify a dedicated list of form fields to be filled.
    * This allows re-use of existing models/DTOs/etc.
    *
    * @param object $model An instance of your DTO/model/etc to fill the form with.
    * @param array $mapping Optional list of fields to be mapped.
    *
    * @return $this This instance for further usage.
    */
   public function fillForm($model, array $mapping = []);

}
