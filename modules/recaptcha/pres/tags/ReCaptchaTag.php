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
import('tools::form::taglib', 'form_control');
require_once(__DIR__ . '/../../external/google/recaptchalib.php');

/**
 * @package modules::recaptcha::pres::tags
 * @class ReCaptchaTag
 *
 * Implements a re-captcha wrapper for Google's ReCaptcha.
 * <p/>
 * Further docs can be found under https://developers.google.com/recaptcha/docs/php?hl=de.
 * <p/>
 * The lib currently included within the APF distribution is http://code.google.com/p/recaptcha/downloads/detail?name=recaptcha-php-1.11.zip&can=2&q=label%3Aphplib-Latest
 * <p/>
 * Details on the architecture of reCaptcha can be read about under https://developers.google.com/recaptcha/docs/display?hl=de.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.09.2012<br />
 */
class ReCaptchaTag extends form_control {

   /**
    * @const The name of the challenge identifier url parameter.
    */
   const RE_CAPTCHA_CHALLENGE_FIELD_IDENTIFIER = 'recaptcha_challenge_field';

   /**
    * @const The name of the challenge answer identifier url parameter.
    */
   const RE_CAPTCHA_CHALLENGE_ANSWER_IDENTIFIER = 'recaptcha_response_field';

   /**
    * @var string The error message key to display within the reCaptcha control.
    */
   private $errorMessageKey;

   /**
    * @public
    *
    * Overwrites the parent method since filtering is not necessary with the reCaptcha form.
    *
    * @param AbstractFormFilter $filter The desired filter.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.09.2012<br />
    */
   public function addFilter(AbstractFormFilter &$filter) {
      // Ignore adding filters to the reCaptcha control. This is because
      // reCaptcha form controls are validated externally. Hence, there
      // is no need to filter input.
   }

   public function onParseTime() {
      // do nothing except an attribute check, since presetting on reCaptcha fields is not needed.
      $name = $this->getAttribute('name');
      if (empty($name)) {
         $form = $this->getParentObject();
         throw new FormException('ReCaptcha control within form "' . $form->getAttribute('name')
               . '" has no "name" attribute specified! Please r-check your form definition.');
      }
   }

   /**
    * @return string The public key to use with the reCaptcha control (needed to display the control).
    */
   public function getPublicKey() {
      return $this->getAttribute('public-key');
   }

   /**
    * @return string The private key to use with the reCaptcha control (needed to verify the answer).
    */
   public function getPrivateKey() {
      return $this->getAttribute('private-key');
   }

   /**
    * @param string $errorMessageKey The error message key to pass to the reCaptche control to display a hint to the user.
    */
   public function setErrorMessageKey($errorMessageKey) {
      $this->errorMessageKey = $errorMessageKey;
   }

   /**
    * @private
    *
    * Returns the name of the theme configured. Default is <em>red</em>.
    * <p/>
    * For details, please refer to https://developers.google.com/recaptcha/docs/customization?hl=de.
    *
    * @return string Theme name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.09.2012<br />
    */
   private function getThemeName() {
      return $this->getAttribute('theme', 'red');
   }

   public function transform() {

      $html = ' <script type="text/javascript">var RecaptchaOptions = { '
            . 'theme : \'' . $this->getThemeName() . '\','

            // map default APF DOM tree language attribute to the reCaptcha option to
            // support easy language change
            . ' lang : \'' . $this->getLanguage() . '\'';

      // add tab index support if desired
      $tabIndex = $this->getAttribute('tabindex');
      if ($tabIndex !== null) {
         $html .= ', tabindex : ' . $tabIndex;
      }

      // support custom theme creation as described under
      // https://developers.google.com/recaptcha/docs/customization?hl=de#Custom_Theming
      $customThemeId = $this->getAttribute('custom-theme-id');
      if ($customThemeId !== null) {
         $html .= ', custom_theme_widget: \'' . $customThemeId . '\'';
      }

      // add custom translation options if requested
      $customTranslation = $this->getCustomTranslations();
      if (!empty($customTranslation)) {
         $html .= ', custom_translations : { ' . $customTranslation . ' }';
      }

      return $html . ' };</script>'
            . PHP_EOL
            . recaptcha_get_html($this->getPublicKey(), $this->errorMessageKey, $this->useSSL());
   }

   /**
    * @private
    *
    * Evaluates custom translation options and returns the JavaScript option string.
    *
    * @return string The custom translation option string.
    * @
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.09.2012<br />
    */
   private function getCustomTranslations() {

      $l10nOptions = array();

      $instructionsVisual = $this->getAttribute('l10n-instructions-visual');
      if (!empty($instructionsVisual)) {
         $l10nOptions['instructions_visual'] = $instructionsVisual;
      }

      $instructionsAudio = $this->getAttribute('l10n-instructions-audio');
      if (!empty($instructionsAudio)) {
         $l10nOptions['instructions_audio'] = $instructionsAudio;
      }

      $playAgain = $this->getAttribute('l10n-play-again');
      if (!empty($playAgain)) {
         $l10nOptions['play_again'] = $playAgain;
      }

      $cantHearThis = $this->getAttribute('l10n-cant-hear-this');
      if (!empty($cantHearThis)) {
         $l10nOptions['cant_hear_this'] = $cantHearThis;
      }

      $visualChallenge = $this->getAttribute('l10n-visual-challenge');
      if (!empty($visualChallenge)) {
         $l10nOptions['visual_challenge'] = $visualChallenge;
      }


      $audioChallenge = $this->getAttribute('l10n-audio-challenge');
      if (!empty($audioChallenge)) {
         $l10nOptions['audio_challenge'] = $audioChallenge;
      }

      $refreshButton = $this->getAttribute('l10n-refresh-btn');
      if (!empty($refreshButton)) {
         $l10nOptions['refresh_btn'] = $refreshButton;
      }

      $helpButton = $this->getAttribute('l10n-help-btn');
      if (!empty($helpButton)) {
         $l10nOptions['help_btn'] = $helpButton;
      }

      $incorrectTryAgain = $this->getAttribute('l10n-incorrect-try-again');
      if (!empty($incorrectTryAgain)) {
         $l10nOptions['incorrect_try_again'] = $incorrectTryAgain;
      }

      $combinedL10NOptions = array();

      foreach ($l10nOptions as $key => $value) {
         $combinedL10NOptions[] = $key . ': "' . $value . '"';
      }

      return implode(',', $combinedL10NOptions);
   }

   /**
    * @return bool True, in case SSL should be used, false otherwise.
    */
   private function useSSL() {
      return $this->getAttribute('use-ssl', 'false') === 'true';
   }

}
