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

   import('tools::form','FormException');

   /**
    * @package tools::form::taglib
    * @class form_control
    * @abstract
    *
    * Implements a base class for all APF form elements.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 02.06.2007 (Added the $__ExclusionArray, moved the __getAttributesAsString() to the APFObject class)<br />
    * Version 0.3, 07.12.2008 (Added the filter functionality, that let's you filter user input)<br />
    * Version 0.4, 07.07.2010 (Added event attributes defined in xhtml 1.0 strict)<br />
    * Version 0.5, 21.07.2010 (Added function for adding attributes to the controls whitelist)<br />
    */
   abstract class form_control extends Document {

      /**
       * @protected
       * @since 1.11
       * @var boolean Indicates, whether the form control is valid or not.
       */
      protected $__ControlIsValid = true;

      /**
       * @protected
       * @since 1.11
       * @var boolean Indicates, whether the form is sent or not.
       */
      protected $__ControlIsSent = false;

      /**
       * @protected
       * @since 1.12
       * @var string[] The attributes, that are allowed to render into the XHTML/1.1 strict document.
       */
      protected $attributeWhiteList = array('id','style','class','onclick','ondblclick','onmousedown','onmouseup','onmouseover','onmousemove','onmouseout','onkeypress','onkeydown','onkeyup');

      protected static $METHOD_ATTRIBUTE_NAME = 'method';
      protected static $METHOD_POST_VALUE_NAME = 'post';

      /**
       * @public
       *
       * Initiate presetting of the form control. If you cannot use
       * value presetting, overwrite the protected method
       * <code>__presetValue()</code>.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.08.2009<br />
       */
      public function onParseTime(){
         $this->__presetValue();
      }

      /**
       * @public
       *
       * Returns true in case the form is valid and false otherwise.
       *
       * @return boolean The validity status.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.08.2009<br />
       */
      public function isValid(){
         return $this->__ControlIsValid;
      }

      /**
       * @public
       *
       * Allows you to mark this form control as invalid.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 25.08.2009<br />
       */
      public function markAsInvalid(){
         $this->__ControlIsValid = false;
      }

      /**
       * @public
       *
       * Marks a form as sent.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function markAsSent(){
         $this->__ControlIsSent = true;
      }

      /**
       * @public
       *
       * Returns the sending status of the form.
       *
       * @return boolean True in case the form was sent, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function isSent(){
         return $this->__ControlIsSent;
      }

      /**
       * @public
       *
       * Let's you check, if a radio button was checked.
       *
       * @return boolean True in case the radio button is checked, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function isChecked(){
         if($this->getAttribute('checked') == 'checked'){
            return true;
         }
         return false;
      }

      /**
       * @public
       *
       * Method for checking the checkbox.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function check(){
         $this->setAttribute('checked','checked');
      }

      /**
       * @public
       *
       * Method for unchecking the checkbox.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function uncheck(){
         $this->deleteAttribute('checked');
      }

      /**
       * @public
       *
       * Disables a form control for usage.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function disable(){
         $this->setAttribute('disabled','disabled');
      }

      /**
       * @public
       *
       * Enables a form control for user access.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function enable(){
         $this->deleteAttribute('disabled');
      }

      /**
       * @public
       *
       * Let's you query the user access status.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @return True in case the control is read only, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function isDisabled(){
         if($this->getAttribute('disabled') == 'disabled'){
            return true;
         }
         return false;
      }
      
      /**
       * @public
       *
       * Sets a form control to read only.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function setReadOnly(){
         $this->setAttribute('readonly','readonly');
      }

      /**
       * @public
       *
       * Enables a form control for write access.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function setReadWrite(){
         $this->deleteAttribute('readonly');
      }

      /**
       * @public
       *
       * Let's you query the read only status.
       *
       * @see http://www.w3.org/TR/html401/interact/forms.html#h-17.12
       *
       * @return True in case the control is read only, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 13.02.2010<br />
       */
      public function isReadOnly(){
         if($this->getAttribute('readonly') == 'readonly'){
            return true;
         }
         return false;
      }
      
      /**
       * @public
       * @since 1.11
       *
       * Applies the given filter to the present input element.
       *
       * @param AbstractFormFilter $filter The desired filter.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.08.2009<br />
       */
      public function addFilter(AbstractFormFilter &$filter){
         if($filter->isActive()){
            $value = $this->getAttribute('value');
            $filteredValue = $filter->filter($value);
            $this->setAttribute('value',$filteredValue);
         }
      }

      /**
       * @public
       * @since 1.11
       *
       * Executes the given form validator in context of the current form element.
       *
       * @param AbstractFormValidator $validator The desired validator.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.08.2009<br />
       * Version 0.2, 01.11.2010 (Added support for optional validators)<br />
       */
      public function addValidator(AbstractFormValidator &$validator){
         $value = $this->getAttribute('value');
         if ($validator->isActive() && $this->isMandatory($value)) {
            if (!$validator->validate($value)) {
               $validator->notify();
            }
         }
      }

      /**
       * @protected
       *
       * Indicates, whether validation is mandatory or not. This enables to introduce
       * optional validators that are only active in case a field is filled.
       *
       * @author Christian Achatz, Ralf Schubert
       * @version
       * Version 0.1, 01.11.2010<br />
       */
      protected function isMandatory($value) {
         if($this->getAttribute('optional', 'false') === 'true'){
            return !empty($value);
         }
         return true;
      }

      /**
       * @public
       *
       * Extends the APFObject's attributes methods.
       *
       * @param string $name the name of the attribute
       * @param string $value the value to add to the attribute's value
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 09.01.2007<br />
       */
      public function addAttribute($name,$value){

         if(isset($this->__Attributes[$name])){
            $this->__Attributes[$name] .= $value;
         }
         else{
            $this->__Attributes[$name] = $value;
         }

      }

      /**
       * @protected
       *
       * Prefills the value of the current control.
       *
       * @throws FormException In case the form control has no name.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 07.01.2007<br />
       * Version 0.2, 08.08.2008 (Fixed bug, that the number "0" was not automatically prefilled)<br />
       */
      protected function __presetValue(){

         if(!isset($this->__Attributes['value']) || empty($this->__Attributes['value'])){
            $controlName = $this->getAttribute('name');
            if($controlName === null){
               $formName = $this->__ParentObject->getAttribute('name');
               throw new FormException('['.get_class($this).'::__presetValue()] A form control is missing '
                  .' the required tag attribute "name". Please check the taglib definition of the '
                  .'form with name "'.$formName.'"!',E_USER_ERROR);
            }

            if(isset($_REQUEST[$controlName]) && (!empty($_REQUEST[$controlName]) || $_REQUEST[$controlName] === '0')){
               $this->setAttribute('value',$_REQUEST[$controlName]);
            }

         }

      }

      /**
       * @public
       *
       * Convenience method to fill a place holder within a form control. Currently
       * only works with &lt;form:error /&gt;, &lt;form:listener /&gt; and
       * &lt;html:form /&gt; tags.
       *
       * @param string $name The name of the place holder.
       * @param string $value The value to fill the place holder with.
       * @throws FormException In case the place holder cannot be set.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      public function setPlaceHolder($name,$value){

         // dynamically gather taglib name of the place holder to set
         $tagLibClass = $this->__getClassNameByTagLibClass('placeholder');

         $placeHolderCount = 0;
         if(count($this->__Children) > 0){
            foreach($this->__Children as $objectId => $DUMMY){
               if(get_class($this->__Children[$objectId]) == $tagLibClass){
                  if($this->__Children[$objectId]->getAttribute('name') == $name){
                     $this->__Children[$objectId]->setContent($value);
                     $placeHolderCount++;
                  }
               }
            }
         }
         else{
            throw new FormException('['.get_class($this).'::setPlaceHolder()] No place holder object with '
               .'name "'.$name.'" composed in current for document controller "'
               .($this->__ParentObject->getDocumentController()).'"! Perhaps tag library '
               .'form:placeholder is not loaded in form "'.$this->getAttribute('name').'"!',
               E_USER_ERROR);
         }

         if($placeHolderCount < 1){
            throw new FormException('['.get_class($this).'::setPlaceHolder()] There are no place holders '
               .'found for name "'.$name.'" in template "'.($this->__Attributes['name'])
               .'" in document controller "'.($this->__ParentObject->getDocumentController())
               .'"!',E_USER_WARNING);
         }

      }

      /**
       * @protected
       *
       * Returns the name of the taglib class (PHP class name), that is defined
       * with the given taglib class name. Passing the taglib class name "placeholder"
       * would return "form_taglib_placeholder" within the &lt;html:form /&gt; taglib.
       *
       * @param string $class The taglib class name.
       * @return string The PHP class name, that represents the taglib with the
       *                given taglib class name.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.09.2009<br />
       */
      protected function __getClassNameByTagLibClass($class){
         
         foreach($this->__TagLibs as $tagLib){
            if($tagLib->getClass() == $class){
               return $tagLib->getPrefix().'_taglib_'.$class;
            }
         }

         return null;

      }

      /**
       * @protected
       *
       * Converts an attributes array into a xml string including the black list
       * and white list definition within the taglib instance.
       *
       * @param string[] $attributes The attributes to convert to string.
       * @return string The attributes' xml string representation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 12.02.2010<br />
       */
      protected function getSanitizedAttributesAsString($attributes){
         return $this->__getAttributesAsString($attributes,$this->attributeWhiteList);
      }

      /**
       * @public
       * @since 1.13
       *
       * Adds an additional attribute to the whitelist of the control.
       *
       * @param string $name The attribute which should be added to the whitelist.
       *
       * @author Ralf Schubert
       * @version
       * Version 0.1, 21.07.2010<br />
       */
      public function addAttributeToWhitelist($name){
          $this->attributeWhiteList[] = $name;
      }
      
    // end class
   }
?>