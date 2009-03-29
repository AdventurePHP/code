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

   import('tools::form::taglib','select_taglib_option');
   import('tools::form::taglib','form_taglib_select');


   /**
   *  @namespace tools::form::taglib
   *  @class form_taglib_multiselect
   *
   *  Represents the APF multiselect field.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 15.01.2007<br />
   *  Version 0.2, 07.06.2008 (Reimplemented the transform() method)<br />
   *  Version 0.3, 08.06.2008 (Reimplemented the __validate() method)<br />
   */
   class form_taglib_multiselect extends form_taglib_select
   {

      /**
      *  @public
      *
      *  Initializes the known child taglibs, sets the validator style and addes the multiple attribute.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 03.03.2007 (Removed the "&" before the "new" operator)<br />
      *  Version 0.3, 26.08.2007 (Added the "multiple" attribut)<br />
      */
      function form_taglib_multiselect(){
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','option');
         $this->__ValidatorStyle = 'background-color: red;';
         $this->__Attributes['multiple'] = 'multiple';
       // end function
      }


      /**
      *  @public
      *
      *  Parses the child tags and checks the name of the element to contain "[]".
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.01.2007<br />
      *  Version 0.2, 07.06.2008 (Extended error message)<br />
      *  Version 0.3, 15.08.2008 (Extended error message with the name of the control)<br />
      */
      function onParseTime(){

         // parses the option tags
         $this->__extractTagLibTags();

         // checks, of the name of the control contains "[]" at the end of the name definition
         if(!preg_match('/([A-Za-z0-9]+)\[\]$/',$this->__Attributes['name'])){

            $Form = & $this->__ParentObject;
            $Document = $Form->get('ParentObject');
            $DocumentController = $Document->get('DocumentController');
            trigger_error('[form_taglib_multiselect::onParseTime()] The attribute "name" of the &lt;form:multiselect /&gt; tag with name "'.$this->__Attributes['name'].'" in form "'.$Form->getAttribute('name').'" in document controller "'.$DocumentController.'" must not contain whitespace characters before or between "[" or "]"! Otherwise you maybe forgot the "[]"!',E_USER_ERROR);
            exit();

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Creates the HTML output of the select field
      *
      *  @return string $SelectField the HTML code of the select field
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.06.2008 (Reimplemented the transform() method because of a presetting error)<br />
      */
      function transform(){

         // remove value attribute created by the validation
         unset($this->__Attributes['value']);

         // concatinate the HTML code
         $select = (string)'';
         $select .= '<select '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).'>';

         $content = $this->__Content;

         if(count($this->__Children) > 0){

            // get the "name" attribute without the "[]". otherwise, presetting is not working!
            $TagName = str_replace('[]','',$this->getAttribute('name'));

            foreach($this->__Children as $ObjectID => $Child){

               // check, if $_REQUEST[$TagName] is an array and if the value of the select
               // field is included there
               if(isset($_REQUEST[$TagName]) && is_array($_REQUEST[$TagName])){
                  if(in_array($Child->getAttribute('value'),$_REQUEST[$TagName])){
                     $this->__Children[$ObjectID]->setAttribute('selected','selected');
                   // end if
                  }
                  else{
                     $this->__Children[$ObjectID]->deleteAttribute('selected');
                   // end else
                  }

                // end if
               }

               $content = str_replace('<'.$ObjectID.' />',$this->__Children[$ObjectID]->transform(),$content);

             // end foreach
            }

          // end if
         }

         $select .= $content;
         $select .= '</select>';
         return $select;

       // end function
      }


      /**
      *  @public
      *
      *  Returns the selected options.
      *
      *  @return select_taglib_option[] $options list of the options, that are selected
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008<br />
      */
      function &getSelectedOptions(){

         // call presetting lazy
         $this->__presetValue();

         // create list
         $Options = array();

         foreach($this->__Children as $ObjectID => $Child){

            if($this->__Children[$ObjectID]->getAttribute('selected') == 'selected'){
               $Options[] = &$this->__Children[$ObjectID];
             // end if
            }

          // end foreach
         }

         // return list
         return $Options;

       // end function
      }


      /**
      *  @protected
      *
      *  Reimplements the presetting method for the multiselect field.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.01.2007<br />
      *  Version 0.2, 16.01.2007 (Now checks, if the request param is set)<br />
      */
      protected function __presetValue(){

         // generate the offset of the request array from the name attribute
         $RequestOffset = trim(str_replace('[','',str_replace(']','',$this->__Attributes['name'])));

         // get the request value
         if(isset($_REQUEST[$RequestOffset])){
            $Values = $_REQUEST[$RequestOffset];
          // end if
         }
         else{
            $Values = array();
          // end else
         }

         // preselect options
         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if(in_array($this->__Children[$ObjectID]->getAttribute('value'),$Values)){
                  $this->__Children[$ObjectID]->setAttribute('selected','selected');
                // end if
               }

             // end foreach
            }

          // end if
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Reimplements the validation of the multiselect field.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008 (Reimplemented the __validate() method for the form_taglib_multiselect, because validation is different here)<br />
      */
      protected function __validate(){

         // check, if validation is enabled
         $this->__setValidateObject();

         if($this->__ValidateObject == true){

            // generate the offset of the request array from the name attribute
            $RequestOffset = trim(str_replace('[','',str_replace(']','',$this->__Attributes['name'])));

            // execute validation
            if(!isset($_REQUEST[$RequestOffset])){

               if(isset($this->__Attributes['style'])){
                  $this->__Attributes['style'] .= ' '.$this->__ValidatorStyle;
                // end if
               }
               else{
                  $this->__Attributes['style'] = $this->__ValidatorStyle;
                // end else
               }

               // mark form as invalid
               $this->__ParentObject->set('isValid',false);

             // end if
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>