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
    * @package tools::form::taglib
    * @class form_control
    * @abstract
    *
    * Implements a base class for all APF form elements.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 05.01.2007<br />
    * Version 0.2, 02.06.2007 (Added the $__ExclusionArray, moved the __getAttributesAsString() to the coreObject class)<br />
    * Version 0.3, 07.12.2008 (Added the filter functionality, that let's you filter user input)<br />
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

      function form_control(){
      }

      /**
       * Initiate presetting of the form control. If you cannot use
       * value presetting, overwrite the protected method
       * <code>__presetValue()</code>.
       */
      public function onParseTime(){
         $this->__presetValue();
       // end function
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
       // end function
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
       // end function
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
       // end function
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
       // end function
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
       // end function
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
       // end function
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
       // end function
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
          // end if
         }
       // end function
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
       */
      public function addValidator(AbstractFormValidator &$validator){
         if($validator->isActive()){
            if(!$validator->validate($this->getAttribute('value'))){
               $validator->notify();
            }
         }
       // end function
      }

      /**
       * @public
       *
       * Extends the coreObject's attributes methods.
       *
       * @param string $name the name of the attribute
       * @param string $value the value to add to the attribute's value
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 09.01.2007<br />
       */
      function addAttribute($name,$value){

         if(isset($this->__Attributes[$name])){
            $this->__Attributes[$name] .= $value;
          // end if
         }
         else{
            $this->__Attributes[$name] = $value;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Notifies all validation listeners, who's control attribute is the same
       * as the name of the present control. This enables you to insert listener
       * tags, that output special content if notified. Hence, you can realize
       * special error formatting.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 30.08.2009<br />
       */
      public function notifyValidationListeners(){

         $listeners = &$this->__ParentObject->getFormElementsByTagName('form:listener');
         $count = count($listeners);
         $controlName = $this->getAttribute('name');

         for($i = 0; $i < $count; $i++){
            if($listeners[$i]->getAttribute('control') === $controlName){
               $listeners[$i]->notify();
            }
         }
         
       // end function
      }

      /**
       * @protected
       *
       * Prefills the value of the current control.
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
               trigger_error('['.get_class($this).'::__presetValue()] A form control is missing '
                  .' the required tag attribute "name". Please check the taglib definition of the '
                  .'form with name "'.$formName.'"!');
             // end if
            }

            if(isset($_REQUEST[$controlName]) && (!empty($_REQUEST[$controlName]) || $_REQUEST[$controlName] === '0')){
               $this->setAttribute('value',$_REQUEST[$controlName]);
             // end if
            }

          // end if
         }

       // end function
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
                     $this->__Children[$objectId]->set('Content',$value);
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
            trigger_error('['.get_class($this).'::setPlaceHolder()] No place holder object with '
               .'name "'.$name.'" composed in current for document controller "'
               .($this->__ParentObject->get('DocumentController')).'"! Perhaps tag library '
               .'form:placeholder is not loaded in form "'.$this->getAttribute('name').'"!',
               E_USER_ERROR);
            exit();
          // end else
         }

         if($placeHolderCount < 1){
            trigger_error('['.get_class($this).'::setPlaceHolder()] There are no place holders '
               .'found for name "'.$name.'" in template "'.($this->__Attributes['name'])
               .'" in document controller "'.($this->__ParentObject->get('DocumentController'))
               .'"!',E_USER_WARNING);
          // end if
         }

       // end function
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

       // end function
      }

    // end class
   }
?>