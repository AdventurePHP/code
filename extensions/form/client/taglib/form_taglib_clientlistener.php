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
 * @package extensions::form::client
 * @class form_taglib_clientlistener
 *
 *  This taglib adds an clientlistener, which can be displayed by clientside form validation.
 *
 * @author Ralf Schubert  <ralf.schubert@the-screeze.de>
 * @version
 *  Version 1.0, 18.03.2010<br />
 */
class form_taglib_clientlistener extends form_control {

   /**
    * Add child taglibs.
    *
    * @author Ralf Schubert
    * @version
    *  Version 1.0, 18.03.2010<br />
    */
   public function __construct() {
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'html_taglib_placeholder', 'listener', 'placeholder');
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'html_taglib_getstring', 'listener', 'getstring');
      $this->__TagLibs[] = new TagLib('core::pagecontroller', 'core_taglib_addtaglib', 'listener', 'addtaglib');
   }

   /**
    * Overwrite the parent's method.
    *
    * @author Ralf Schubert
    * @version
    *  Version 1.0, 18.03.2010<br />
    */
   public function onParseTime() {
      $this->__extractTagLibTags();
   }

   /**
    * Overwrite the parent's method, because there's nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    *  Version 1.0, 18.03.2010<br />
    */
   public function onAfterAppend() {
   }

   /**
    * Transforms the tags and java scripts for client-listeners.
    *
    * @return string The generated html and js.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function transform() {
      $controlName = $this->__Attributes['control'];

      /* @var $parent html_taglib_form */
      $parent = $this->__ParentObject;
      $control = $parent->getFormElementByName($controlName);

      $this->transformChildren();

      $output = '<div id="apf-listener-' . $controlName . '" class="apf-form-clientlistener">' . $this->__Content . '</div>';

      // Check type of control, and generate jQuery selector
      switch (get_class($control)) {
         case 'form_taglib_select':
            $jQSelector = ':input[name=\'' . $controlName . '\[\]\']';
            break;
         case 'form_taglib_date':
            $jQSelector = 'span[id=\'' . $controlName . '\']';
            break;
         default:
            $jQSelector = ':input[name=\'' . $controlName . '\']';
      }

      // Get attributes which define animation options and properties
      $jsfordata = '';
      if (($anProps = $this->getAttribute('animationproperties', null)) !== null) {
         $jsfordata .= '$("#apf-listener-' . $controlName . '").data("animationproperties", ' . $anProps . ');';
         unset($anProps);
      }
      if (($anOpt = $this->getAttribute('animationoptions', null)) !== null) {
         $jsfordata .= '$("#apf-listener-' . $controlName . '").data("animationoptions", ' . $anOpt . ');';
         unset($anOpt);
      }

      $formID = $parent->getAttribute('id');
      $output .= '<script type="text/javascript">' .
            '$(document).ready(function(){' .
            '$("#' . $formID . ' ' . $jQSelector . '").bind(' .
            '"ValidationNotify",' .
            'function(event, param){' .
            'jQuery.APFFormValidator.handleClientListenerEvent($("#apf-listener-' . $controlName . '"),param);' .
            '}' .
            ');' .
            $jsfordata .
            '});</script>';
      return $output;
   }
}