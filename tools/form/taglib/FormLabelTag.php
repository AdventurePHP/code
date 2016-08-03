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
namespace APF\tools\form\taglib;

/**
 * Represents a &lt;label /&gt; form tag that is APF parser sensitive in terms of visibility management.
 * The definition of the tag is as follows:
 * <pre>
 * &lt;form:label for="..."[ id=""][ class=""]&gt;
 *    [...]
 *    [&lt;label:getstring [name=""] namespace="" config="" entry="" /&gt;]
 * &lt;/form:label&gt;
 * </pre>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 16.12.2012<br />
 */
class FormLabelTag extends AbstractFormControl {

   public function __construct() {
      $this->attributeWhiteList = ['for', 'id', 'class'];
   }

   public function onParseTime() {
      $this->extractTagLibTags();

      // ID#303: allow to hide form element by default within a template
      if ($this->getAttribute('hidden', 'false') === 'true') {
         $this->hide();
      }
   }

   public function transform() {
      if ($this->isVisible) {
         return '<label ' . $this->getSanitizedAttributesAsString($this->getAttributes()) . '>'
         . $this->getContent()
         . '</label>';
      }

      return '';
   }

   public function reset() {
      // nothing to do as labels cannot be reset
   }

}
