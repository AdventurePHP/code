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
    * @namespace extensions::htmllist::taglib
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
      public function addAttribute( $name , $value ) {
         if( isset( $this->__Attributes[$name] ) ) {
            $this->__Attributes[$name] .= $value;
         }
         else {
            $this->__Attributes[$name] = $value;
         }
      }

      /**
       * Set's a place holder within the html list container.
       *
       * @param string $name The name of the place holder.
       * @param string $value The value of the place holder.
       */
      public function setPlaceHolder( $name , $value ) {
         // dynamically gather taglib name of the place holder to set
         $tagLibClass = $this->__getClassNameByTagLibClass('placeholder');

         $placeHolderCount = 0;
         if(count($this->__Children) > 0) {
            foreach($this->__Children as $objectId => $DUMMY) {
               if(get_class($this->__Children[$objectId]) == $tagLibClass) {
                  if($this->__Children[$objectId]->getAttribute('name') == $name) {
                     $this->__Children[$objectId]->setContent($value);
                     $placeHolderCount++;
                  }
               }
            }
         }
         else {
            throw new Exception('['.get_class($this).'::setPlaceHolder()] No place holder object with '
                    .'name "'.$name.'" composed in current for document controller "'
                    .($this->__ParentObject->getDocumentController()).'"! Perhaps tag library '
                    .'form:placeholder is not loaded in form "'.$this->getAttribute('name').'"!',
            E_USER_ERROR);
            exit();
         }

         if($placeHolderCount < 1) {
            throw new Exception('['.get_class($this).'::setPlaceHolder()] There are no place holders '
                    .'found for name "'.$name.'" in template "'.($this->__Attributes['name'])
                    .'" in document controller "'.($this->__ParentObject->getDocumentController())
                    .'"!',E_USER_WARNING);
         }
      }

      /**
       * @protected
       *
       * This method is for concenient setting of multiple place holders. The applied
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
       * Thereby, the <em>key-*</em> offsets define the name of the place holders, theire
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
    * @namespace extensions::htmllist::taglib
    * @class AbstractTaglibList
    *
    * Abstractive class for the concrete list-classes.
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
         if(count($this->__Children) > 0) {
            // --- Run through list elements
            foreach($this->__Children as $objectId => $DUMMY) {
               $this->__Content = str_replace('<'.$objectId.' />',
                       $this->__Children[$objectId]->transform(),$this->__Content);
            }

            // --- Create list
            $this->__Content = '<'.$this->__getListIdentifier().' '
                    .$this->__getAttributesAsString($this->__Attributes).'>'
                    .$this->__Content.'</'.$this->__getListIdentifier().'>';
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
       * @param string $sContent
       * @param string $sClass
       */
      protected function __addElement( $sContent , $sClass , $sElement ) {
         $objectId = XmlParser::generateUniqID();
         $sClassname = 'list_taglib_'.$sElement;
         $this->__Children[$objectId] = new $sClassname;

         $this->__Children[$objectId]->setObjectId($objectId);
         $this->__Children[$objectId]->setContent($sContent);
         $this->__Children[$objectId]->setLanguage($this->__Language);
         $this->__Children[$objectId]->setContext($this->__Context);

         if( !empty( $sClass ) ) {
            $this->__Children[$objectId]->setAttribute('class',$sClass);
         }

         $this->__Children[$objectId]->onParseTime();

         // inject parent object (=this) to guarantee native DOM tree environment
         $this->__Children[$objectId]->setParentObject($this);
         $this->__Children[$objectId]->onAfterAppend();

         // add xml marker, necessary for transformation
         $this->__Content .= '<'.$objectId.' />';
      }
      
   }
?>