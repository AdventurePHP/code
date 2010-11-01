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

   import('tools::form::taglib','form_control');
   import('tools::form::taglib','form_control_observer');
   
   import('tools::form::taglib','form_taglib_text');
   import('tools::form::taglib','form_taglib_select');
   import('tools::form::taglib','form_taglib_date');
   import('tools::form::taglib','form_taglib_placeholder');
   import('tools::form::taglib','form_taglib_password');
   import('tools::form::taglib','form_taglib_hidden');
   import('tools::form::taglib','form_taglib_checkbox');
   import('tools::form::taglib','form_taglib_radio');
   import('tools::form::taglib','form_taglib_file');
   import('tools::form::taglib','form_taglib_area');
   import('tools::form::taglib','form_taglib_multiselect');
   import('tools::form::taglib','form_taglib_getstring');
   import('tools::form::taglib','form_taglib_addtaglib');
   import('tools::form::taglib','form_taglib_marker');

   import('tools::form::taglib','form_taglib_button');
   import('tools::form::taglib','form_taglib_reset');
   import('tools::form::taglib','form_taglib_imagebutton');

   import('tools::form::taglib','form_taglib_addfilter');
   import('tools::form::taglib','form_taglib_addvalidator');
   import('tools::form::taglib','form_taglib_listener');
   import('tools::form::taglib','form_taglib_error');
   import('tools::form::taglib','form_taglib_success');

   import('tools::form::taglib','form_taglib_timecaptcha');

   /**
    * @package tools::form::taglib
    * @class html_taglib_form
    *
    * Represents a APF form element (DOM node).
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 12.01.2007 (Form is now handled as a template)<br />
    * Version 0.3, 13.01.2007 (Added mode taglibs)
    * Version 0.4, 15.01.2007 (Added the "form:multiselect" taglib)<br />
    * Version 0.5, 11.02.2007 (Added the "form:validate" taglib)<br />
    * Version 0.6, 25.06.2007 (Replaced "form:validate" with "form:valgroup")<br />
    * Version 0.7, 14.04.2007 (Added "isSent" attribute)<br />
    * Version 0.8, 22.09.2007 (Added the generic validator)<br />
    * Version 0.9, 01.06.2008 (Added the getFormElementsByType() method)<br />
    * Version 1.0, 16.06.2008 (API change: added getFormElementsByTagName())<br />
    * Version 1.1, 30.12.2009 (Added the form:success tag)<br />
    */
   class html_taglib_form extends form_control {

      /**
       * @protected
       * Indicates, whether the form should be transformed at it'd place of definition or not.
       * @var boolean
       */
      protected $__TransformOnPlace = false;

      /**
       * @public
       *
       * Initializes the known taglibs.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.01.2007<br />
       * Version 0.2, 13.01.2007<br />
       * Version 0.3, 15.01.2007 (Added the form:multiselect tag)<br />
       * Version 0.4, 11.02.2007 (Added the form:validate tag)<br />
       * Version 0.5, 03.03.2007 (Removed the "&" before the "new" operator)<br />
       * Version 0.6, 25.03.2007 (Added the form:valgroup tag)<br />
       * Version 0.7, 22.09.2007 (Added the generic validator tag)<br />
       * Version 0.8, 06.11.2007 (Added the form:getstring tag)<br />
       * Version 0.9, 11.07.2008 (Added the form:addtaglib tag)<br />
       * Version 1.0, 03.09.2008 (Added the form:marker tag)<br />
       * Version 1.1, 22.06.2009 (Added the form:reset tag)<br />
       * Version 1.2, 03.09.2009 (Added several new tags concerning the refactoring)<br />
       * Version 1.3, 15.06.2010 (Bugfix: white listing did not recognize enctype attribute)<br />
       */
      public function __construct(){

         // Place the listener here, to ensure, that it is there, when the
         // notification is sent!
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','listener');

         // Please note, that the form:addfilter taglib is placed before the
         // form:addvalidator, because filtering must take place before the
         // validation. Otherwise, we might get unexpected results.
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','addfilter');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','addvalidator');

         // The time of adding the form errors is not relevant, because the action
         // takes place on transform time. But for clarity, we add it near the listeners.
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','error');

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','success');

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','button');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','reset');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','imagebutton');

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','text');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','select');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','date');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','placeholder');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','password');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','hidden');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','checkbox');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','radio');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','file');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','area');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','multiselect');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','getstring');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','marker');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','addtaglib');

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','timecaptcha');

         // add additional attributes to whitelist
         $this->attributeWhiteList[] = self::$METHOD_ATTRIBUTE_NAME;
         $this->attributeWhiteList[] = 'action';
         $this->attributeWhiteList[] = 'enctype';
         $this->attributeWhiteList[] = 'accept-charset'; // to explicitly specify an encoding

       // end function
      }

      /**
       * @public
       *
       * Parses the known taglibs.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 05.01.2007<br />
       */
      public function onParseTime(){

         // add default method for convenience
         $method = $this->getAttribute(self::$METHOD_ATTRIBUTE_NAME);
         if ($method === null) {
            $this->setAttribute(
                    self::$METHOD_ATTRIBUTE_NAME,
                    strtolower(
                            Registry::retrieve('apf::tools', 'FormDefaultMethod', self::$METHOD_POST_VALUE_NAME)
                    )
            );
         }

         $this->__extractTagLibTags();

      }

      /**
       * @public
       *
       * Indicates, whether the form has been sent or not. Retrieves the status
       * directly from the form controls. Overwrites the parent's method.
       * 
       * @return boolean True, in case the form is sent, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function isSent(){

         foreach($this->__Children as $objectId => $DUMMY){
            if($this->__Children[$objectId]->isSent() === true){
               return true;
            }
         }

         return false;

       // end function
      }

      /**
       * @public
       *
       * Indicates, whether the form is valid or not. Retrieves the status
       * directly from the form controls. Overwrites the parent's method.
       *
       * @return boolean True, in case the form is valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function isValid(){

         foreach($this->__Children as $objectId => $DUMMY){
            if($this->__Children[$objectId]->isValid() === false){
               return false;
            }
         }

         return true;

       // end function
      }

      /**
       * @public
       *
       * Adds a new form element at the end of the form. This method is intended to dynamically generate forms.
       *
       * @param string $elementType Type of the element (e.g. "form:text")
       * @param string[] $elementAttributes Associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
       * @return string Id of the new form object or null (e.g. for addressing the new element)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.01.2007<br />
       * Version 0.2, 05.09.2008 (The new form element now gets the current context and language)<br />
       * Version 0.3, 06.09.2008 (API change: now the tag name (e.g. "form:text") is expected as an argument)<br />
       * Version 0.4, 10.09.2008 (Added the $ElementAttributes param)<br />
       */
      public function addFormElement($elementType,$elementAttributes = array()){

         // create form element
         $objectId = $this->__createFormElement($elementType,$elementAttributes);

         // add form element if id is not null
         if($objectId !== null){

            // add position placeholder to the content
            $this->__Content .= '<'.$objectId.' />';

            // return object id of the new form element
            return $objectId;

         } else {
            throw new FormException('[html_taglib_form::addFormElement()] Form element "'.$elementType
               .'" cannot be added due to previous errors!');
         }

       // end function
      }

      /**
       * @public
       *
       * Adds content at the end of the form. This method is intended to dynamically generate forms.
       *
       * @param string $content The desired content
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.01.2007<br />
       */
      public function addFormContent($content){
         $this->__Content .= $content;
      }

      /**
       * @public
       *
       * Adds content in front of a form marker. This method is intended to dynamically generate forms.
       *
       * @param string $markerName the desired marker name
       * @param string $content the content to add
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.09.2008<br />
       */
      public function addFormContentBeforeMarker($markerName,$content){

         // get desired marker
         $marker = &$this->__getMarker($markerName);

         // check if marker exists
         if($marker !== null){

            // get the object if
            $objectId = $marker->getObjectId();

            // add the desired content before the marker
            $this->__Content = str_replace('<'.$objectId.' />',$content.'<'.$objectId.' />',$this->__Content);

         } else {

            throw new FormException('[html_taglib_form::addFormContentBeforeMarker()] No marker object '
               .'with name "'.$markerName.'" composed in current form for document controller "'
               .($this->getParentObject()->getDocumentController()).'"! Please check the definition of '
               .'the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);

         }

      }

      /**
       * @public
       *
       * Adds content behind a form marker. This method is intended to dynamically generate forms.
       *
       * @param string $markerName the desired marker name
       * @param string $content the content to add
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.09.2008<br />
       */
      public function addFormContentAfterMarker($markerName,$content){

         // get desired marker
         $marker = &$this->__getMarker($markerName);

         // check if marker exists
         if($marker !== null){

            // get the object if
            $objectId = $marker->getObjectId();

            // add the desired content before the marker
            $this->__Content = str_replace('<'.$objectId.' />','<'.$objectId.' />'.$content,$this->__Content);

         } else {

            throw new FormException('[html_taglib_form::addFormContentAfterMarker()] No marker object '
               .'with name "'.$markerName.'" composed in current form for document controller "'
               .($this->getParentObject()->getDocumentController()).'"! Please check the definition of '
               .'the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);

         }

      }

      /**
       * @public
       *
       * Adds a new form element in front of a form marker. This method is intended to dynamically generate forms.
       *
       * @param string $markerName the desired marker name
       * @param string $elementType type of the element (e.g. "form:text")
       * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
       * @return string Id of the new form object or null (e.g. for addressing the new element)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.09.2008<br />
       * Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
       */
      public function addFormElementBeforeMarker($markerName,$elementType,$elementAttributes = array()){

         // create new form element
         $objectId = $this->__createFormElement($elementType,$elementAttributes);

         // add form element if id is not null
         if($objectId !== null){

            // get desired marker
            $marker = &$this->__getMarker($markerName);
            if($marker === null){
               throw new FormException('[html_taglib_form::addFormElementBeforeMarker()] No marker object '
                  .'with name "'.$markerName.'" composed in current form for document controller "'
                  .($this->getParentObject()->getDocumentController()).'"! Please check the definition of '
                  .'the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            }

            // add the position placeholder to the content
            $markerId = $marker->getObjectId();
            $this->__Content = str_replace('<'.$markerId.' />','<'.$objectId.' /><'.$markerId.' />',$this->__Content);

            // return object id of the new form element
            return $objectId;

         } else {

            // notify user and return null
            throw new FormException('[html_taglib_form::addFormElementBeforeMarker()] Form element "'
               .$elementType.'" cannot be added due to previous errors!');

         }

      }

      /**
       * @public
       *
       * Adds a new form element after a form marker. This method is intended to dynamically generate forms.
       *
       * @param string $markerName the desired marker name
       * @param string $elementType type of the element (e.g. "form:text")
       * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
       * @return string Id of the new form object or null (e.g. for addressing the new element)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 05.09.2008<br />
       * Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
       */
      public function addFormElementAfterMarker($markerName,$elementType,$elementAttributes = array()){

         // create new form element
         $objectId = $this->__createFormElement($elementType,$elementAttributes);

         // add form element if id is not null
         if($objectId !== null){

            // get desired marker
            $marker = &$this->__getMarker($markerName);
            if($marker === null){
               throw new FormException('[html_taglib_form::addFormElementAfterMarker()] No marker object '
                  .'with name "'.$markerName.'" composed in current form for document controller "'
                  .($this->getParentObject()->getDocumentController()).'"! Please check the definition of '
                  .'the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            }

            // add the position placeholder to the content
            $markerId = $marker->getObjectId();
            $this->__Content = str_replace(
               '<'.$markerId.' />','<'.$markerId.' /><'.$objectId.' />',
               $this->__Content
            );

            // return object id of the new form element
            return $objectId;

         } else {
            throw new FormException('[html_taglib_form::addFormElementBeforeMarker()] Form element "'.$elementType.'" cannot be added due to previous errors!');
         }

      }

      /**
       * @protected
       *
       * Adds a new form element to the child list.
       *
       * @param string $elementType type of the element (e.g. "form:text")
       * @param array $elementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
       * @return string Id of the new form object (e.g. for addressing the new element)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.09.2008<br />
       * Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
       * Version 0.3, 12.11.2008 (Bugfix: language and context initialisation were wrong)<br />
       */
      protected function __createFormElement($elementType,$elementAttributes = array()){

         // define taglib class
         $tagLibClass = str_replace(':','_taglib_',$elementType);

         // check, if class exists
         if(class_exists($tagLibClass)){

            // generate object id
            $objectId = XmlParser::generateUniqID();

            // create new form element
            $formObject = new $tagLibClass();

            // add standard and user defined attributes
            $formObject->setObjectId($objectId);
            $formObject->setLanguage($this->__Language);
            $formObject->setContext($this->__Context);

            foreach($elementAttributes as $key => $value){
               $formObject->setAttribute($key,$value);
             // end foreach
            }

            // add form element to DOM tree and call the onParseTime() method
            $formObject->setParentObject($this);
            $formObject->onParseTime();

            // add new form element to children list
            $this->__Children[$objectId] = $formObject;

            // call the onAfterAppend() method
            $this->__Children[$objectId]->onAfterAppend();

            // return object id for further addressing
            return $objectId;

         } else {
            throw new FormException('[html_taglib_form::__createFormElement()] No form element with name "'
               .$elementType.'" found! Maybe the tag name is misspellt or the class is not '
               .'imported yet. Please use import() or &lt;form:addtaglib /&gt;!');
         }

       // end function
      }

      /**
       * @protected
       *
       * Returns a reference on the desired marker or null.
       *
       * @param string $markerName the desired marker name
       * @return form_taglib_marker $Marker the marker or null
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.09.2008<br />
       */
      protected function &__getMarker($markerName){

         // check, weather the form has children
         if(count($this->__Children) > 0){

            // have a look at the children
            foreach($this->__Children as $objectId => $DUMMY){

               // check, if current children is a marker
               if(get_class($this->__Children[$objectId]) == 'form_taglib_marker'){

                  // check, if the name fits the method's argument
                  if($this->__Children[$objectId]->getAttribute('name') == $markerName){
                     return $this->__Children[$objectId];
                  }

               }

            }

            // return null, if no child was found (with a quick hack)
            $null = null;
            return $null;

         } else {

            // return null (with a quick hack)
            $null = null;
            return $null;

         }

      }

      /**
       * @public
       *
       * Sets the action url of the form.
       *
       * @param string $action The action URL of the form.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 07.01.2007<br />
       */
      public function setAction($action){
         $this->setAttribute('action', $action);
      }

      /**
       * @public
       *
       * Returns a reference on the form element identified by the given name.
       *
       * @param string $name The name of the desired form element.
       * @return form_control A reference on the form element.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 12.09.2009 (Corrected debug message)<br />
       */
      public function &getFormElementByName($name){

         if(count($this->__Children) > 0){
            foreach($this->__Children as $objectId => $DUMMY){
               if($this->__Children[$objectId]->getAttribute('name') == $name){
                  return $this->__Children[$objectId];
               }
            }

         }

         // display extended debug message in case no form element was found
         $parent = &$this->getParentObject();
         $docCon = $parent->getDocumentController();
         throw new FormException('[html_taglib_form::getFormElementByName()] No form element with name "'
            .$name.'" composed in current form "'.$this->getAttribute('name')
            .'" in document controller "'.$docCon.'". Please double-check your taglib definitions '
            .'within this form (especially attributes, that are used for referencing other form '
            .'controls)!',E_USER_ERROR);

      }

      /**
       * @public
       *
       * Returns a list of form controls with the given name.
       *
       * @param string $name The name of the form elements to collect (e.g. for radio buttons).
       * @return form_control[] The list of form controls with the given name.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.08.2010<br />
       */
      public function &getFormElementsByName($name){
         $elements = array();
         if(count($this->__Children) > 0){
            foreach($this->__Children as $objectId => $DUMMY){
               if($this->__Children[$objectId]->getAttribute('name') == $name){
                  $elements[] = &$this->__Children[$objectId];
               }
            }
         }
         return $elements;
      }

      /**
       * @public
       *
       * Returns a reference on the form element identified by the given id.
       *
       * @param string $ID The ID of the desired form element.
       * @return form_control A reference on the form element.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 21.01.2007<br />
       */
      public function &getFormElementByID($id){

         if(count($this->__Children) > 0){

            foreach($this->__Children as $objectId => $DUMMY){
               if($this->__Children[$objectId]->getAttribute('id') == $id){
                  return $this->__Children[$objectId];
               }
            }

         }

         // display extended debug message in case no form element was found
         $parent = &$this->getParentObject();
         $grandParent = &$parent->getParentObject();
         if($grandParent !== null){
            $docCon = $grandParent->getDocumentController();
         }
         else {
            $docCon = 'n/a';
         }
         throw new FormException('[html_taglib_form::getFormElementByID()] No form element with id "'
            .$id.'" composed in current form "'.$parent->getAttribute('name')
            .'" in document controller "'.$docCon.'"!',E_USER_ERROR);

      }

      /**
       * @public
       *
       * Returns a reference on a form element addressed by it's internal object id.
       *
       * @param string $objectId The object id of of the desired form element.
       * @return form_control A reference on the form element.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 12.01.2007 (Corrected error message)<br />
       * Version 0.3, 06.09.2008 (Corrected error message again)<br />
       */
      public function &getFormElementByObjectID($objectId){

         if(isset($this->__Children[$objectId])){
            return $this->__Children[$objectId];
         }

         // note, that no suitable child has been found
         $parent = &$this->getParentObject();
         $documentController = $parent->getDocumentController();
         throw new FormException('[html_taglib_form::getFormElementByObjectID()] No form element with id "'
            .$objectId.'" composed in current form "'.$this->__Attributes['name']
            .'" in document controller "'.$documentController.'"!',E_USER_ERROR);

       // end function
      }

      /**
       * @public
       *
       * Returns a list of form elements addressed by their tag name.
       *
       * @param string $tagName The tag name of the desired form element (e.g. "form:text").
       * @return form_control[] A list of references on the form elements.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 14.06.2008 (API change: do use this function instead of getFormElementsByType()!)<br />
       */
      public function &getFormElementsByTagName($tagName){

         $colon = strpos($tagName,':');
         $tagClassName = trim(substr($tagName,0,$colon)).'_taglib_'.trim(substr($tagName,$colon + 1));

         if(count($this->__Children) > 0){

            $formElements = array();
            foreach($this->__Children as $objectId => $DUMMY){

               if(get_class($this->__Children[$objectId]) == $tagClassName){
                  $formElements[] = &$this->__Children[$objectId];
               }

            }

            return $formElements;

         }

         // display extended debug message in case no form elements were found
         $parent = &$this->getParentObject();
         $grandParent = &$parent->getParentObject();
         $documentController = $grandParent->getDocumentController();
         throw new FormException('[html_taglib_form::getFormElementsByType()] No form elements composed in '.
            'current form "'.$this->__Attributes['name'].'" in document controller "'
            .$documentController.'"!',E_USER_ERROR);

       // end function
      }

      /**
       * @public
       *
       * Returns the content of the transformed form.
       *
       * @return string The content of the transformed form.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.01.2007<br />
       * Version 0.2, 20.01.2007 (Changed action attribute handling)<br />
       * Version 0.3, 27.07.2009 (Attibute "name" is not rendered into HTML tag, because of XHTML 1.1 strict)<br />
       */
      public function transformForm(){

         $t = &Singleton::getInstance('BenchmarkTimer');
         $id = '(html_taglib_form) '.$this->__ObjectID.'::transformForm()';
         $t->start($id);

         // add action attribute if not set
         $action = $this->getAttribute('action');
         if($action === null){
            // escape current request uri to avoid XSS attacks
            $this->setAttribute('action', htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES));
         }

         // transform the form including all child tags
         $htmlCode = (string)'<form ';
         $htmlCode .= $this->__getAttributesAsString($this->__Attributes,$this->attributeWhiteList);
         $htmlCode .= '>';

         if(count($this->__Children) > 0){
            
            foreach($this->__Children as $objectId => $DUMMY){
               $childId = '('.get_class($this->__Children[$objectId]).') '.$objectId.'::transform()';
               $t->start($childId);

               $this->__Content = str_replace('<'.$objectId.' />',
                       $this->__Children[$objectId]->transform(),$this->__Content);

               $t->stop($childId);
            }
         }

         $htmlCode .= $this->__Content;
         $htmlCode .= '</form>';

         $t->stop($id);
         return $htmlCode;

      }

      /**
       * @public
       *
       * Defines, whether the form should be transformed at the definition place.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 01.06.2008<br />
       */
      public function transformOnPlace(){
         $this->__TransformOnPlace = true;
      }

      /**
       * @public
       *
       * Re-implements the {@link transform} method for the form taglib.
       *
       * @return string The content of the form (in case of transformOnPlace) or an empty string.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 12.01.2007<br />
       * Version 0.2, 01.06.2008 (Added the transformOnPlace() feature)<br />
       */
      public function transform(){

         // to transformation on place if desired
         if($this->__TransformOnPlace === true){
            return $this->transformForm();
         }

         return (string)'';

      }

    // end class
   }
?>