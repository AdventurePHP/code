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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\LanguageLabelTag;
use APF\core\pagecontroller\TagLib;
use APF\core\pagecontroller\XmlParser;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;
use APF\tools\form\FormException;

/**
 * @package APF\tools\form\taglib
 * @class HtmlFormTag
 *
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
class HtmlFormTag extends Document {

   public static $METHOD_ATTRIBUTE_NAME = 'method';
   public static $METHOD_POST_VALUE_NAME = 'post';

   /**
    * @var boolean Indicates, whether the form should be transformed at it'd place of definition or not.
    */
   protected $transformOnPlace = false;

   /**
    * @public
    *
    * Initializes the known taglibs.
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

      // Place the listener here, to ensure, that it is there, when the
      // notification is sent!
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\ValidationListenerTag', 'form', 'listener');

      // Please note, that the form:addfilter taglib is placed before the
      // form:addvalidator, because filtering must take place before the
      // validation. Otherwise, we might get unexpected results.
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\AddFormControlFilterTag', 'form', 'addfilter');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\AddFormControlValidatorTag', 'form', 'addvalidator');

      // The time of adding the form errors is not relevant, because the action
      // takes place on transform time. But for clarity, we add it near the listeners.
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\FormErrorDisplayTag', 'form', 'error');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\FormSuccessDisplayTag', 'form', 'success');

      // Buttons are analyzed right early to be able to initialize form controls
      // concerning the status of the form (e.g. sent!).
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\ButtonTag', 'form', 'button');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\ResetButtonTag', 'form', 'reset');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\ImageButtonTag', 'form', 'imagebutton');

      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'form', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'form', 'getstring');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'form', 'addtaglib');

      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\FormLabelTag', 'form', 'label');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\TextFieldTag', 'form', 'text');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\SelectBoxTag', 'form', 'select');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\DateSelectorTag', 'form', 'date');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\PasswordFieldTag', 'form', 'password');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\HiddenFieldTag', 'form', 'hidden');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\CheckBoxTag', 'form', 'checkbox');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\RadioButtonTag', 'form', 'radio');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\FileUploadTag', 'form', 'file');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\TextAreaTag', 'form', 'area');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\MultiSelectBoxTag', 'form', 'multiselect');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\DynamicFormElementMarkerTag', 'form', 'marker');

      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\TimeCaptchaTag', 'form', 'timecaptcha');
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\CsrfProtectionHashTag', 'form', 'csrfhash');

      // analyzing the form:time tag must be done after the form:timecaptcha, since
      // the tag parser analyzes the whole tag string and form:time is fully contained
      // in form:timecaptcha. this restriction of the APF parser is accepted due to
      // performance reasons!
      $this->tagLibs[] = new TagLib('APF\tools\form\taglib\TimeSelectorTag', 'form', 'time');

      // setup attributes within white-list
      $this->attributeWhiteList = array_merge(AbstractFormControl::$CORE_ATTRIBUTES, AbstractFormControl::$EVENT_ATTRIBUTES, AbstractFormControl::$I18N_ATTRIBUTES);
      $this->attributeWhiteList[] = self::$METHOD_ATTRIBUTE_NAME;
      $this->attributeWhiteList[] = 'action';
      $this->attributeWhiteList[] = 'name'; // allowed with HTML5 again (see http://www.w3.org/html/wg/drafts/html/master/forms.html#attr-form-name)
      $this->attributeWhiteList[] = 'enctype';
      $this->attributeWhiteList[] = 'onsubmit';
      $this->attributeWhiteList[] = 'onreset';
      $this->attributeWhiteList[] = 'accept';
      $this->attributeWhiteList[] = 'accept-charset'; // to explicitly specify an encoding
      $this->attributeWhiteList[] = 'autocomplete'; // to disable form auto-completion for browsers supporting this security feature
      $this->attributeWhiteList[] = 'target';
   }

   public function onParseTime() {

      // add default method for convenience
      $method = $this->getAttribute(self::$METHOD_ATTRIBUTE_NAME);
      if ($method === null) {
         $this->setAttribute(
            self::$METHOD_ATTRIBUTE_NAME,
            strtolower(
               Registry::retrieve('APF\tools', 'FormDefaultMethod', self::$METHOD_POST_VALUE_NAME)
            )
         );
      }

      $this->extractTagLibTags();
   }

   /**
    * @public
    *
    * Indicates, whether the form has been sent or not. Retrieves the status
    * directly from the form controls. Overwrites the parent's method.
    *
    * @return boolean True, in case the form is sent, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function isSent() {

      foreach ($this->children as $objectId => $DUMMY) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($this->children[$objectId] instanceof FormControl) {
            if ($this->children[$objectId]->isSent() === true) {
               return true;
            }
         }
      }

      return false;
   }

   /**
    * @public
    *
    * Indicates, whether the form is valid or not. Retrieves the status
    * directly from the form controls. Overwrites the parent's method.
    *
    * @return boolean True, in case the form is valid, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   public function isValid() {

      foreach ($this->children as $objectId => $DUMMY) {
         // Only include real form elements to avoid unnecessary
         // implementation overhead for elements that just want to
         // be used within forms but do not act as form elements!
         // See http://forum.adventure-php-framework.org/viewtopic.php?f=6&t=1387
         // for details.
         if ($this->children[$objectId] instanceof FormControl) {
            if ($this->children[$objectId]->isValid() === false) {
               return false;
            }
         }
      }

      return true;
   }

   /**
    * @public
    *
    * Adds a new form element at the end of the form. This method is intended to dynamically generate forms.
    *
    * @param string $elementType Type of the element (e.g. "form:text")
    * @param string[] $elementAttributes Associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    * @return string Id of the new form object or null (e.g. for addressing the new element)
    * @throws FormException In case the form element cannot be added.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 05.09.2008 (The new form element now gets the current context and language)<br />
    * Version 0.3, 06.09.2008 (API change: now the tag name (e.g. "form:text") is expected as an argument)<br />
    * Version 0.4, 10.09.2008 (Added the $ElementAttributes param)<br />
    */
   public function addFormElement($elementType, array $elementAttributes = array()) {

      // create form element
      $objectId = $this->createFormElement($elementType, $elementAttributes);

      // add form element if id is not null
      if ($objectId !== null) {

         // add position place holder to the content
         $this->content .= '<' . $objectId . ' />';

         // return object id of the new form element
         return $objectId;
      }

      throw new FormException('[HtmlFormTag::addFormElement()] Form element "' . $elementType
         . '" cannot be added due to previous errors!');
   }

   /**
    * @public
    *
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
    * @public
    *
    * Adds content in front of a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $content the content to add
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2008<br />
    */
   public function addFormContentBeforeMarker($markerName, $content) {

      // get desired marker
      $marker = & $this->getMarker($markerName);

      // get the object id
      $objectId = $marker->getObjectId();

      // add the desired content before the marker
      $this->content = str_replace('<' . $objectId . ' />', $content . '<' . $objectId . ' />', $this->content);
   }

   /**
    * @public
    *
    * Adds content behind a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $content the content to add
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    */
   public function addFormContentAfterMarker($markerName, $content) {

      // get desired marker
      $marker = & $this->getMarker($markerName);

      // get the object id
      $objectId = $marker->getObjectId();

      // add the desired content before the marker
      $this->content = str_replace('<' . $objectId . ' />', '<' . $objectId . ' />' . $content, $this->content);
   }

   /**
    * @public
    *
    * Adds a new form element in front of a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $elementType type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    * @return string Id of the new form object or null (e.g. for addressing the new element)
    * @throws FormException In case object creation fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $elementAttributes param)<br />
    */
   public function addFormElementBeforeMarker($markerName, $elementType, array $elementAttributes = array()) {

      // create new form element
      $objectId = $this->createFormElement($elementType, $elementAttributes);

      // add form element if id is not null
      if ($objectId !== null) {

         // get desired marker
         $marker = & $this->getMarker($markerName);

         // add the position place holder to the content
         $markerId = $marker->getObjectId();
         $this->content = str_replace('<' . $markerId . ' />', '<' . $objectId . ' /><' . $markerId . ' />', $this->content);

         // return object id of the new form element
         return $objectId;
      }

      // notify developer that object creation failed
      throw new FormException('[HtmlFormTag::addFormElementBeforeMarker()] Form element "'
         . $elementType . '" cannot be added due to previous errors!');
   }

   /**
    * @public
    *
    * Adds a new form element after a form marker. This method is intended to dynamically generate forms.
    *
    * @param string $markerName the desired marker name
    * @param string $elementType type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    * @return string Id of the new form object or null (e.g. for addressing the new element)
    * @throws FormException In case object creation fails.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
    */
   public function addFormElementAfterMarker($markerName, $elementType, array $elementAttributes = array()) {

      // create new form element
      $objectId = $this->createFormElement($elementType, $elementAttributes);

      // add form element if id is not null
      if ($objectId !== null) {

         // get desired marker
         $marker = & $this->getMarker($markerName);

         // add the position place holder to the content
         $markerId = $marker->getObjectId();
         $this->content = str_replace(
            '<' . $markerId . ' />', '<' . $markerId . ' /><' . $objectId . ' />',
            $this->content
         );

         // return object id of the new form element
         return $objectId;
      }

      // notify developer that object creation failed
      throw new FormException('[HtmlFormTag::addFormElementBeforeMarker()] Form element "' . $elementType . '" cannot be added due to previous errors!');
   }

   /**
    * @protected
    *
    * Adds a new form element to the child list.
    *
    * @param string $elementType Type of the element (e.g. "form:text")
    * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
    * @return string Id of the new form object (e.g. for addressing the new element)
    * @throws FormException In case form element cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.09.2008<br />
    * Version 0.2, 10.09.2008 (Added the $elementAttributes param)<br />
    * Version 0.3, 12.11.2008 (Bug-fix: language and context initialisation were wrong)<br />
    */
   protected function createFormElement($elementType, array $elementAttributes = array()) {

      // define taglib class
      $colon = strpos($elementType, ':');
      $prefix = substr($elementType, 0, $colon);
      $name = substr($elementType, $colon + 1);

      // lazily import APF-internal taglib class. this is necessary, since taglibs are
      // not statically included as of 1.14 but loaded dynamically due to performance
      // reasons!
      $class = null;
      foreach ($this->tagLibs as $taglib) {
         /* @var $taglib TagLib */
         if ($taglib->getPrefix() === $prefix && $taglib->getName() === $name) {
            $class = $taglib->getClass();
            break;
         }
      }

      // generate object id
      $objectId = XmlParser::generateUniqID();

      // create new form element
      $formControl = new $class();
      /* @var $formControl AbstractFormControl */

      // add standard and user defined attributes
      $formControl->setObjectId($objectId);
      $formControl->setLanguage($this->language);
      $formControl->setContext($this->context);
      $formControl->setAttributes($elementAttributes);

      // add form element to DOM tree and call the onParseTime() method
      $formControl->setParentObject($this);
      $formControl->onParseTime();

      // add new form element to children list
      $this->children[$objectId] = $formControl;

      // call the onAfterAppend() method
      $this->children[$objectId]->onAfterAppend();

      // return object id for further addressing
      return $objectId;

   }

   /**
    * @protected
    *
    * Returns a reference on the desired marker or null.
    *
    * @param string $markerName The desired marker's name.
    * @return DynamicFormElementMarkerTag The marker.
    * @throws FormException In case the marker cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2008<br />
    */
   protected function &getMarker($markerName) {
      try {
         return $this->getChildNode('name', $markerName, 'APF\tools\form\taglib\DynamicFormElementMarkerTag');
      } catch (\InvalidArgumentException $e) {
         throw new FormException('[HtmlFormTag::addFormContentAfterMarker()] No marker object '
            . 'with name "' . $markerName . '" composed in current form for document controller "'
            . ($this->getParentObject()->getDocumentController()) . '"! Please check the definition of '
            . 'the form with name "' . $this->getAttribute('name') . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * @public
    *
    * Sets the action url of the form.
    *
    * @param string $action The action URL of the form.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    */
   public function setAction($action) {
      $this->setAttribute('action', $action);
   }

   /**
    * @public
    * @since 2.0
    *
    * Adds an additional attribute to the white list of the form.
    *
    * @param string $name The attribute which should be added to the white list.
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
    * @public
    *
    * Returns a reference on the form element identified by the given name.
    *
    * @param string $name The name of the desired form element.
    * @return AbstractFormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.09.2009 (Corrected debug message)<br />
    */
   public function &getFormElementByName($name) {

      if (count($this->children) > 0) {
         foreach ($this->children as $objectId => $DUMMY) {
            if ($this->children[$objectId]->getAttribute('name') == $name) {
               return $this->children[$objectId];
            }
         }
      }

      // display extended debug message in case no form element was found
      $parent = & $this->getParentObject();
      $docCon = $parent->getDocumentController();
      throw new FormException('[HtmlFormTag::getFormElementByName()] No form element with name "'
         . $name . '" composed in current form "' . $this->getAttribute('name')
         . '" in document controller "' . $docCon . '". Please double-check your taglib definitions '
         . 'within this form (especially attributes, that are used for referencing other form '
         . 'controls)!', E_USER_ERROR);
   }

   /**
    * @public
    *
    * Returns a list of form controls with the given name.
    *
    * @param string $name The name of the form elements to collect (e.g. for radio buttons).
    * @return AbstractFormControl[] The list of form controls with the given name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.08.2010<br />
    */
   public function &getFormElementsByName($name) {
      $elements = array();
      if (count($this->children) > 0) {
         foreach ($this->children as $objectId => $DUMMY) {
            if ($this->children[$objectId]->getAttribute('name') == $name) {
               $elements[] = & $this->children[$objectId];
            }
         }
      }
      return $elements;
   }

   /**
    * @public
    *
    * Returns a reference on the form element identified by the given id.
    *
    * @param string $id The ID of the desired form element.
    * @return AbstractFormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 21.01.2007<br />
    */
   public function &getFormElementByID($id) {

      if (count($this->children) > 0) {
         foreach ($this->children as $objectId => $DUMMY) {
            if ($this->children[$objectId]->getAttribute('id') == $id) {
               return $this->children[$objectId];
            }
         }
      }

      // display extended debug message in case no form element was found
      $parent = & $this->getParentObject();
      $documentController = $parent->getDocumentController();
      throw new FormException('[HtmlFormTag::getFormElementByID()] No form element with id "'
         . $id . '" composed in current form "' . $this->getAttribute('name')
         . '" in document controller "' . $documentController . '"!', E_USER_ERROR);
   }

   /**
    * @public
    *
    * Returns a reference on a form element addressed by it's internal object id.
    *
    * @param string $objectId The object id of of the desired form element.
    * @return AbstractFormControl A reference on the form element.
    * @throws FormException In case the form element cannot be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.01.2007 (Corrected error message)<br />
    * Version 0.3, 06.09.2008 (Corrected error message again)<br />
    */
   public function &getFormElementByObjectID($objectId) {

      if (isset($this->children[$objectId])) {
         return $this->children[$objectId];
      }

      // note, that no suitable child has been found
      $parent = & $this->getParentObject();
      $documentController = $parent->getDocumentController();
      throw new FormException('[HtmlFormTag::getFormElementByObjectID()] No form element with id "'
         . $objectId . '" composed in current form "' . $this->getAttribute('name')
         . '" in document controller "' . $documentController . '"!', E_USER_ERROR);
   }

   /**
    * @public
    *
    * Returns a list of form elements addressed by their tag name.
    *
    * @param string $tagName The tag name of the desired form element (e.g. "form:text").
    * @return AbstractFormControl[] A list of references on the form elements.
    * @throws FormException In case the form element cannot be found or desired tag is not registered.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 14.06.2008 (API change: do use this function instead of getFormElementsByType()!)<br />
    * Version 0.2, 12.12.2012 (Refactoring due to tag renaming)<br />
    */
   public function &getFormElementsByTagName($tagName) {

      $tagClassName = $this->getTagClass($tagName);

      if (count($this->children) > 0) {

         $formElements = array();
         foreach ($this->children as $objectId => $DUMMY) {

            if ($this->children[$objectId] instanceof $tagClassName) {
               $formElements[] = & $this->children[$objectId];
            }
         }

         return $formElements;
      }

      // display extended debug message in case no form elements were found
      $parent = & $this->getParentObject();
      $documentController = $parent->getDocumentController();
      throw new FormException('[HtmlFormTag::getFormElementsByType()] No form elements composed in ' .
         'current form "' . $this->getAttribute('name') . '" in document controller "'
         . $documentController . '"!', E_USER_ERROR);
   }

   /**
    * @private
    *
    * Returns the name of the tag implementation that refers to the applied tag name.
    *
    * @param string $tagName The name of the tag (e.g. form:listener).
    * @return string The name of the tag implementation class.
    * @throws FormException In case the referred tag name is not registered within the current form.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.12.2012<br />
    */
   private function getTagClass($tagName) {
      foreach ($this->tagLibs as $tagLib) {
         /* @var $tagLib TagLib */
         if ($tagLib->getPrefix() . ':' . $tagLib->getName() == $tagName) {
            return $tagLib->getClass();
         }
      }

      $parent = & $this->getParentObject();
      $documentController = $parent->getDocumentController();
      throw new FormException('[HtmlFormTag::getTagClass()] No tag with name "' . $tagName
         . '" registered in form with name "' . $this->getAttribute('name') . '" in document controller '
         . $documentController . '!', E_USER_ERROR);
   }

   /**
    * @public
    *
    * Let's you retrieve an &lt;form:getstring /&gt; tag instance with the specified name.
    *
    * @param string $name The name of the form label to return.
    * @return LanguageLabelTag The instance of the desired label.
    * @throws \InvalidArgumentException In case no label can be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.01.2012<br />
    */
   public function &getLabel($name) {
      try {
         return $this->getChildNode('name', $name, 'APF\core\pagecontroller\LanguageLabelTag');
      } catch (\InvalidArgumentException $e) {
         throw new \InvalidArgumentException('[HtmlFormTag::getLabel()] No label found with name "' . $name
            . '" composed in form with name "' . $this->getAttribute('name') . '" for document controller "'
            . $this->getParentObject()->getDocumentController() . '"!', E_USER_ERROR, $e);
      }
   }

   /**
    * @public
    *
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
   public function transformForm() {

      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      /* @var $t BenchmarkTimer */
      $id = '(HtmlFormTag) ' . $this->getObjectId() . '::transformForm()';
      $t->start($id);

      // add action attribute if not set
      $action = $this->getAttribute('action');
      if ($action === null) {
         // escape current request uri to avoid XSS attacks
         $this->setAttribute('action', htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, Registry::retrieve('APF\core', 'Charset'), false));
      }

      // transform the form including all child tags
      $htmlCode = (string) '<form ';
      $htmlCode .= $this->getAttributesAsString($this->attributes, $this->attributeWhiteList);
      $htmlCode .= '>';

      if (count($this->children) > 0) {

         foreach ($this->children as $objectId => $DUMMY) {
            $childId = '(' . get_class($this->children[$objectId]) . ') ' . $objectId . '::transform()';
            $t->start($childId);

            $this->content = str_replace('<' . $objectId . ' />',
               $this->children[$objectId]->transform(), $this->content);

            $t->stop($childId);
         }
      }

      $htmlCode .= $this->content;
      $htmlCode .= '</form>';

      $t->stop($id);
      return $htmlCode;
   }

   /**
    * @public
    *
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
    * @public
    *
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

}
