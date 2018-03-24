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
namespace APF\extensions\htmllist\taglib;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\DomNode;
use APF\core\pagecontroller\XmlParser;
use InvalidArgumentException;

/**
 * Represents a HTMLList list (DOM node).
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
class HtmlListTag extends Document {

   protected $listTypes = [
         'list:unordered' => UnorderedListTag::class,
         'list:ordered' => OrderedListTag::class,
         'list:definition' => DefinitionListTag::class
   ];

   /**
    * Adds a list.
    *
    * @param string $elementType The type of list.
    * @param array $elementAttributes The attributes of the list.
    *
    * @return string The object id of the list.
    */
   public function addList($elementType, array $elementAttributes = []) {

      // create list element
      $objectId = $this->createList($elementType, $elementAttributes);

      // add position placeholder to the content
      $this->content .= '<' . $objectId . ' />';

      // return object id of the new list element
      return $objectId;
   }

   /**
    * Creates a list.
    *
    * @param string $elementType The type of list to create (ul, ol).
    * @param array $elementAttributes The attributes of the list.
    *
    * @return string The object id of the created list.
    * @throws InvalidArgumentException In case the desired list cannot be created.
    */
   protected function createList($elementType, array $elementAttributes = []) {

      if (!isset($this->listTypes[$elementType])) {
         throw new InvalidArgumentException('[HtmlListTag::createList()] Invalid element type found. '
               . 'Supported element types: ' . implode(', ', array_keys($this->listTypes)));
      }

      $tagClass = $this->listTypes[$elementType];

      // generate object id
      $objectId = XmlParser::generateUniqID();

      /* @var $listObject DomNode */
      $listObject = new $tagClass();

      // add standard and user defined attributes
      $listObject->setObjectId($objectId);
      $listObject->setLanguage($this->language);
      $listObject->setContext($this->context);

      foreach ($elementAttributes as $key => $value) {
         $listObject->setAttribute($key, $value);
      }

      // add list element to DOM tree and call the onParseTime() method
      $listObject->setParent($this);
      $listObject->onParseTime();

      // add new list element to children list
      $this->children[$objectId] = $listObject;

      // call the onAfterAppend() method
      $this->children[$objectId]->onAfterAppend();

      // return object id for further addressing
      return $objectId;
   }

   /**
    * Get a list by its identifier.
    *
    * @param string $id The id of the list.
    *
    * @return AbstractListTag The desired instance.
    * @throws InvalidArgumentException In case no list can be found.
    */
   public function getListById($id) {
      if (count($this->children) > 0) {
         foreach ($this->children as $objectID => $DUMMY) {
            if ($this->children[$objectID]->getAttribute('id') == $id) {
               return $this->children[$objectID];
            }
         }
      }

      // display extended debug message in case no list element was found
      $parent = $this->getParent();
      $grandParent = $parent->getParent();
      $docCon = ($grandParent !== null) ? get_class($grandParent->getDocumentController()) : 'n/a';

      throw new InvalidArgumentException('[HtmlListTag::getListById()] No list with id "' . $id
            . '" in document controller "' . $docCon . '"!', E_USER_ERROR);
   }

}
