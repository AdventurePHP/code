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
use APF\core\pagecontroller\XmlParser;
use APF\core\pagecontroller\Document;
use APF\extensions\htmllist\taglib\list_control;

/**
 * @package APF\extensions\htmllist\taglib
 * @class html_taglib_list
 *
 * Represents a HTMLList list (DOM node).
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
class html_taglib_list extends list_control {

   /**
    * Adds a list.
    *
    * @param string $elementType The type of list.
    * @param array $elementAttributes The attributes of the list.
    * @return string The object id of the list.
    */
   public function addList($elementType, $elementAttributes = array()) {

      // create list element
      $objectId = $this->createList($elementType, $elementAttributes);

      // add position placeholder to the content
      $this->content .= '<' . $objectId . ' />';

      // return object id of the new list element
      return $objectId;
   }

   /**
    * Get a list by its identifier.
    *
    * @param string $id The id of the list.
    * @return AbstractTaglibList The desired instance.
    * @throws \InvalidArgumentException In case no list can be found.
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
      $parent = $this->getParentObject();
      $grandParent = $parent->getParentObject();
      $docCon = ($grandParent !== null) ? $grandParent->getDocumentController() : $docCon = 'n/a';

      throw new \InvalidArgumentException('[html_taglib_list::getListById()] No list with id "' . $id
         . '" in document controller "' . $docCon . '"!', E_USER_ERROR);
   }

   /**
    * Creates a list.
    *
    * @param string $elementType The type of list to create (ul, ol).
    * @param array $elementAttributes The attributes of the list.
    * @return string The object id of the created list.
    * @throws \InvalidArgumentException In case the desired list cannot be created.
    */
   protected function createList($elementType, array $elementAttributes = array()) {

      $tagLibClass = str_replace(':', '_taglib_', $elementType);

      // check, if class exists
      if (class_exists($tagLibClass)) {
         // generate object id
         $objectId = XmlParser::generateUniqID();

         /* @var $listObject Document */
         $listObject = new $tagLibClass();

         // add standard and user defined attributes
         $listObject->setObjectId($objectId);
         $listObject->setLanguage($this->language);
         $listObject->setContext($this->context);

         foreach ($elementAttributes as $Key => $Value) {
            $listObject->setAttribute($Key, $Value);
         }

         // add list element to DOM tree and call the onParseTime() method
         $listObject->setParentObject($this);
         $listObject->onParseTime();

         // add new list element to children list
         $this->children[$objectId] = $listObject;

         // call the onAfterAppend() method
         $this->children[$objectId]->onAfterAppend();

         // return object id for further addressing
         return $objectId;
      } else {
         // throw error and return null as object id
         throw new \InvalidArgumentException('[html_taglib_list::createList()] No list element with name "'
            . $elementType . '" found! Maybe the tag name is mis-spelt. Please use &lt;list:addtaglib /&gt;!');
      }
   }

}
