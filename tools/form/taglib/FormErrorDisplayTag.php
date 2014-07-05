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
use APF\core\pagecontroller\TagLib;

/**
 * @package APF\tools\form\taglib
 * @class FormErrorDisplayTag
 *
 * Implements a taglib, that outputs it's content, in case the form, the
 * tag is defined in, is sent but not valid. This let's you easily define
 * form error messages. The definition of the tag is as follows:
 * <pre>
 * &lt;form:error&gt;
 *   The content to display, in case the form is sent, but invalid!
 *   [&lt;error:getstring namespace="" config="" key="" /&gt;]
 *   [&lt;error:placeholder name="" /&gt;]
 *   [&lt;error:addtaglib namespace="" class="" prefix="" name="" /&gt;]
 * &lt;/form:error&gt;
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.09.2009<br />
 */
class FormErrorDisplayTag extends AbstractFormControl {

   public function __construct() {
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\PlaceHolderTag', 'error', 'placeholder');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\LanguageLabelTag', 'error', 'getstring');
      $this->tagLibs[] = new TagLib('APF\core\pagecontroller\AddTaglibTag', 'error', 'addtaglib');
   }

   /**
    * @public
    *
    * Overwrites the parent's method, because there is nothing to do except
    * analyzing the child tags.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2009<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * @public
    *
    * Outputs the content of the tag, if the form, the tag is
    * defined in is sent but invalid!
    *
    * @return string The content of the tag.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.09.2009<br />
    */
   public function transform() {
      /* @var $form HtmlFormTag */
      $form = $this->getParentObject();
      if ($form->isSent() && !$form->isValid()) {
         $this->transformChildren();
         return $this->content;
      }
      return '';
   }

}