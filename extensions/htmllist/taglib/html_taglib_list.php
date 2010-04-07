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

   import('extensions::htmllist::taglib','list_control');
   import('extensions::htmllist::taglib','list_taglib_definition');
   import('extensions::htmllist::taglib','list_taglib_ordered');
   import('extensions::htmllist::taglib','list_taglib_unordered');

   /**
    * @namespace extensions::htmllist::taglib
    * @class html_taglib_list
    *
    * Represents a HTMLList list (DOM node).
    *
    * @author Florian Horn
    * @version 1.0, 03.04.2010<br />
    */
   class html_taglib_list extends list_control {
      
      /**
       * Adds a list
       * @param string $elementType
       * @param array $elementAttributes
       * @return string
       */
      public function addList( $elementType , $elementAttributes = array() ) {
         // create form element
         $objectId = $this->__createList( $elementType , $elementAttributes );

         // add form element if id is null
         if($objectId === null) {
            // notify user and return null
            throw new Exception('[html_taglib_list::addList()] List element "'.$elementType
                    .'" cannot be added due to previous errors!');
            return null;
         }

         // add position placeholder to the content
         $this->__Content .= '<'.$objectId.' />';

         // return object id of the new form element
         return $objectId;
      }

      /**
       * Get a list by its identifier
       * @param string $sId
       * @return AbstractTaglibList
       */
      public function getListById( $sId ) {
         if(count($this->__Children) > 0) {
            foreach($this->__Children as $objectID => $DUMMY) {
               if($this->__Children[$objectID]->getAttribute('id') == $sId) {
                  return $this->__Children[$objectID];
               }
            }
         }

         // display extended debug message in case no form element was found
         $parent = $this->get('ParentObject');
         $grandParent = $parent->get('ParentObject');
         $docCon = ($grandParent !== null) ? $grandParent->get('DocumentController') : $docCon = 'n/a';

         throw new Exception('[html_taglib_list::getListById()] No list with id "'.$id
                 .'" in document controller "'.$docCon.'"!',E_USER_ERROR);
         exit();
      }

      /**
       * Creates a list
       * @param string $elementType
       * @param array $elementAttributes
       * @return string
       */
      protected function __createList( $elementType , $elementAttributes = array() ) {
         // define taglib class
         $tagLibClass = str_replace(':','_taglib_',$elementType);

         // check, if class exists
         if(class_exists($tagLibClass)) {
            // generate object id
            $objectId = XmlParser::generateUniqID();

            // create new form element
            $listObject = new $tagLibClass();

            // add standard and user defined attributes
            $listObject->set('ObjectID',$objectId);
            $listObject->set('Language',$this->__Language);
            $listObject->set('Context',$this->__Context);

            foreach($elementAttributes as $Key => $Value) {
               $listObject->setAttribute($Key,$Value);
            }

            // add form element to DOM tree and call the onParseTime() method
            $listObject->setByReference('ParentObject',$this);
            $listObject->onParseTime();

            // add new form element to children list
            $this->__Children[$objectId] = $listObject;

            // call the onAfterAppend() method
            $this->__Children[$objectId]->onAfterAppend();

            // return object id for further addressing
            return $objectId;
         }
         else {
            // throw error and return null as object id
            throw new Exception('[html_taglib_list::__createList()] No list element with name "'
                    .$elementType.'" found! Maybe the tag name is misspellt or the class is not '
                    .'imported yet. Please use import() or &lt;list:addtaglib /&gt;!');
            return null;
         }
      }
      
   }
?>