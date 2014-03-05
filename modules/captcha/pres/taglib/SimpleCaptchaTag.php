<?php
namespace APF\modules\captcha\pres\taglib;

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
use APF\core\pagecontroller\XmlParser;
use APF\tools\form\filter\AbstractFormFilter;
use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\validator\AbstractFormValidator;
use APF\tools\link\Url;
use APF\tools\string\StringAssistant;
use APF\core\session\Session;
use APF\tools\link\LinkGenerator;

/**
 * @package APF\modules\captcha\pres\taglib
 * @class SimpleCaptchaTag
 *
 * Implements a CAPTCHA-Taglib to extend a form's features. Inherits from AbstractFormControl
 * in order to be a fully qualified form element.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.07.2008<br />
 * Version 0.2, 20.07.2008 (Moved in a separate module folder to deliver it with the framework release)<br />
 */
class SimpleCaptchaTag extends AbstractFormControl {

   /**
    * @protected
    * @var AbstractFormControl Contains the instance of the captcha text field.
    */
   protected $textField = null;

   /**
    * @protected
    * @var string Contains the name of the captcha text field.
    */
   protected $textFieldName = null;

   /**
    * @protected
    * @var string The captcha string of the current request.
    */
   protected $captchaString = null;

   /**
    * @public
    *
    * Re-implements the addValidator() method for the captcha taglib.
    * Can be used to add the shipped validator or a custom one
    * directly within the form definition.
    *
    * @param AbstractFormValidator $validator The desired validator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function addValidator(AbstractFormValidator &$validator) {
      $this->textField->addValidator($validator);
   }

   /**
    * @public
    * @since 1.11
    *
    * Re-implements the addFiler() method for the captcha taglib.
    * Can be used to add the shipped filter or a custom one
    * directly within the form definition.
    *
    * @param AbstractFormFilter $filter The desired filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function addFilter(AbstractFormFilter &$filter) {
      $this->textField->addFilter($filter);
   }

   /**
    * @pubic
    *
    * Re-implements the isValid() method for this special case.
    *
    * @return boolean The validation status of the inner text field.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.09.2009<br />
    */
   public function isValid() {
      return $this->textField->isValid();
   }

   /**
    * @public
    *
    * Implements the onAfterAppend method from the ui_element class.
    *
    * @author Christian Achatz, Stephan Spiess
    * @version
    * Version 0.1, 20.06.2008<br />
    * Version 0.2, 10.11.2008 (Added the "clearonerror" attribute. If set to "true", the field is cleared on error.)<br />
    * Version 0.3, 04.01.2010 (Added the text_id attribute)<br />
    * Version 0.4, 29.10.2012 (Bug-fix: attribute valmarkerclass is now applied to the inner form field to allow css field validation on error)<br />
    */
   public function onParseTime() {

      // create text field
      $this->textField = new TextFieldTag();
      $this->textField->setObjectId(XmlParser::generateUniqID());

      // prepare the text field
      $textClass = $this->getAttribute('text_class');
      if ($textClass !== null) {
         $this->textField->setAttribute('class', $textClass);
      }
      $textStyle = $this->getAttribute('text_style');
      if ($textStyle !== null) {
         $this->textField->setAttribute('style', $textStyle);
      }
      $textId = $this->getAttribute('text_id');
      if ($textId !== null) {
         $this->textField->setAttribute('id', $textId);
      }

      // apply validation marker css class to provide validation markup capabilities
      $errorClass = $this->getAttribute(AbstractFormValidator::$CUSTOM_MARKER_CLASS_ATTRIBUTE);
      if ($errorClass !== null) {
         $this->textField->setAttribute(AbstractFormValidator::$CUSTOM_MARKER_CLASS_ATTRIBUTE, $errorClass);
      }

      $this->textFieldName = md5($this->getParentObject()->getAttribute('name') . '_captcha');
      $this->textField->setAttribute('name', $this->textFieldName);
      $this->textField->setAttribute('maxlength', '5');

      // apply the onParseTime method to guarantee native APF environment
      $this->textField->setLanguage($this->language);
      $this->textField->setContext($this->context);
      $this->textField->onParseTime();

      // apply the onAfterAppend method to guarantee native APF environment
      $this->textField->setParentObject($this->getParentObject());
      $this->textField->onAfterAppend();

      // get the captcha string from session
      $sessMgr = new Session('APF\modules\captcha');
      $this->captchaString = $sessMgr->load($this->textFieldName);
      $sessMgr->save($this->textFieldName, StringAssistant::generateCaptchaString(5));

   }

   /**
    * @public
    *
    * Returns the current captcha string to be able to validate it
    * against the user input.
    *
    * @return string The current captcha string.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function getCurrentCaptcha() {
      return $this->captchaString;
   }

   /**
    * @public
    *
    * Returns a reference on the captcha control's text field.
    *
    * @return TextFieldTag The captcha's text field.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function &getCaptchaTextField() {
      return $this->textField;
   }

   /**
    * @public
    *
    * Generate and return HTML code of the captcha tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.06.2008<br />
    * Version 0.2, 05.11.2008 (Changed action base url generation)<br />
    * Version 0.3, 07.11.2008 (Fixed the action URL generation. See class MediaInclusionTag for more details.)<br />
    * Version 0.4, 19.12.2009 (Added attribute to be able to disable the inline styles to have clean markup)<br />
    * Version 0.5, 04.01.2010 (Added clearonformerror attribute)<br />
    * Version 0.6, 04.01.2010 (Added the image_id attribute)<br />
    */
   public function transform() {

      // check, if the inline style should be disabled
      $disableInlineStyle = $this->getAttribute('disable_inline');
      $disableInlineStyle = $disableInlineStyle === 'true' ? true : false;

      // create desired media url
      $captchaUrl = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\modules\captcha\biz', 'showCaptcha', array(
         'name' => $this->textFieldName
      ));

      // initialize captcha source
      $captchaCode = '<div class="captcha"><img src="' . $captchaUrl . '" alt="CAPTCHA" ';
      if ($disableInlineStyle === false) {
         $captchaCode .= 'style="float:left;" ';
      }

      // add class and style attributes if desired
      $imgClass = $this->getAttribute('image_class');
      if ($imgClass !== null) {
         $captchaCode .= 'class="' . $imgClass . '" ';
      }
      $imgStyle = $this->getAttribute('image_style');
      if ($imgStyle !== null) {
         $captchaCode .= 'style="' . $imgStyle . '" ';
      }
      $imgId = $this->getAttribute('image_id');
      if ($imgId !== null) {
         $captchaCode .= 'id="' . $imgId . '" ';
      }

      // clear field on form errors
      $cleanOnError = $this->getAttribute('clearonformerror');
      if ($cleanOnError === 'true') {
         /* @var $parent HtmlFormTag */
         $parent = $this->getParentObject();
         if (!$parent->isValid()) {
            $this->textField->setAttribute('value', '');
         }
      }

      // concatenate the html code and return it
      if ($disableInlineStyle === true) {
         return $captchaCode . '/><div>'
               . $this->textField->transform() . '</div></div>';
      } else {
         return $captchaCode . '/><div style="line-height: 40px; float: left; margin-left: 20px;">'
               . $this->textField->transform() . '</div><div style="clear: left;"></div></div>';
      }

   }

}
