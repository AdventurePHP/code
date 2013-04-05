<?php
namespace APF\extensions\form\client\taglib;

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
use APF\core\pagecontroller\TagLib;
use APF\tools\form\taglib\AbstractFormControl;

/**
 * @package APF\extensions\form\client
 * @class FormClientErrorDisplayTag
 *
 *  This taglib adds an clienterror, which can be displayed by the client form validation.
 *
 * @author Ralf Schubert  <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.03.2010<br />
 */
class FormClientErrorDisplayTag extends AbstractFormControl {

   /**
    * Add child taglibs.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function __construct() {
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'error', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'error', 'getstring');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'error', 'addtaglib');
   }

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
    * Transforms the tags and javascript for clienterrors.
    *
    * @return string The generated html and js.
    *
    * @author Ralf Schubert
    * @version
    * Version 1.0, 18.03.2010<br />
    */
   public function transform() {
      $this->transformChildren();

      $formID = $this->getParentObject()->getAttribute('id');
      $output = '<div id="apf-error-' . $formID . '" class="apf-form-clienterror">' . $this->content . '</div>';
      /*
       * Generate javascript for binding on the ValidationNotify event.
       * !Important: Check for (event.target === this), because ValidationNotify
       * triggered on child-inputs will be handed up to form-element.!
      */
      $output .= '<script type="text/javascript">' .
            '$(document).ready(function(){' .
            '$("#' . $formID . '").bind(' .
            '"ValidationNotify",' .
            'function(event, param){' .
            'if(event.target === this){' .
            'var listener = $("#apf-error-' . $formID . '");' .
            'if(param.valid === false){' .
            'listener.removeClass("apf-form-clienterror");' .
            '}' .
            'else {' .
            'listener.addClass("apf-form-clienterror");' .
            '}' .
            '}' .
            '}' .
            ');' .
            '});</script>';

      return $output;
   }
}