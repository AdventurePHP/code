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

/**
 * @package extensions::htmllist::taglib
 * @class list_control
 *
 * Implements a base class for all HTMLList elements.
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
abstract class list_control extends Document {

   /**
    * Adds an attribute
    * @param string $name
    * @param $value
    */
   public function addAttribute($name, $value) {
      if (isset($this->__Attributes[$name])) {
         $this->__Attributes[$name] .= $value;
      } else {
         $this->__Attributes[$name] = $value;
      }
   }

   protected function getClassNameByTagLibName($name) {

      foreach ($this->__TagLibs as $tagLib) {
         if ($tagLib->getName() == $name) {
            return $tagLib->getClass();
         }
      }

      return null;
   }

   /**
    * Set's a place holder within the html list container.
    *
    * @param string $name The name of the place holder.
    * @param string $value The value of the place holder.
    * @return list_control This instance for further usage.
    * @throws Exception In case no place holder can be found.
    */
   public function &setPlaceHolder($name, $value) {
      // dynamically gather taglib name of the place holder to set
      $tagLibClass = $this->getClassNameByTagLibName('placeholder');

      $placeHolderCount = 0;
      if (count($this->__Children) > 0) {
         foreach ($this->__Children as $objectId => $DUMMY) {
            if (get_class($this->__Children[$objectId]) == $tagLibClass) {
               if ($this->__Children[$objectId]->getAttribute('name') == $name) {
                  $this->__Children[$objectId]->setContent($value);
                  $placeHolderCount++;
               }
            }
         }
      } else {
         throw new Exception('[' . get_class($this) . '::setPlaceHolder()] No place holder object with '
                  . 'name "' . $name . '" composed in current for document controller "'
                  . ($this->__ParentObject->getDocumentController()) . '"! Perhaps tag library '
                  . 'form:placeholder is not loaded in form "' . $this->getAttribute('name') . '"!',
            E_USER_ERROR);
      }

      if ($placeHolderCount < 1) {
         throw new Exception('[' . get_class($this) . '::setPlaceHolder()] There are no place holders '
               . 'found for name "' . $name . '" in template "' . ($this->__Attributes['name'])
               . '" in document controller "' . ($this->__ParentObject->getDocumentController())
               . '"!', E_USER_WARNING);
      }

      return $this;
   }

   /**
    * @protected
    *
    * This method is for convenient setting of multiple place holders. The applied
    * array must contain a structure like this:
    * <code>
    * array(
    *    'key-a' => 'value-a',
    *    'key-b' => 'value-b',
    *    'key-c' => 'value-c',
    *    'key-d' => 'value-d',
    *    'key-e' => 'value-e',
    * )
    * </code>
    * Thereby, the <em>key-*</em> offsets define the name of the place holders, their
    * values are used as the place holder's values.
    *
    * @param array $placeHolderValues Key-value-couples to fill place holders.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2010<br />
    */
   public function setPlaceHolders(array $placeHolderValues) {
      foreach ($placeHolderValues as $key => $value) {
         $this->setPlaceHolder($key, $value);
      }
   }

}

/**
 * @package extensions::htmllist::taglib
 * @class AbstractTaglibList
 *
 * Abstract class for list classes.
 *
 * @author Florian Horn
 * @version 1.0, 03.04.2010<br />
 */
abstract class AbstractTaglibList extends list_control {

   public function onParseTime() {
      $this->__extractTagLibTags();
   }

   /**
    * Creates the html code of the list.
    *
    * @return string The list' html representation.
    */
   public function transform() {
      // --- Checks if list elements exist
      if (count($this->__Children) > 0) {
         // --- Run through list elements
         $this->transformChildren();

         // --- Create list
         $this->__Content = '<' . $this->__getListIdentifier() . ' '
               . $this->getAttributesAsString($this->__Attributes) . '>'
               . $this->__Content . '</' . $this->__getListIdentifier() . '>';
      }

      return $this->__Content;
   }

   /**
    * Returns the html list identifier (e.g. <em>li</em>, ...).
    *
    * @return string The tag name of the list identifier.
    */
   abstract protected function __getListIdentifier();

   /**
    * Adds a list element.
    * @param string $content The content of the element.
    * @param string $cssClass The name of the implementation class.
    * @param string $elementName The name of the tag (e.g. "elem_list" for list_taglib_elem_list class).
    */
   protected function __addElement($content, $cssClass, $elementName) {

      $objectId = XmlParser::generateUniqID();

      $fullClassName = 'list_taglib_' . $elementName;

      $this->__Children[$objectId] = new $fullClassName();
      $this->__Children[$objectId]->setObjectId($objectId);
      $this->__Children[$objectId]->setContent($content);
      $this->__Children[$objectId]->setLanguage($this->__Language);
      $this->__Children[$objectId]->setContext($this->__Context);

      if (!empty($cssClass)) {
         $this->__Children[$objectId]->setAttribute('class', $cssClass);
      }

      $this->__Children[$objectId]->onParseTime();

      // inject parent object (=this) to guarantee native DOM tree environment
      $this->__Children[$objectId]->setParentObject($this);
      $this->__Children[$objectId]->onAfterAppend();

      // add xml marker, necessary for transformation
      $this->__Content .= '<' . $objectId . ' />';
   }

}
