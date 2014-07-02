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
namespace APF\core\pagecontroller;

use InvalidArgumentException;

/**
 * Represents a reusable html fragment (template) within a template file. The tag's functionality
 * can be extended by the &lt;template:addtaglib /&gt; tag. Use setPlaceHolder() to set a place
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
class TemplateTag extends Document {

   /**
    * Indicates, if the template should be transformed on the place of definition. Default is false.
    */
   protected $transformOnPlace = false;

   /**
    * Implements the onParseTime() method from the APFObject class. Uses the extractTagLibTags()
    * function to parse the known tags.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 31.12.2006<br />
    */
   public function onParseTime() {
      $this->extractTagLibTags();
   }

   public function onAfterAppend() {
      $this->extractExpressionTags();
   }

   /**
    * Let's you retrieve an &lt;template:getstring /&gt; tag instance with the specified name.
    *
    * @param string $name The name of the template label to return.
    *
    * @return LanguageLabelTag The instance of the desired label.
    * @throws InvalidArgumentException In case no label can be found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.01.2012<br />
    */
   public function &getLabel($name) {
      try {
         return $this->getChildNode('name', $name, 'APF\core\pagecontroller\LanguageLabelTag');
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException('[TemplateTag::getLabel()] No label found with name "' . $name
               . '" composed in template with name "' . $this->getAttribute('name') . '" for document controller "'
               . $this->getParentObject()->getDocumentController() . '"! Perhaps, the tag library for template:getstring '
               . 'is not loaded.', E_USER_ERROR, $e);
      }
   }

   /**
    * Returns the content of the template. Can be used to generate the template output
    * within a document controller. Usage:
    * <pre>
    * $template = &$this->getTemplate('MyTemplate');
    * $template->setPlaceHolder('URL', 'http://adventure-php-framework.org');
    * echo $template->transformTemplate(); // or echo $template; using the __toString() implementation
    * </pre>
    *
    * @return string The content of the transformed template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.12.2006<br />
    * Version 0.2, 31.12.2006 (Removed parameter $this->isVisible, because the parent object automatically removes the XML positioning tag on transformation now)<br />
    * Version 0.3, 02.02.2007 (Renamed method to transformTemplate(). Removed visible marking finally from the class.)<br />
    * Version 0.4, 05.01.2007 (Added the template:addtaglib tag)<br />
    */
   public function transformTemplate() {
      return $this->transformChildrenAndPreserveContent();
   }

   /**
    * Returns the transformed state of the current template instance. Can be used to retrieve the
    * content of the current template more convenient.
    * <p/>
    * You may want to use this method e.g. for transforming templates within a loop saving an
    * explicit call to <em>transformTemplate()</em>. E.g.:
    * <code>
    * $tmpl = $this->getTemplate(...);
    * $buffer = '';
    * foreach($items as $item) {
    *    $buffer .= $tmpl; // short version of $tmpl->transformTemplate()
    * }
    * </code>
    *
    * @return string The content of the transformed template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 05.08.2013<br />
    */
   public function __toString() {
      return $this->transformTemplate();
   }

   /**
    * Allows you to clear a TemplateTag instance according to the applied clear approach.
    * <p/>
    * By default, the <em>DefaultTemplateTagClearApproach</em> is used reset the template's
    * place holder tags by removing their content.
    * <p/>
    * In case you intend to add your own implementation of place holders or elements that need
    * to be cleared, please implement your custom clear strategy and apply to this method.
    *
    * @param TemplateTagClearApproach $approach The clear approach to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.05.2013<br />
    */
   public function clear(TemplateTagClearApproach $approach = null) {
      if ($approach === null) {
         $approach = new DefaultTemplateTagClearApproach();
      }
      $approach->clear($this);
   }

   /**
    * Indicates, that the template should be displayed on the place of definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.05.2008<br />
    */
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
