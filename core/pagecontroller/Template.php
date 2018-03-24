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
 * Declares the interface of a template (re-usable fragment within a template file).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.08.2014 (ID#231: Introduced interface to allow custom implementations)<br />
 */
interface Template {

   /**
    * Returns the content of the template. Can be used to generate the template output
    * within a document controller. Usage:
    * <pre>
    * $template = $this->getTemplate('MyTemplate');
    * $template->setPlaceHolder('URL', 'https://adventure-php-framework.org');
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
   public function transformTemplate();

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
   public function __toString();

   /**
    * Indicates, that the template should be displayed on the place of definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 19.05.2008<br />
    */
   public function transformOnPlace();

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
   public function clear(TemplateTagClearApproach $approach = null);

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
   public function getLabel(string $name);

   /**
    * Convenience method to obtain a form located within a template.
    *
    * @param string $name The name of the form to return.
    * @return HtmlForm The desired form instance.
    *
    * @throws InvalidArgumentException In case no form can be found within the document tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2017<br />
    */
   public function getForm(string $name);

   /**
    * Convenience method to obtain a (sub) template located within a template.
    *
    * @param string $name The name of the template to return.
    * @return Template The desired template instance.
    *
    * @throws InvalidArgumentException In case no template can be found within the document tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.05.2017<br />
    */
   public function getTemplate(string $name);

}
