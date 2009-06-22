<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::form::taglib','ui_element');
   import('tools::form::taglib','form_taglib_button');
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
   import('tools::form::taglib','form_taglib_validate');
   import('tools::form::taglib','form_taglib_valgroup');
   import('tools::form::taglib','form_taglib_genericval');
   import('tools::form::taglib','form_taglib_getstring');
   import('tools::form::taglib','form_taglib_addtaglib');
   import('tools::form::taglib','form_taglib_marker');
   import('tools::form::taglib','form_taglib_reset');

   /**
    * @package tools::form::taglib
    * @class html_taglib_form
    *
    * Represents a APF form element (DOM node).
    *
    * @author Christian Sch�fer
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
    */
   class html_taglib_form extends ui_element
   {

      /**
       * @protected
       * Indicates, whether the form was filled correctly (concerning the field validators).
       * @var boolean
       */
      protected $__isValid = true;


      /**
       * @protected
       * @since 0.7
       * Indicates, if the form was sent.
       * @var boolean
       */
      protected $__isSent = false;


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
       */
      function html_taglib_form(){

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','button');
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
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','valgroup');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','validate');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','genericval');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','getstring');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','marker');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','addtaglib');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','reset');

       // end function
      }


      /**
       * @public
       *
       * Parses the known taglibs.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 05.01.2007<br />
       */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  Adds a new form element at the end of the form. This method is intended to dynamically generate forms.
      *
      *  @param string $elementType Type of the element (e.g. "form:text")
      *  @param string[] $elementAttributes Associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
      *  @return string Id of the new form object or null (e.g. for addressing the new element)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 05.09.2008 (The new form element now gets the current context and language)<br />
      *  Version 0.3, 06.09.2008 (API change: now the tag name (e.g. "form:text") is expected as an argument)<br />
      *  Version 0.4, 10.09.2008 (Added the $ElementAttributes param)<br />
      */
      function addFormElement($elementType,$elementAttributes = array()){

         // create form element
         $ObjectID = $this->__createFormElement($elementType,$elementAttributes);

         // add form element if id is not null
         if($ObjectID !== null){

            // add position placeholder to the content
            $this->__Content .= '<'.$ObjectID.' />';

            // return object id of the new form element
            return $ObjectID;

          // end if
         }
         else{

            // notify user and return null
            trigger_error('[html_taglib_form::addFormElement()] Form element "'.$elementType.'" cannot be added due to previous errors!');
            return null;

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Adds content at the end of the form. This method is intended to dynamically generate forms.
      *
      *  @param string $content The desired content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function addFormContent($content){
         $this->__Content .= $content;
       // end function
      }


      /**
      *  @public
      *
      *  Adds content in front of a form marker. This method is intended to dynamically generate forms.
      *
      *  @param string $MarkerName the desired marker name
      *  @param string $Content the content to add
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function addFormContentBeforeMarker($MarkerName,$Content){

         // get desired marker
         $Marker = &$this->__getMarker($MarkerName);

         // check if marker exists
         if($Marker !== null){

            // get the object if
            $ObjectID = $Marker->get('ObjectID');

            // add the desired content before the marker
            $this->__Content = str_replace('<'.$ObjectID.' />',$Content.'<'.$ObjectID.' />',$this->__Content);

          // end if
         }
         else{

            // display an error
            trigger_error('[html_taglib_form::addFormContentBeforeMarker()] No marker object with name "'.$MarkerName.'" composed in current form for document controller "'.($this->__ParentObject->__DocumentController).'"! Please check the definition of the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Adds content behind a form marker. This method is intended to dynamically generate forms.
      *
      *  @param string $MarkerName the desired marker name
      *  @param string $Content the content to add
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.09.2008<br />
      */
      function addFormContentAfterMarker($MarkerName,$Content){

         // get desired marker
         $Marker = &$this->__getMarker($MarkerName);

         // check if marker exists
         if($Marker !== null){

            // get the object if
            $ObjectID = $Marker->get('ObjectID');

            // add the desired content before the marker
            $this->__Content = str_replace('<'.$ObjectID.' />','<'.$ObjectID.' />'.$Content,$this->__Content);

          // end if
         }
         else{

            // display an error
            trigger_error('[html_taglib_form::addFormContentAfterMarker()] No marker object with name "'.$MarkerName.'" composed in current form for document controller "'.($this->__ParentObject->__DocumentController).'"! Please check the definition of the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Adds a new form element in front of a form marker. This method is intended to dynamically generate forms.
      *
      *  @param string $MarkerName the desired marker name
      *  @param string $ElementType type of the element (e.g. "form:text")
      *  @param array $ElementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
      *  @return string $ObjectID id of the new form object or null (e.g. for addressing the new element)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.09.2008<br />
      *  Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
      */
      function addFormElementBeforeMarker($MarkerName,$ElementType,$ElementAttributes = array()){

         // create new form element
         $ObjectID = $this->__createFormElement($ElementType,$ElementAttributes);

         // add form element if id is not null
         if($ObjectID !== null){

            // get desired marker
            $Marker = &$this->__getMarker($MarkerName);

            // add the position placeholder to the content
            $MarkerID = $Marker->get('ObjectID');
            $this->__Content = str_replace('<'.$MarkerID.' />','<'.$ObjectID.' /><'.$MarkerID.' />',$this->__Content);

            // return object id of the new form element
            return $ObjectID;

          // end if
         }
         else{

            // notify user and return null
            trigger_error('[html_taglib_form::addFormElementBeforeMarker()] Form element "'.$ElementType.'" cannot be added due to previous errors!');
            return null;

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Adds a new form element after a form marker. This method is intended to dynamically generate forms.
      *
      *  @param string $MarkerName the desired marker name
      *  @param string $ElementType type of the element (e.g. "form:text")
      *  @param array $ElementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
      *  @return string $ObjectID id of the new form object or null (e.g. for addressing the new element)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.09.2008<br />
      *  Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
      */
      function addFormElementAfterMarker($MarkerName,$ElementType,$ElementAttributes = array()){

         // create new form element
         $ObjectID = $this->__createFormElement($ElementType,$ElementAttributes);

         // add form element if id is not null
         if($ObjectID !== null){

            // get desired marker
            $Marker = &$this->__getMarker($MarkerName);

            // add the position placeholder to the content
            $MarkerID = $Marker->get('ObjectID');
            $this->__Content = str_replace('<'.$MarkerID.' />','<'.$MarkerID.' /><'.$ObjectID.' />',$this->__Content);

            // return object id of the new form element
            return $ObjectID;

          // end if
         }
         else{

            // notify user and return null
            trigger_error('[html_taglib_form::addFormElementBeforeMarker()] Form element "'.$ElementType.'" cannot be added due to previous errors!');
            return null;

          // end else
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Adds a new form element to the child list.
      *
      *  @param string $ElementType type of the element (e.g. "form:text")
      *  @param array $ElementAttributes associative list of form element attributes (e.g. name, to enable the validation and presetting feature)
      *  @return string $ObjectID id of the new form object (e.g. for addressing the new element)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 06.09.2008<br />
      *  Version 0.2, 10.09.2008 (Added the $ElementAttributes param)<br />
      *  Version 0.3, 12.11.2008 (Bugfix: language and context initialisation were wrong)<br />
      */
      protected function __createFormElement($ElementType,$ElementAttributes = array()){

         // define taglib class
         $TagLibClass = str_replace(':','_taglib_',$ElementType);

         // check, if class exists
         if(class_exists($TagLibClass)){

            // generate object id
            $ObjectID = xmlParser::generateUniqID();

            // create new form element
            $FormObject = new $TagLibClass();

            // add standard and user defined attributes
            $FormObject->set('ObjectID',$ObjectID);
            $FormObject->set('Language',$this->__Language);
            $FormObject->set('Context',$this->__Context);

            foreach($ElementAttributes as $Key => $Value){
               $FormObject->setAttribute($Key,$Value);
             // end foreach
            }

            // add form element to DOM tree and call the onParseTime() method
            $FormObject->setByReference('ParentObject',$this);
            $FormObject->onParseTime();

            // add new form element to children list
            $this->__Children[$ObjectID] = $FormObject;

            // call the onAfterAppend() method
            $this->__Children[$ObjectID]->onAfterAppend();

            // return object id for further addressing
            return $ObjectID;

          // end if
         }
         else{

            // throw error and return null as object id
            trigger_error('[html_taglib_form::__createFormElement()] No form element with name "'.$ElementType.'" found! Maybe the tag name is misspellt or the class is not imported yet. Please use import() or &lt;form:addtaglib /&gt;!');
            return null;

          // end else
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Returns a reference on the desired marker or null.
      *
      *  @param string $MarkerName the desired marker name
      *  @return form_taglib_marker $Marker the marker or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      protected function &__getMarker($MarkerName){

         // check, weather the form has children
         if(count($this->__Children) > 0){

            // have a look at the children
            foreach($this->__Children as $ObjectID => $Child){

               // check, if current children is a marker
               if(get_class($Child) == 'form_taglib_marker'){

                  // check, if the name fits the method's argument
                  if($Child->getAttribute('name') == $MarkerName){
                     return $this->__Children[$ObjectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

            // return null, if no child was found (with a quick hack)
            $null = null;
            return $null;

          // end if
         }
         else{

            // return null (with a quick hack)
            $null = null;
            return $null;

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Convenience method to fill a place holder within the form.  
      *
      *  @param string $name The name of the place holder.
      *  @param string $value The value to fill the place holder with.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function setPlaceHolder($name,$value){

         $tagLibClass = 'form_taglib_placeholder';

         if(!class_exists($tagLibClass)){
            trigger_error('[html_taglib_form::setPlaceHolder()] TagLib module '.$tagLibClass.' is not loaded!',E_USER_ERROR);
          // end if
         }

         $placeHolderCount = 0;
         if(count($this->__Children) > 0){
            foreach($this->__Children as $ObjectID => $Child){
               if(get_class($Child) == $tagLibClass){
                  if($Child->getAttribute('name') == $name){
                     $this->__Children[$ObjectID]->set('Content',$value);
                     $placeHolderCount++;
                   // end if
                  }
                // end if
               }
             // end foreach
            }
          // end if
         }
         else{
            trigger_error('[html_taglib_form::setPlaceHolder()] No placeholder object with name "'.$name.'" composed in current for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library form:placeholder is not loaded in form "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();
          // end else
         }

         if($placeHolderCount < 1){
            trigger_error('[html_taglib_form::setPlaceHolder()] There are no placeholders found for name "'.$name.'" in template "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Sets the action url of the form.
      *
      *  @param string $action The action URL of the form.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function setAction($action){
         $this->__Attributes['action'] = $action;
       // end function
      }


      /**
      *  @public
      *
      *  Returns a reverence on the form element identified by the given name.
      *
      *  @param string $name The name of the desired form element.
      *  @return ui_element A reference on the form element.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function &getFormElementByName($name){

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){
               if($Child->getAttribute('name') == $name){
                  return $this->__Children[$ObjectID];
                // end if
               }
             // end foreach
            }

          // end if
         }

         // display extended debug message in case no form element was found
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementByName()] No form element with name "'.$name.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Returns a reverence on the form element identified by the given id.
      *
      *  @param string $ID The ID of the desired form element.
      *  @return ui_element A reference on the form element.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function &getFormElementByID($id){

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $DUMMY){
               if($Child->getAttribute('id') == $id){
                  return $this->__Children[$ObjectID];
                // end if
               }
             // end foreach
            }

          // end if
         }

         // display extended debug message in case no form element was found
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementByID()] No form element with id "'.$id.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Returns a reference on a form element addressed by it's internal object id.
      *
      *  @param string $objectID The object id of of the desired form element.
      *  @return ui_element A reference on the form element.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 12.01.2007 (Corrected error message)<br />
      *  Version 0.3, 06.09.2008 (Corrected error message again)<br />
      */
      function &getFormElementByObjectID($objectID){

         if(isset($this->__Children[$objectID])){
            return $this->__Children[$objectID];
          // end if
         }
         else{

            // note, that no suitable child has been found
            $Parent = $this->get('ParentObject');
            $DocumentController =  $Parent->get('DocumentController');
            trigger_error('[html_taglib_form::getFormElementByObjectID()] No form element with id "'.$objectID.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
            exit();

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Returns a list of form elements addressed by their tag name.
      *
      *  @param string $tagName The tag name of the desired form element (e.g. "form:text").
      *  @return ui_element[] A list of references on the form elements.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.06.2008 (API-Änderung. Statt getFormElementsByType() soll nur noch getFormElementsByTagName() verwendet werden, da intuitiver.)<br />
      */
      function &getFormElementsByTagName($tagName){

         $colon = strpos($tagName,':');
         $tagClassName = trim(substr($tagName,0,$colon)).'_taglib_'.trim(substr($tagName,$colon + 1));

         if(count($this->__Children) > 0){

            $FormElements = array();
            foreach($this->__Children as $objectID => $DUMMY){

               if(get_class($this->__Children[$objectID]) == $tagClassName){
                  $FormElements[] = &$this->__Children[$objectID];
                // end if
               }

             // end foreach
            }

            return $FormElements;

          // end if
         }

         // display extended debug message in case no form elements were found
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementsByType()] No form elements composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Returns the content of the transformed form.
      *
      *  @return string The content of the transformed form.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 20.01.2007 (Handling f�r Attribut "action" ge�ndert)<br />
      */
      function transformForm(){

         // add action attribute if not set
         if(!isset($this->__Attributes['action']) || empty($this->__Attributes['action'])){
            $this->__Attributes['action'] = $_SERVER['REQUEST_URI'];
          // end if
         }

         // transform
         $HTML_Form = (string)'';
         $HTML_Form .= '<form '.$this->__getAttributesAsString($this->__Attributes).'>';

         $Content = $this->__Content;

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){
               $Content = str_replace('<'.$ObjectID.' />',$Child->transform(),$Content);
             // end foreach
            }

          // end if
         }

         $HTML_Form .= $Content;
         $HTML_Form .= '</form>';
         return $HTML_Form;

       // end function
      }


      /**
      *  @public
      *
      *  Defines, whether the form should be transformed at the definition place.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function transformOnPlace(){
         $this->__TransformOnPlace = true;
       // end function
      }


      /**
      *  @public
      *
      *  Re-implements the {@link transform} method for the form taglib.
      *
      *  @return string The content of the form (in case of transformOnPlace) or an empty string.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 01.06.2008 (Added the transformOnPlace() feature)<br />
      */
      function transform(){

         // to transformation on place if desired
         if($this->__TransformOnPlace === true){
            return $this->transformForm();
          // end if
         }

         return (string)'';

       // end function
      }

    // end class
   }
?>