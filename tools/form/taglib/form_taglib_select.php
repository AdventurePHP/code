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

   import('tools::form::taglib','select_taglib_option');

   /**
    * @namespace tools::form::taglib
    * @class form_taglib_select
    *
    * Represents an APF select field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.01.2007 (Renamed to "form_taglib_select")<br />
    */
   class form_taglib_select extends form_control {

      /**
       * @public
       *
       * Initializes the child taglibs.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 03.03.2007 (Removed "&" before "new")<br />
       */
      function form_taglib_select(){
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','option');
       // end function
      }

      /**
       * @public
       *
       * Parses the options and initializes the select field.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       */
      function onParseTime(){
         $this->__extractTagLibTags();
         $this->__presetValue();
       // end function
      }

      /**
       * @public
       *
       * Selects an option by a given name or value.
       *
       * @param string $displayNameOrValue The option's name or value to set to selected.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 18.11.2007 (Fehlermeldung korrigiert und Möglichkeit eingeräumt per Value oder DisplayName selected zu setzen)<br />
       */
      function setOption2Selected($displayNameOrValue){

         $optionCount = 0;

         foreach($this->__Children as $objectId => $Child){

            if(trim($this->__Children[$objectId]->get('Content')) == $displayNameOrValue
               || $Child->getAttribute('value') == $displayNameOrValue){
               $this->__Children[$objectId]->setAttribute('selected','selected');
               $optionCount++;
             // end if
            }

          // end foreach
         }

         if($optionCount < 1){
            trigger_error('[form_taglib_select::setOption2Selected()] No option with name or value "'
               .$displayNameOrValue.'" found in select field "'.$this->__Attributes['name']
               .'" in form "'.$this->__ParentObject->getAttribute('name').'"!');
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Adds an option to the select field
       *
       * @param string $displayName The display text of the option.
       * @param string $value The option's value.
       * @param boolean $preSelected True in case, the option should be selected, false otherwise.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 07.06.2008 (objectId is not set to the added option)<br />
       */
      function addOption($displayName,$value,$preSelected = false){

         $objectId = xmlParser::generateUniqID();
         $this->__Children[$objectId] = new select_taglib_option();

         $this->__Children[$objectId]->set('ObjectID',$objectId);
         $this->__Children[$objectId]->set('Content',$displayName);
         $this->__Children[$objectId]->setAttribute('value',$value);

         if($preSelected == true){
            $this->__Children[$objectId]->setAttribute('selected','selected');
          // end if
         }
         $this->__Children[$objectId]->set('Language',$this->__Language);
         $this->__Children[$objectId]->set('Context',$this->__Context);
         $this->__Children[$objectId]->onParseTime();

         // inject parent object (=this) to guarantee native DOM tree environment
         $this->__Children[$objectId]->setByReference('ParentObject',$this);
         $this->__Children[$objectId]->onAfterAppend();

         // add xml marker, necessary for transformation
         $this->__Content .= '<'.$objectId.' />';

       // end function
      }

      /**
       * @public
       *
       * Generates the HTML code of the select field.
       *
       * @return string The HTML code of the select field.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 12.01.2007 (Removed typos)<br />
       * Version 0.3, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
       */
      function transform(){

         $select = (string)'';
         $select .= '<select '.$this->__getAttributesAsString($this->__Attributes).'>';

         if(count($this->__Children) > 0){

            foreach($this->__Children as $objectId => $DUMMY){

               if(isset($_REQUEST[$this->getAttribute('name')]) && !empty($_REQUEST[$this->getAttribute('name')])){

                  if($this->__Children[$objectId]->getAttribute('value') == $_REQUEST[$this->getAttribute('name')]){
                     $this->__Children[$objectId]->setAttribute('selected','selected');
                   // end if
                  }
                  else{
                     $this->__Children[$objectId]->deleteAttribute('selected');
                   // end else
                  }

                // end if
               }

               $this->__Content = str_replace('<'.$objectId.' />',$this->__Children[$objectId]->transform(),$this->__Content);

             // end foreach
            }

          // end if
         }

         $select .= $this->__Content;
         $select .= '</select>';
         return $select;

       // end function
      }

      /**
       * @public
       *
       * Returns the selected option.
       *
       * @return select_taglib_option The selected option taglib instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.06.2008<br />
       */
      function &getSelectedOption(){

         // execute presetting lazily for dynamic forms
         //$this->__presetValue();

         $selectedOption = null;

         foreach($this->__Children as $objectId => $DUMMY){
            if($this->__Children[$objectId]->getAttribute('selected') == 'selected'){
               $selectedOption = &$this->__Children[$objectId];
             // end if
            }
          // end foreach
         }

         return $selectedOption;

       // end function
      }

      /**
       * @public
       * @since 1.11
       *
       * Re-implements the addValidator() method for select fields.
       *
       * @param AbstractFormValidator $validator The desired validator.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function addValidator(AbstractFormValidator &$validator){

         if($validator->isActive()){
            $option = &$this->getSelectedOption();
            if($option === null){
               $value = null;
            }
            else {
               $value = $option->getAttribute('value');
            }

            if(!$validator->validate($value)){
               $validator->notifyElement();
            }
            
          // end if
         }
         
       // end function
      }

      /**
       * @protected
       *
       * Re-implements the method from the base class to specialize the
       * presetting for the select field.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.06.2008 (Method is now overridden due to validation error!)<br />
       * Version 0.2, 29.08.2009 (Introduced presetting of sub-elements)<br />
       */
      protected function __presetValue(){
         //echo '<br />__presetValue()';
         $value = $this->__getRequestValue();

         if(count($this->__Children) > 0){

            foreach($this->__Children as $objectId => $DUMMY){

               // preselect options with the corresponding values or delete attribute
               if($this->__Children[$objectId]->getAttribute('value') == $value){
                  $this->__Children[$objectId]->setAttribute('selected','selected');
                // end if
               }

             // end foreach
            }

          // end if
         }

       // end function
      }

      /**
       * @protected
       *
       * Returns the value of the present form control from the request.
       * Enables sub-elements of form controls (date control!).
       *
       * @return string The form control's value in request.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      protected function __getRequestValue(){

         $name = $this->getAttribute('name');
         $value = (string)'NOVALUEINREQUEST';

         $subMarkerStart = '[';
         $subMarkerEnd = ']';

         // analyze sub-elements by the start marker bracket
         if(substr_count($name,$subMarkerStart) > 0){
            $startBracketPos = strpos($name,$subMarkerStart);
            $endBracketPos = strpos($name,$subMarkerEnd);
            $mainName = substr($name,0,$startBracketPos);
            $subName = substr($name,$startBracketPos + 1,
               $endBracketPos - $startBracketPos - strlen($subMarkerEnd)
            );
            if(isset($_REQUEST[$mainName][$subName])){
               $value = $_REQUEST[$mainName][$subName];
            }
          // end if
         }
         else {
            if(isset($_REQUEST[$name])){
               $value = $_REQUEST[$name];
            }
          // end else
         }

         return $value;
         
       // end function
      }

    // end class
   }
?>