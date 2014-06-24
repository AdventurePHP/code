<?php
namespace APF\core\pagecontroller;

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
 * @package APF\core\pagecontroller
 * @class Page
 *
 * The Page object represents the root node of  a web page. It is used as a container for the
 * initial document (root document) and is responsible for creating and transforming the root
 * document.
 *
 * @author Christian Schäfer
 * @version
 * Version 0.1, 28.12.2006<br />
 * Version 0.2, 03.01.2007 (Introduced URL rewriting)<br />
 * Version 0.3, 08.06.2007 (URL rewriting was outsourced and "__rewriteRequestURI()" was removed)<br />
 */
class Page extends APFObject {

   /**
    * @var Document Container for the root <em>Document</em> of the page.
    */
   private $document;

   /**
    * @public
    *
    * Returns the root document of the APF DOM tree.
    *
    * @return Document The root document of the page controller's APF DOM tree.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.10.2010<br />
    */
   public function &getRootDocument() {
      return $this->document;
   }

   /**
    * @public
    *
    * Creates the initial document (root) of the page object and loads the initial template. If
    * no context was set before, the namespace of the initial template is taken instead.
    *
    * @param string $namespace namespace if the initial template
    * @param string $design (file)name if the initial template
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 31.01.2007 (Now the context of the document is set)<br />
    * Version 0.3, 04.03.2007 (The namespace is taken as a context, if no other was set before)<br />
    * Version 0.4, 22.04.2007 (Now the language is applied to the document)<br />
    * Version 0.5, 08.03.2009 (Bug-fix: protected variable parentObject might not be used)<br />
    */
   public function loadDesign($namespace, $design) {

      $this->document = new Document();

      // set the current context
      $context = $this->getContext();
      if (empty($context)) {
         $this->document->setContext($namespace);
      } else {
         $this->document->setContext($context);
      }

      // set the current language
      $this->document->setLanguage($this->getLanguage());

      // load the design
      $this->document->loadDesign($namespace, $design);
      $this->document->setObjectId(XmlParser::generateUniqID());
   }

   /**
    * @public
    *
    * Transforms the APF DOM tree of the current page. Returns the content of the transformed document.
    *
    * @return string The content of the transformed page
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 28.12.2006<br />
    * Version 0.2, 03.01.2007 (Introduced URL rewriting)<br />
    * Version 0.3, 08.06.2007 (Moved the URL rewriting into a filter)<br />
    * Version 0.4, 11.12.2008 (Switched to the new input filter concept)<br />
    */
   public function transform() {
      return $this->document->transform();
   }

}
