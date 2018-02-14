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
namespace APF\core\pagecontroller;

use APF\tools\form\HtmlForm;
use InvalidArgumentException;

/**
 * Represents a reusable html fragment (template) within a template file. The tag's functionality
 * can be extended by the &lt;core:addtaglib /&gt; tag. Use setPlaceHolder() to set a place
 * holder's value and transformOnPlace() or transformTemplate() to generate the output.
 * <p/>
 * Besides, you may directly echo the template using it's __toString() implementation which internally
 * uses the transformTemplate() method to generate the output implicitly.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 10.11.2008 (Removed the IncludedTagLib behavior, because this lead to errors when including new taglibs with template:addtaglib.)<br />
 */
class TemplateTag extends Document implements Template {

   /**
    * Indicates, if the template should be transformed on the place of definition. Default is false.
    */
   protected $transformOnPlace = false;

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   public function &getLabel($name) {
      try {
         return $this->getChildNode('name', $name, LanguageLabel::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[TemplateTag::getLabel()] No label found with name "' . $name
               . '" composed in template with name "' . $this->getAttribute('name') . '" for document controller "'
               . get_class($this->getParentObject()->getDocumentController()) . '"!', E_USER_ERROR, $e);
      }
   }

   public function &getForm($name) {
      try {
         return $this->getChildNode('name', $name, HtmlForm::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[TemplateTag::getForm()] No form found with name "' . $name
               . '" composed in template with name "' . $this->getAttribute('name') . '" for document controller "'
               . get_class($this->getParentObject()->getDocumentController()) . '"!', E_USER_ERROR, $e);
      }
   }

   public function &getTemplate($name) {
      try {
         return $this->getChildNode('name', $name, Template::class);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[TemplateTag::getTemplate()] No nested template found with name "' . $name
               . '" composed in template with name "' . $this->getAttribute('name') . '" for document controller "'
               . get_class($this->getParentObject()->getDocumentController()) . '"!', E_USER_ERROR, $e);
      }
   }

   public function __toString() {
      return $this->transformTemplate();
   }

   public function transformTemplate() {
      return $this->transformChildrenAndPreserveContent();
   }

   public function clear(TemplateTagClearApproach $approach = null) {
      if ($approach === null) {
         $approach = new DefaultTemplateTagClearApproach();
      }
      $approach->clear($this);
   }

   public function transformOnPlace() {
      $this->transformOnPlace = true;
   }

   /**
    * By default, the content of the template is returned as an empty string. This is because the
    * html:template tag normally is used as a reusable fragment. If the transformOnPlace() function
    * is called before, the content of the template is returned instead.
    *
    * @return string Empty string or content of the tag
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.01.2007<br />
    * Version 0.2, 12.01.2007 (An empty string is now returned)<br />
    * Version 0.3, 19.05.2008 (Implemented the transformOnPlace() feature)<br />
    */
   public function transform() {

      // checks, if transformOnPlace is activated
      if ($this->transformOnPlace === true) {
         return $this->transformTemplate();
      }

      // return empty string
      return '';
   }

}
