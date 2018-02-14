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

/**
 * This class implements the functionality of the core::importdesign tag. It generates a sub node
 * from the template specified by the tag's attributes within the current APF DOM tree. Each
 * importdesign tag can compose further tags.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 */
class ImportTemplateTag extends Document {

   /**
    * Implements the onParseTime() method from the Document class. Includes the desired template
    * as a new DOM node into the current APF DOM tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 31.12.2006 (Added pagepart option)<br />
    * Version 0.3, 15.01.2007 (Now DocumentController tags are extracted first)<br />
    * Version 0.4, 10.03.2007 (The Context can now be manipulated in the core:importdesign tag)<br />
    * Version 0.5, 11.03.2007 (Introduced the "incparam" attribute to be able to control the template param via url)<br />
    * Version 0.6, 26.10.2008 (Made the benchmark id generation more generic)<br />
    */
   public function onParseTime() {

      // get attributes
      $namespace = $this->getAttribute('namespace');
      $template = $this->getAttribute('template');

      // apply context if available
      $context = $this->getAttribute('context');
      if ($context !== null) {
         $this->setContext($context);
      }

      // apply language if available
      $language = $this->getAttribute('language');
      if ($language !== null) {
         $this->setLanguage($language);
      }

      // manage inc param
      $incParam = $this->getAttribute('incparam');
      if ($incParam === null) {
         $incParam = 'pagepart';
      }

      // check, if the inc param is present in the current request
      if (strpos($template, '[') !== false) {

         $value = $this->getRequest()->getParameter($incParam);
         if ($value !== null) {
            $template = $value;
         } else {

            // read template attribute from inc param
            $pagePartStartPos = strpos($template, '=');
            $pagePartEndPos = strlen($template) - 1;
            $template = trim(substr($template, $pagePartStartPos + 1, ($pagePartEndPos - $pagePartStartPos) - 1));
         }
      }

      // get content
      $this->loadContentFromFile($namespace, $template);

      // parse document controller statements
      $this->extractDocumentController();

      // extract further xml tags
      $this->extractTagLibTags();
   }

}
