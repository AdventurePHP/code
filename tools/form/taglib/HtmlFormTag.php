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

use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\XmlParser;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use APF\tools\form\FormControl;
use APF\tools\form\FormControlToModelMapper;
use APF\tools\form\FormException;
use APF\tools\form\HtmlForm;
use APF\tools\form\mixin\FormControlFinder as FormControlFinderImpl;
use APF\tools\form\ModelToFormControlMapper;
use APF\tools\link\Url;
use ReflectionClass;

/**
 * Represents a APF form element (DOM node).
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
class HtmlFormTag extends Document implements HtmlForm {

   use FormControlFinderImpl;

   const ACTION_ATTRIBUTE_NAME = 'action';
   const SUBMIT_ACTION_URL_PARAMS_ATTRIBUTE_NAME = 'submit-action-url-params';

   /**
    * @var string[] List of form control to model mappers (fully qualified class name e.g. <em>APF\tools\form\mapping\StandardControlToModelMapper</em>).
    */
   protected static $formToModelMappers = [];

   /**
    * @var string[] List of form control to model mappers (fully qualified class name e.g. <em>APF\tools\form\mapping\StandardModelToFormControlMapper</em>).
    */
   protected static $modelToFormMappers = [];

   /**
    * Indicates, whether the form should be transformed at it'd place of definition or not.
    *
    * @var boolean $transformOnPlace
    */
   protected $transformOnPlace = false;

   /**
    * The attributes, that are allowed to render into the XHTML/1.1 strict document.
    *
    * @var string[] $attributeWhiteList
    */
   protected $attributeWhiteList = [];

   /**
    * Initializes the form.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 13.01.2007<br />
    * Version 0.3, 15.01.2007 (Added the form:multiselect tag)<br />
    * Version 0.4, 11.02.2007 (Added the form:validate tag)<br />
    * Version 0.5, 03.03.2007 (Removed the "&" before the "new" operator)<br />
    * Version 0.6, 25.03.2007 (Added the form:valgroup tag)<br />
    * Version 0.7, 22.09.2007 (Added the generic validator tag)<br />
    * Version 0.8, 06.11.2007 (Added the form:getstring tag)<br />
    * Version 0.9, 11.07.2008 (Added the form:addtaglib tag)<br />
    * Version 1.0, 03.09.2008 (Added the form:marker tag)<br />
    * Version 1.1, 22.06.2009 (Added the form:reset tag)<br />
    * Version 1.2, 03.09.2009 (Added several new tags concerning the refactoring)<br />
    * Version 1.3, 15.06.2010 (Bug-fix: white listing did not recognize encrypt attribute)<br />
    */
   public function __construct() {
      // setup attributes within white-list
      $this->attributeWhiteList = array_merge(AbstractFormControl::$CORE_ATTRIBUTES, AbstractFormControl::$EVENT_ATTRIBUTES, AbstractFormControl::$I18N_ATTRIBUTES);
      $this->attributeWhiteList[] = self::METHOD_ATTRIBUTE_NAME;
      $this->attributeWhiteList[] = self::ACTION_ATTRIBUTE_NAME;
      $this->attributeWhiteList[] = 'name'; // allowed with HTML5 again (see http://www.w3.org/html/wg/drafts/html/master/forms.html#attr-form-name)
      $this->attributeWhiteList[] = 'enctype';
      $this->attributeWhiteList[] = 'onsubmit';
      $this->attributeWhiteList[] = 'onreset';
      $this->attributeWhiteList[] = 'accept';
      $this->attributeWhiteList[] = 'accept-charset'; // to explicitly specify an encoding
      $this->attributeWhiteList[] = 'autocomplete'; // to disable form auto-completion for browsers supporting this security feature
      $this->attributeWhiteList[] = 'target';
   }

   public static function addFormControlToModelMapper($mapper) {
      self::$formToModelMappers[] = $mapper;
   }

   public static function clearFormControlToModelMappers() {
      self::$formToModelMappers = [];
   }

   public static function addModelToFormControlMapper($mapper) {
      self::$modelToFormMappers[] = $mapper;
   }

   public static function clearModelToFormControlMapper() {
      self::$modelToFormMappers = [];
   }

   public function onParseTime() {

      // add default method for convenience
      $method = $this->getAttribute(self::METHOD_ATTRIBUTE_NAME);
      if ($method === null) {
         $this->setAttribute(
               self::METHOD_ATTRIBUTE_NAME,
               strtolower(
                     Registry::retrieve('APF\tools', 'FormDefaultMethod', self::METHOD_POST_VALUE_NAME)
               )
         );
      }

      $this->extractTagLibTags();
   }

   public function isSent() {

      foreach ($this->children as &$child) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($child instanceof FormControl) {
            if ($child->isSent() === true) {
               return true;
            }
         }
      }

      return false;
   }

   // TODO give it another try to refactor validation to isValid() rather than direct validation per addValidator() to streamline API
   public function isValid() {

      foreach ($this->children as &$child) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($child instanceof FormControl) {
            if ($child->isValid() === false) {
               return false;
            }
         }
      }

      return true;
   }

   public function reset() {
      foreach ($this->children as &$child) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($child instanceof FormControl) {
            $child->reset();
         }
      }
   }

   /**
    * Adds a new form element at the end of the form. This method is intended to dynamically generate forms.
    *
    * @param string $elementType Type of the element (e.g. "form:text")
    * @param string[] $elementAttributes Associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    *
    * @return FormControl The new form object or null.
    * @throws FormException In case the form element cannot be added.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 05.09.2008 (The new form element now gets the current context and language)<br />
    * Version 0.3, 06.09.2008 (API change: now the tag name (e.g. "form:text") is expected as an argument)<br />
    * Version 0.4, 10.09.2008 (Added the $ElementAttributes param)<br />
    * Version 0.5, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    */
   public function &addFormElement($elementType, array $elementAttributes = []) {

      // create form element
      $control = $this->createFormElement($this, $elementType, $elementAttributes);

      if ($control === null) {
         // notify developer that object creation failed
         throw new FormException('[HtmlFormTag::addFormElement()] Form element "'
               . $elementType . '" cannot be added due to previous errors!');
      }

      // add position place holder to the content
      $this->content .= '<' . $control->getObjectId() . ' />';

      return $control;
   }

   /**
    * Adds a new form element to the child list.
    *
    * @param DomNode $parent The parent document to create the object in.
    * @param string $elementType Type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    *
    * @return FormControl The created form element.
    * @throws FormException In case form element cannot be found.
    *
    * @author Christian Achatz, Danil Mihajluk
    * @version
    * Version 0.1, 06.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $elementAttributes param)<br />
    * Version 0.3, 12.11.2008 (Bug-fix: language and context initialisation were wrong)<br />
    * Version 0.4, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    * Version 0.5, 03.12.2015 (ID#279: changed code to rely on interface type rather than internal structure by Danil Mihajluk)<br />
    */
   protected function &createFormElement(DomNode &$parent, $elementType, array $elementAttributes = []) {

      $class = $this->getTagClass($elementType);
      if ($class === null) {
         return null;
      }

      // generate object id
      $objectId = XmlParser::generateUniqID();

      // create new form element
      /* @var FormControl $child */
      $child = new $class();

      // add standard and user defined attributes
      $child->setObjectId($objectId);
      $child->setLanguage($this->getLanguage());
      $child->setContext($this->getContext());
      $child->setAttributes($elementAttributes);

      // add form element to DOM tree and call the onParseTime() method
      $child->setParentObject($parent);
      $child->onParseTime();

      // call the onAfterAppend() method
      $child->onAfterAppend();

      // add child to list
      $parent->getChildren()[$objectId] = $child;

      return $child;
   }

   /**
    * Returns the name of the tag implementation that refers to the applied tag name.
    *
    * @param string $tagName The name of the tag (e.g. form:listener).
    *
    * @return string The name of the tag implementation class.
    * @throws FormException In case the referred tag name is not registered within the current form.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.12.2012<br />
    * Version 0.2, 20.08.2014 (ID#198: Method is now public to be used within all FormControlFinder implementations)<br />
    */
   public function getTagClass($tagName) {

      // gather tag implementation and return in case there is nothing defined
      $colon = strpos($tagName, ':');
      $prefix = substr($tagName, 0, $colon);
      $name = substr($tagName, $colon + 1);

      $class = $this->getTagLibClass($prefix, $name);
      if ($class === null) {
         $parent = $this->getParentObject();
         $documentController = get_class($parent->getDocumentController());
         throw new FormException('[HtmlFormTag::getTagClass()] No tag with name "' . $tagName
               . '" registered in form with name "' . $this->getAttribute('name') . '" in document controller '
               . $documentController . '!', E_USER_ERROR);
      }

      return $class;

   }

   /**
    * Adds content at the end of the form. This method is intended to dynamically generate forms.
    *
    * @param string $content The desired content
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.01.2007<br />
    */
   public function addFormContent($content) {
      $this->content .= $content;
   }

   /**
    * Adds content in front of a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $content the content to add
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2008<br />
    * Version 0.2, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    */
   public function addFormContentBeforeMarker($markerName, $content) {
      $this->getMarker($markerName)->addContentBefore($content);
   }

   /**
    * Adds content behind a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $content the content to add
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    * Version 0.2, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    */
   public function addFormContentAfterMarker($markerName, $content) {
      $this->getMarker($markerName)->addContentAfter($content);
   }

   /**
    * Adds a new form element in front of a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $elementType type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    *
    * @return FormControl The new form object or null (e.g. for addressing the new element).
    * @throws FormException In case object creation fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $elementAttributes param)<br />
    * Version 0.3, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    */
   public function &addFormElementBeforeMarker($markerName, $elementType, array $elementAttributes = []) {

      $marker = $this->getMarker($markerName);
      $control = $this->createFormElement($marker->getParentObject(), $elementType, $elementAttributes);

      if ($control === null) {
         // notify developer that object creation failed
         throw new FormException('[HtmlFormTag::addFormElementBeforeMarker()] Form element "'
               . $elementType . '" cannot be added due to previous errors!');
      }

      // add the position place holder to the content
      $markerId = $marker->getObjectId();
      $parent = $marker->getParentObject();

      $parent->setContent(str_replace(
            '<' . $markerId . ' />',
            '<' . $control->getObjectId() . ' /><' . $markerId . ' />',
            $parent->getContent()
      ));

      return $control;
   }

   /**
    * Adds a new form element after a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $elementType type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    *
    * @return FormControl The new form object or null (e.g. for addressing the new element).
    * @throws FormException In case object creation fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
    * Version 0.3, 23.08.2014 (ID#198: added unlimited form control nesting capability)<br />
    */
   public function &addFormElementAfterMarker($markerName, $elementType, array $elementAttributes = []) {

      $marker = $this->getMarker($markerName);
      $control = $this->createFormElement($marker->getParentObject(), $elementType, $elementAttributes);

      if ($control === null) {
         // notify developer that object creation failed
         throw new FormException('[HtmlFormTag::addFormElementBeforeMarker()] Form element "'
               . $elementType . '" cannot be added due to previous errors!');
      }

      // add the position place holder to the content
      $markerId = $marker->getObjectId();
      $parent = $marker->getParentObject();

      $parent->setContent(str_replace(
            '<' . $markerId . ' />',
            '<' . $markerId . ' /><' . $control->getObjectId() . ' />',
            $parent->getContent()
      ));

      return $control;
   }

   public function setAction($action) {
      $this->setAttribute(self::ACTION_ATTRIBUTE_NAME, $action);
   }

   /**
    * Adds an additional attribute to the white list of the form.
    *
    * @param string $name The attribute which should be added to the white list.
    *
    * @since 2.0
    *
    * @author Ralf Schubert, Christian Achatz
    * @version
    * Version 0.1, 21.07.2010<br />
    * Version 0.2, 08.06.2013 (Re-introduced white-list modification for form tag)<br />
    */
   public function addAttributeToWhitelist($name) {
      $this->attributeWhiteList[] = $name;
   }

   /**
    * Defines, whether the form should be transformed at the definition place.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 01.06.2008<br />
    */
   public function transformOnPlace() {
      $this->transformOnPlace = true;
   }

   /**
    * Re-implements the {@link transform} method for the form taglib.
    *
    * @return string The content of the form (in case of transformOnPlace) or an empty string.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 12.01.2007<br />
    * Version 0.2, 01.06.2008 (Added the transformOnPlace() feature)<br />
    */
   public function transform() {

      // to transformation on place if desired
      if ($this->transformOnPlace === true) {
         return $this->transformForm();
      }

      return '';
   }

   public function transformForm() {

      $t = Singleton::getInstance(BenchmarkTimer::class);
      /* @var $t BenchmarkTimer */
      $id = '(HtmlFormTag) ' . $this->getObjectId() . '::transformForm()';
      $t->start($id);

      // add action attribute if not set
      $action = $this->getAttribute(self::ACTION_ATTRIBUTE_NAME);
      if ($action === null) {
         $this->setAttribute(self::ACTION_ATTRIBUTE_NAME, $this->getRequest()->getRequestUri());
      }

      // ID#239: always encode action attribute when present to secure custom actions as well as auto-generated ones
      $this->setAttribute(
            self::ACTION_ATTRIBUTE_NAME,
            htmlspecialchars(
                  $this->getAttribute(self::ACTION_ATTRIBUTE_NAME),
                  ENT_QUOTES,
                  Registry::retrieve('APF\core', 'Charset'),
                  false
            )
      );

      // transform the form including all child tags
      $htmlCode = (string) '<form ';
      $htmlCode .= $this->getAttributesAsString($this->attributes, $this->attributeWhiteList);
      $htmlCode .= '>';

      // ID#281: add hidden form fields with URL get parameters for GET forms for convenience reasons
      if ($this->getAttribute(self::METHOD_ATTRIBUTE_NAME) === self::METHOD_GET_VALUE_NAME
            && $this->getAttribute(self::SUBMIT_ACTION_URL_PARAMS_ATTRIBUTE_NAME) === 'true'
      ) {

         $url = Url::fromString($this->getAttribute(self::ACTION_ATTRIBUTE_NAME));
         $queryParams = $url->getQuery();

         if (count($queryParams) > 0) {
            $hiddenFieldMarker = '';

            foreach ($queryParams as $name => $value) {
               $control = $this->createFormElement($this, 'form:hidden', ['name' => $name, 'value' => $value]);
               $hiddenFieldMarker .= '<' . $control->getObjectId() . ' />';
            }

            // prepend fields to preserve parameter order
            $this->content = $hiddenFieldMarker . $this->content;
         }

      }

      if (count($this->children) > 0) {

         foreach ($this->children as &$child) {
            $childId = '(' . get_class($child) . ') ' . $child->getObjectId() . '::transform()';
            $t->start($childId);

            $this->content = str_replace('<' . $child->getObjectId() . ' />',
                  $child->transform(), $this->content);

            $t->stop($childId);
         }
      }

      $htmlCode .= $this->content;
      $htmlCode .= '</form>';

      $t->stop($id);

      return $htmlCode;
   }

   public function fillModel(&$model, array $mapping = []) {

      $class = new ReflectionClass($model);
      $properties = $class->getProperties();

      // Gathering model properties to fill via reflection is very convenient. However, not all properties are
      // potentially intended to be form properties!
      // For this reason, parameter $mapping allows to restrict the list of model properties that will be
      // treated as form control pendants (white list approach).
      foreach ($properties as $property) {

         // Only map properties that should be mapped to be able to re-use existing models!
         if (empty($mapping) || in_array($property->getName(), $mapping)) {
            try {
               // otherwise setValue() will fail...
               $property->setAccessible(true);

               $control = $this->getFormElementByName($property->getName());

               // Map form value(s) to model using configurable/exchangeable mappers. For details
               // on the implementation pattern/idea see HtmlForm::addFormControlToModelMapper().
               $value = null;
               foreach (self::$formToModelMappers as $mapper) {
                  /* @var $mapper FormControlToModelMapper */
                  if ($mapper::applies($control)) {
                     $value = $mapper::getValue($control);
                  }
               }

               $property->setValue($model, $value);
               $property->setAccessible(false);
            } catch (FormException $e) {
               // In case a form control does not exist, ignore mapping attempt. This is because not all model
               // properties might be represented by form controls, dynamic form fields have not been added
               // in certain use cases and are not present ATM, or the model is used for a multi-step workflow
               // with just certain fields present in each step.
               continue;
            }
         }
      }

      return $this;
   }

   public function fillForm($model, array $mapping = []) {

      $class = new ReflectionClass($model);
      $properties = $class->getProperties();

      // Gathering model properties to fill via reflection is very convenient. However, not all properties are
      // potentially intended to be form properties!
      // For this reason, parameter $mapping allows to restrict the list of model properties that will be
      // treated as form control pendants (white list approach).
      foreach ($properties as $property) {

         // Only map properties that should be mapped to be able to re-use existing models!
         if (empty($mapping) || in_array($property->getName(), $mapping)) {
            try {

               // get form controls by property name (multiple files for e.g. check boxes)
               $controls = $this->getFormElementsByName($property->getName());

               // otherwise getValue() will fail...
               $property->setAccessible(true);

               // Map form value(s) to model using configurable/exchangeable mappers. For details
               // on the implementation pattern/idea see HtmlForm::addFormControlToModelMapper().
               $value = $property->getValue($model);

               // loop through list of e.g. check boxes
               foreach ($controls as &$control) {
                  foreach (self::$modelToFormMappers as $mapper) {
                     /* @var $mapper ModelToFormControlMapper */
                     if ($mapper::applies($control)) {
                        $mapper::setValue($control, $value);
                     }
                  }
               }

               $property->setAccessible(false);
            } catch (FormException $e) {
               // In case a form control does not exist, ignore mapping attempt. This is because not all model
               // properties might be represented by form controls, dynamic form fields have not been added
               // in certain use cases and are not present ATM, or the model is used for a multi-step workflow
               // with just certain fields present in each step.
               continue;
            }
         }
      }

      return $this;
   }

}
