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
   import('tools::form::taglib','select_taglib_group');

   /**
    * @package tools::form::taglib
    * @class form_taglib_select
    *
    * Represents an APF select field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 07.01.2007<br />
    * Version 0.2, 12.01.2007 (Renamed to "form_taglib_select")<br />
    * Version 0.3, 15.02.2010 (Added option groups)<br />
    */
   class form_taglib_select extends form_control {

      /**
       * @protected
       * @var boolean Marks the field as dynamic to do special presetting on transformation time. 
       */
      protected $isDynamicField = false;

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
      public function form_taglib_select(){
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','option');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','group');
         $this->attributeWhiteList[] = 'disabled';
         $this->attributeWhiteList[] = 'name';
         $this->attributeWhiteList[] = 'size';
         $this->attributeWhiteList[] = 'tabindex';
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
      public function onParseTime(){
         $this->__extractTagLibTags();
         $this->setOption2Selected($this->getRequestValue());
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
       * Version 0.2, 07.06.2008 (objectId is now set to the added option)<br />
       */
      public function addOption($displayName,$value,$preSelected = false){

         // mark as dynamic field
         $this->isDynamicField = true;

         $objectId = XmlParser::generateUniqID();
         $this->__Children[$objectId] = new select_taglib_option();

         $this->__Children[$objectId]->setObjectId($objectId);
         $this->__Children[$objectId]->setContent($displayName);
         $this->__Children[$objectId]->setAttribute('value',$value);

         if($preSelected == true){
            $this->__Children[$objectId]->setAttribute('selected','selected');
         }
         $this->__Children[$objectId]->setLanguage($this->__Language);
         $this->__Children[$objectId]->setContext($this->__Context);
         $this->__Children[$objectId]->onParseTime();

         // inject parent object (=this) to guarantee native DOM tree environment
         $this->__Children[$objectId]->setParentObject($this);
         $this->__Children[$objectId]->onAfterAppend();

         // add xml marker, necessary for transformation
         $this->__Content .= '<'.$objectId.' />';

       // end function
      }

      /**
       * @public
       *
       * Adds an option to a group specified by the applied label.
       * 
       * @param string $groupLabel The name of the group's label.
       * @param string $displayName The display text of the option.
       * @param string $value The option's value.
       * @param boolean $preSelected True in case, the option should be selected, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.02.2010<<br />
       */
      public function addGroupOption($groupLabel,$displayName,$value,$preSelected = false){

         // mark as dynamic field
         $this->isDynamicField = true;

         // retrieve or lazily create group
         $group = &$this->getGroup($groupLabel);
         if($group === null){

            $objectId = XmlParser::generateUniqID();
            $this->__Children[$objectId] = new select_taglib_group();
            $this->__Children[$objectId]->setObjectId($objectId);
            $this->__Children[$objectId]->setAttribute('label',$groupLabel);

            $this->__Children[$objectId]->setLanguage($this->__Language);
            $this->__Children[$objectId]->setContext($this->__Context);
            $this->__Children[$objectId]->onParseTime();

            // inject parent object (=this) to guarantee native DOM tree environment
            $this->__Children[$objectId]->setParentObject($this);
            $this->__Children[$objectId]->onAfterAppend();

            // add xml marker, necessary for transformation
            $this->__Content .= '<'.$objectId.' />';

            // make group available for the subsequent call
            $group = &$this->__Children[$objectId];

          // end if
         }

         // add option to group
         $group->addOption($displayName,$value,$preSelected);

       // end function
      }

      /**
       * @public
       *
       * Returns the desired group by a given group label.
       *
       * @param string $label The label of the group to return.
       * @return select_taglib_group The desired group or null.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.02.2010<<br />
       */
      public function &getGroup($label){

         $group = null;

         foreach($this->__Children as $objectId => $DUMMY){
            if($this->__Children[$objectId]->getAttribute('label') == $label){
               $group = &$this->__Children[$objectId];
               break;
            }
         }

         return $group;

       // end function
      }

      /**
       * @public
       *
       * Returns the selected option.
       *
       * @return select_taglib_option The selected option.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.02.2010<br />
       */
      public function &getSelectedOption(){

         // lazily do request presetting when not already done
         if($this->isDynamicField === true){
            $this->setOption2Selected($this->getRequestValue());
         }

         $selectedOption = null;
         foreach($this->__Children as $objectId => $DUMMY){

            if(get_class($this->__Children[$objectId]) == 'select_taglib_group'){
               $selectedOption = &$this->__Children[$objectId]->getSelectedOption();
            }
            else{
               if($this->__Children[$objectId]->getAttribute('selected') === 'selected'){
                  $selectedOption = &$this->__Children[$objectId];
                  break;
               }
            }

          // end foreach
         }

         return $selectedOption;
         
       // end function
      }

      /**
       * @public
       *
       * Pre-selects an option by a given display name or value.
       *
       * @param string $displayNameOrValue The display name or the value of the option to pre-select.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 15.02.2010<br />
       * Version 0.2, 17.06.2010 (Bugfix: introduced unsetting for previously selected options)<br />
       */
      public function setOption2Selected($displayNameOrValue){

         $this->isDynamicField = false;

         $selectedObjectId = null;
         foreach($this->__Children as $objectId => $DUMMY){

            // treat groups as a special case, because a group has more options in it!
            if(get_class($this->__Children[$objectId]) == 'select_taglib_group'){
               $this->__Children[$objectId]->setOption2Selected($displayNameOrValue);
            }
            else{
               if($this->__Children[$objectId]->getAttribute('value') == $displayNameOrValue
                       || $this->__Children[$objectId]->getContent() == $displayNameOrValue){
                  $this->__Children[$objectId]->setAttribute('selected','selected');
                  $selectedObjectId = $objectId;
               }
            }

          // end foreach
         }

         // unselect all other option to do not have interference with the currently selected option!
         // this is only necessary within the simple select field - not multi select.
         if(get_class($this) == 'form_taglib_select' && $selectedObjectId !== null){
            foreach($this->__Children as $objectId => $DUMMY){
               if($objectId != $selectedObjectId){
                  $this->__Children[$objectId]->deleteAttribute('selected');
               }
            }
         }
         
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
      public function transform(){

         // do lazy presetting, in case we are having a field with dynamic options
         if($this->isDynamicField === true){
            $value = $this->getRequestValue();
            $this->setOption2Selected($this->getRequestValue());
         }

         // create html code
         $select = (string)'';
         $select .= '<select '.$this->__getAttributesAsString($this->__Attributes).'>';

         foreach($this->__Children as $objectId => $DUMMY){
            $this->__Content = str_replace('<'.$objectId.' />',
                    $this->__Children[$objectId]->transform(),
                    $this->__Content
            );
         }

         return $select.$this->__Content.'</select>';

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
               $validator->notify();
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
      protected function getRequestValue(){

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