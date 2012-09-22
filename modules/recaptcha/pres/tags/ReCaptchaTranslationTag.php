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

/**
 * @package modules::recaptcha::pres::tags
 * @class ReCaptchaTranslationTag
 *
 * Implements a custom <em>&lt;*:getstring /&gt;</em> tag for the <em>&lt;form:recaptcha /&gt;</em>
 * form control.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 22.09.2012<br />
 */
class ReCaptchaTranslationTag extends html_taglib_getstring {

   public function onAfterAppend() {

      // check for attribute "namespace"
      $namespace = $this->getAttribute('namespace');
      if ($namespace === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->onAfterAppend()] No attribute '
               . '"namespace" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "config"
      $configName = $this->getAttribute('config');
      if ($configName === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->onAfterAppend()] No attribute '
               . '"config" given in tag definition!', E_USER_ERROR);
      }

      // check for attribute "entry"
      $entry = $this->getAttribute('entry');
      if ($entry === null) {
         throw new InvalidArgumentException('[' . get_class($this) . '->onAfterAppend()] No attribute '
               . '"entry" given in tag definition!', E_USER_ERROR);
      }

      // get configuration values
      $config = $this->getConfiguration($namespace, $configName);

      $langSection = $config->getSection($this->getLanguage());
      if ($langSection === null) {

         // get environment variable from registry to have nice exception message
         $env = Registry::retrieve('apf::core', 'Environment');

         throw new InvalidArgumentException('[' . get_class($this) . '::onAfterAppend()] Given entry "'
               . $entry . '" is not defined in section "' . $this->getLanguage() . '" in configuration "'
               . $env . '_' . $configName . '" in namespace "' . $namespace . '" and context "'
               . $this->getContext() . '"!', E_USER_ERROR);
      }

      /* @var $control ReCaptchaTag */
      $control = $this->getParentObject();

      // inject custom translation attributes into the ReCaptchaTag
      foreach ($langSection->getValueNames() as $name) {
         $control->setAttribute($name, $langSection->getValue($name));
      }
   }

   public function transform() {
      return '';
   }

}
