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
namespace APF\extensions\form\client\taglib;

use APF\tools\form\taglib\AbstractFormControl;
use APF\tools\form\taglib\HtmlFormTag;

/**
 *  This taglib adds an client listener, which can be displayed by client-side form validation.
 *
 * @author Ralf Schubert  <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.03.2010<br />
 */
class ClientValidationListenerTag extends AbstractFormControl {

   /**
    * Overwrite the parent's method.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * Overwrite the parent's method, because there's nothing to do here.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
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
      $controlName = $this->attributes['control'];

      /* @var $parent HtmlFormTag */
      $parent = $this->getParent();
      $control = $parent->getFormElementByName($controlName);

      $this->transformChildren();

      $output = '<div id="apf-listener-' . $controlName . '" class="apf-form-clientlistener">' . $this->content . '</div>';

      // Check type of control, and generate jQuery selector
      switch (get_class($control)) {
         case 'SelectBoxTag':
            $jQSelector = ':input[name=\'' . $controlName . '\[\]\']';
            break;
         case 'DateSelectorTag':
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

   public function reset() {
      // nothing to do as client listeners cannot be reset
   }

}
