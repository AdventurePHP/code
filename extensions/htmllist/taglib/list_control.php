<?php
namespace APF\extensions\htmllist\taglib;

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
use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\XmlParser;

/**
 * @package APF\extensions\htmllist\taglib
 * @class list_control
 *
 * Implements a base class for all HTMLList elements.
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
abstract class list_control extends Document {
}

/**
 * @package APF\extensions\htmllist\taglib
 * @class AbstractTaglibList
 *
 * Abstract class for list classes.
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
abstract class AbstractTaglibList extends list_control {

   public function onParseTime() {
      $this->extractTagLibTags();
   }

   /**
    * Creates the html code of the list.
    *
    * @return string The list' html representation.
    */
   public function transform() {
      // --- Checks if list elements exist
      if (count($this->children) > 0) {
         // --- Run through list elements
         $this->transformChildren();

         // --- Create list
         $this->content = '<' . $this->getListIdentifier() . ' '
               . $this->getAttributesAsString($this->attributes) . '>'
               . $this->content . '</' . $this->getListIdentifier() . '>';
      }

      return $this->content;
   }

   /**
    * Returns the html list identifier (e.g. <em>li</em>, ...).
    *
    * @return string The tag name of the list identifier.
    */
   abstract protected function getListIdentifier();

   /**
    * Adds a list element.
    * @param string $content The content of the element.
    * @param string $cssClass The name of the implementation class.
    * @param string $elementName The name of the tag (e.g. "elem_list" for list_taglib_elem_list class).
    */
   protected function addElement($content, $cssClass, $elementName) {

      $objectId = XmlParser::generateUniqID();

      $fullClassName = 'list_taglib_' . $elementName;

      $this->children[$objectId] = new $fullClassName();
      $this->children[$objectId]->setObjectId($objectId);
      $this->children[$objectId]->setContent($content);
      $this->children[$objectId]->setLanguage($this->language);
      $this->children[$objectId]->setContext($this->context);

      if (!empty($cssClass)) {
         $this->children[$objectId]->setAttribute('class', $cssClass);
      }

      $this->children[$objectId]->onParseTime();

      // inject parent object (=this) to guarantee native DOM tree environment
      $this->children[$objectId]->setParentObject($this);
      $this->children[$objectId]->onAfterAppend();

      // add xml marker, necessary for transformation
      $this->content .= '<' . $objectId . ' />';
   }

}
