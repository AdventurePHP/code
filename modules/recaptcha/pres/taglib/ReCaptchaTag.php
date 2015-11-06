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
namespace APF\modules\recaptcha\pres\taglib;

use APF\tools\form\filter\FormFilter;
use APF\tools\form\FormException;
use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\validator\AbstractFormValidator;

/**
 * Implements a re-captcha wrapper for Google's ReCaptcha.
 * <p/>
 * Further docs can be found under https://developers.google.com/recaptcha/intro.
 * <p/>
 * Details on the architecture of reCaptcha can be read about under https://developers.google.com/recaptcha/.
 * <p/>
 * Using APF's ReCaptcha wrapper requires download and inclusion of Google's
 * <em>recaptcha</em> available under https://github.com/google/recaptcha
 * <p/>
 * Please include <em>recaptcha</em> library into your project and add to
 * auto loading.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.09.2012<br />
 * Version 0.2, 06.11.2013 (Removed inclusion of external recaptachalib library due to license issues described in ID#80)<br />
 * Version 0.3, 30.10.2015 (ID#94: updated to ReCaptcha 2 lib)<br />
 */
class ReCaptchaTag extends AbstractFormControl {

   /**
    * The name of the challenge answer identifier url parameter.
    *
    * @var string RE_CAPTCHA_RESPONSE_IDENTIFIER
    */
   const RE_CAPTCHA_RESPONSE_IDENTIFIER = 'g-recaptcha-response';

   /**
    * The error messages key to display within the reCaptcha control.
    *
    */
   protected $errorMessageKeys = [];

   /**
    * Overwrites the parent method since filtering is not necessary with the reCaptcha form.
    *
    * @param FormFilter $filter The desired filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.09.2012<br />
    */
   public function addFilter(FormFilter &$filter) {
      // Ignore adding filters to the reCaptcha control. This is because
      // reCaptcha form controls are validated externally. Hence, there
      // is no need to filter input.
   }

   public function onParseTime() {

      // do nothing except an attribute check, since presetting on reCaptcha fields is not needed.
      $name = $this->getAttribute('name');
      if (empty($name)) {
         $form = $this->getParentObject();
         throw new FormException(
               'ReCaptcha control within form "' . $form->getAttribute('name')
               . '" has no "name" attribute specified! Please re-check your form definition.'
         );
      }

      $publicKey = $this->getAttribute('public-key');
      if (empty($publicKey)) {
         $form = $this->getParentObject();
         throw new FormException(
               'ReCaptcha control within form "' . $form->getAttribute('name')
               . '" has no "public-key" attribute specified! Please re-check your form definition.'
         );
      }

      $privateKey = $this->getAttribute('private-key');
      if (empty($privateKey)) {
         $form = $this->getParentObject();
         throw new FormException(
               'ReCaptcha control within form "' . $form->getAttribute('name')
               . '" has no "private-key" attribute specified! Please re-check your form definition.'
         );
      }
   }

   /**
    * @return string The private key to use with the reCaptcha control (needed to verify the answer).
    */
   public function getPrivateKey() {
      return $this->getAttribute('private-key');
   }

   /**
    * @return string[] The error message keys returned by the Google API call.
    */
   public function getErrorMessageKeys() {
      return $this->errorMessageKeys;
   }

   /**
    * @param string[] $errorMessageKeys The error message keys to display a hint to the user.
    */
   public function setErrorMessageKeys(array $errorMessageKeys = []) {
      $this->errorMessageKeys = $errorMessageKeys;
   }

   public function transform() {

      // Bug fix ID#77: in case the control has been deactivated, don't generate output.
      if (!$this->isVisible) {
         return '';
      }

      $captchaId = $this->getCaptchaId();

      // mark control with CSS class
      $errorIndicator = '';
      if ($this->controlIsValid === false) {
         $errorIndicator = ' ' . $this->getAttribute(
                     AbstractFormValidator::$CUSTOM_MARKER_CLASS_ATTRIBUTE,
                     AbstractFormValidator::$DEFAULT_MARKER_CLASS
               );
      };

      // allow language override
      $language = $this->getAttribute('lang', $this->getLanguage());

      $html = '<div class="g-recaptcha' . $errorIndicator . '" id="g-recaptcha-' . $captchaId . '"></div>' . PHP_EOL;
      $html .= '<script src="https://www.google.com/recaptcha/api.js?hl=' . $language
            . '&amp;onload=ReCaptchaDisplay' . $captchaId . '&amp;render=explicit" async defer></script>' . PHP_EOL;

      // site key is mandatory!
      $params = [];
      $params[] = '      \'sitekey\' : \'' . $this->getPublicKey() . '\'';

      // support custom theme creation as described under
      // https://developers.google.com/recaptcha/docs/display#config
      $theme = $this->getAttribute('theme');
      if ($theme !== null) {
         $params[] = '      \'theme\' : \'' . $theme . '\'';
      }

      // add tab index support if desired
      $tabIndex = $this->getAttribute('tabindex');
      if ($tabIndex !== null) {
         $params[] = '      \'tabindex\' : \'' . $tabIndex . '\'';
      }

      // add size support if desired
      $size = $this->getAttribute('size');
      if ($size !== null) {
         $params[] = '      \'size\' : \'' . $size . '\'';
      }

      $html .= '<script type="text/javascript">
var ReCaptchaDisplay' . $captchaId . ' = function () {
   grecaptcha.render(\'g-recaptcha-' . $captchaId . '\', {' . PHP_EOL
            . implode(', ' . PHP_EOL, $params) . PHP_EOL
            . '   });
};
</script>';

      return $html;
   }

   /**
    * @return string Unique id of this reCAPTCHA instance.
    */
   protected function getCaptchaId() {
      return md5($this->getAttribute('name') . uniqid(rand(), true));
   }

   /**
    * @return string The public key to use with the reCaptcha control (needed to display the control).
    */
   protected function getPublicKey() {
      return $this->getAttribute('public-key');
   }

}
