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
namespace APF\modules\captcha\pres\taglib;

use APF\core\pagecontroller\XmlParser;
use APF\modules\captcha\biz\actions\ShowCaptchaImageAction;
use APF\tools\form\filter\FormFilter;
use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\taglib\HtmlFormTag;
use APF\tools\form\taglib\TextFieldTag;
use APF\tools\form\validator\AbstractFormValidator;
use APF\tools\form\validator\FormValidator;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;
use APF\tools\string\StringAssistant;

/**
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
    * Contains the instance of the captcha text field.
    *
    * @var AbstractFormControl $textField
    */
   protected $textField = null;

   /**
    * Contains the name of the captcha text field.
    *
    * @var string $textFieldName
    */
   protected $textFieldName = null;

   /**
    * The captcha string of the current request.
    *
    * @var string $captchaString
    */
   protected $captchaString = null;

   /**
    * Re-implements the addValidator() method for the captcha taglib.
    * Can be used to add the shipped validator or a custom one
    * directly within the form definition.
    *
    * @param FormValidator $validator The desired validator.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function addValidator(FormValidator $validator) {
      $this->textField->addValidator($validator);
   }

   /**
    * Re-implements the addFiler() method for the captcha taglib.
    * Can be used to add the shipped filter or a custom one
    * directly within the form definition.
    *
    * @param FormFilter $filter The desired filter.
    *
    * @since 1.11
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.08.2009<br />
    */
   public function addFilter(FormFilter $filter) {
      $this->textField->addFilter($filter);
   }

   /**
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
    * Implements the onAfterAppend method from the ui_element class.
    *
    * @author Christian Achatz, Stephan Spiess
    * @version
    * Version 0.1, 20.06.2008<br />
    * Version 0.2, 10.11.2008 (Added the "clearonerror" attribute. If set to "true", the field is cleared on error.)<br />
    * Version 0.3, 04.01.2010 (Added the text_id attribute)<br />
    * Version 0.4, 29.10.2012 (Bug-fix: attribute valmarkerclass is now applied to the inner form field to allow css field validation on error)<br />
    * Version 0.5, 03.08.2016 (ID#303: allow hiding via template definition)<br />
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
      $session = $this->getRequest()->getSession(ShowCaptchaImageAction::SESSION_NAMESPACE);
      $this->captchaString = $session->load($this->textFieldName);
      $session->save($this->textFieldName, StringAssistant::generateCaptchaString(5));

      // ID#303: allow to hide form element by default within a template
      if ($this->getAttribute('hidden', 'false') === 'true') {
         $this->hide();
      }
   }

   /**
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

      // In case the control has been deactivated, don't generate output.
      if (!$this->isVisible) {
         return '';
      }

      // check, if the inline style should be disabled
      $disableInlineStyle = $this->getAttribute('disable_inline');
      $disableInlineStyle = $disableInlineStyle === 'true' ? true : false;

      // create desired media url
      $captchaUrl = LinkGenerator::generateActionUrl(Url::fromCurrent(), 'APF\modules\captcha\biz', 'showCaptcha', [
            'name' => $this->textFieldName
      ]);

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

   public function reset() {
      // Delegate reset to the inner text field. Might be without any effect in case clearonformerror=true.
      $this->textField->reset();
   }

}
