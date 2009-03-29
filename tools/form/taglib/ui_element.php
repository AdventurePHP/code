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

   import('tools::validator','myValidator');

   /**
   *  @namespace tools::form::taglib
   *  @class ui_element
   *  @abstract
   *
   *  Implements a base class for all APF form elements.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   *  Version 0.2, 02.06.2007 (Added the $__ExclusionArray, moved the __getAttributesAsString() to the coreObject class)<br />
   *  Version 0.3, 07.12.2008 (Added the filter functionality, that let's you filter user input)<br />
   */
   abstract class ui_element extends Document
   {

      /**
      *  @protected
      *  Defines the CSS style used to indicate invalid form elements.
      */
      protected $__ValidatorStyle = 'border: 2px solid red;';


      /**
      *  @protected
      *  Indicates, whether the form object should be validated (true) or not (false).
      */
      protected $__ValidateObject = false;


      /**
      *  @protected
      *  Indicated the validator type method.
      */
      protected $__Validator;


      /**
      *  @protected
      *  @since 0.2
      *  Exclusion array for transformation purposes.
      */
      protected $__ExclusionArray = array('validate','validator','button','filter','filterclass');


      /**
      *  @protected
      *  @since 1.3
      *  Indicates, if the current object should be filtered.
      */
      protected $__FilterObject = false;


      /**
      *  @protected
      *  @since 1.3
      *  Describes the delimiter between the namespace and class name of the filter.
      */
      protected $__FilterClassDelimiter = '|';


      /**
      *  @protected
      *  @since 1.3
      *  Contains the filter class' namespace.
      */
      protected $__FilterNamespace = 'tools::form::filter';


      /**
      *  @protected
      *  @since 1.3
      *  Contains the filter class' name.
      */
      protected $__FilterClass = 'FormFilter';


      /**
      *  @protected
      *  @since 1.3
      *  Contains the filter method name.
      */
      protected $__FilterMethod = null;


      function ui_element(){
      }


      /**
      *  @public
      *
      *  Extends the coreObject's attributes methods.
      *
      *  @param string $name the name of the attribute
      *  @param string $value the value to add to the attribute's value
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.01.2007<br />
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
      *  @protected
      *
      *  Prefills the value of the current control.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 08.08.2008 (Fixed bug, that the number "0" was not automatically prefilled)<br />
      */
      protected function __presetValue(){

         if(!isset($this->__Attributes['value']) || empty($this->__Attributes['value'])){

            if(
               isset($_REQUEST[$this->__Attributes['name']])
               &&
               (
               !empty($_REQUEST[$this->__Attributes['name']])
               ||
               $_REQUEST[$this->__Attributes['name']] === '0'
               )
               ){
               $this->__Attributes['value'] = $_REQUEST[$this->__Attributes['name']];
             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Validates the value of an form object. If the field is not valid, the field will be
      *  marked red, using additional css styles.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 13.01.2007 (Enhanced the error message, that was displayed if the "button" attribute is missing)<br />
      *  Version 0.3, 13.01.2007 (Refactored the method. Moved the check functionality to the __setValidateObject() function)<br />
      *  Version 0.4, 11.02.2007 (Bugfix: form is not makred as invalid, too, if a form element is invalid)<br />
      *  Version 0.5, 03.03.2007 (Removed bug within error message)<br />
      */
      protected function __validate(){

         // check, if object has to be validated
         $this->__setValidateObject();

         // execute validation
         if($this->__ValidateObject == true){

            // Attribut "value" setzen, falls nicht vorhanden
            if(!isset($this->__Attributes['value'])){
               $this->__Attributes['value'] = (string)'';
             // end if
            }

            // concat validator method
            $ValidatorMethode = 'validate'.$this->__Validator;

            if(in_array($ValidatorMethode,get_class_methods('myValidator'))){

               if(!myValidator::$ValidatorMethode($this->__Attributes['value']) || !isset($_REQUEST[$this->__Attributes['name']])){

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
            else{
               trigger_error('['.get_class($this).'::__validate()] Validation method "'.$ValidatorMethode.'" is not supported in class "myValidator"! Please consult the API documentation for further details!');
             // end else
            }

          // end if
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Checks, if the current form element should be validated.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 13.01.2007 (Activation is now done, only if the button is clicked)<br />
      */
      protected function __setValidateObject(){

         $this->__ValidateObject = false;

         // check for validator and button
         if(isset($this->__Attributes['validate']) && (trim($this->__Attributes['validate']) == 'true' || trim($this->__Attributes['validate']) == '1')){

            // set the validator
            if(!isset($this->__Attributes['validator'])){
               $this->__Validator = 'Text';
             // end if
            }
            else{
               $this->__Validator = $this->__Attributes['validator'];
             // end else
            }

            // check for button attribute
            if(!isset($this->__Attributes['button']) || empty($this->__Attributes['button'])){
               trigger_error('['.get_class($this).'::__setValidateObject()] Validation not possible for form object "'.get_class($this).'" with name "'.$this->__Attributes['name'].'"! Button is not specified!');
               $Button = (string)'';
             // end if
            }
            else{
               $Button = $this->__Attributes['button'];
             // end else
            }

            // activate validation, if button was pressed
            if(isset($_REQUEST[$Button])){
               $this->__ValidateObject = true;
             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Initializes the filter parameters of the APF form filters. To use the filter mechanism,
      *  this method must be called within the onAfterAppend() if the desired form taglib. Filtering
      *  is executed, if the "filter" attribute contains the name of the desired filter instruction.
      *  By default, the class "FormFilter" from the "tools::form::filter" namespace is used. If
      *  you like to use another filter class, please provide the "filterclass" attribute containing
      *  a filter class specification like "my::filter::class::namespace|MyFilterClassName". Sets the
      *  $this->__FilterMethod, $this->__FilterNamespace and $this->__FilterClass properties, that
      *  can be used by onw __filter() method implementations.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.12.2008<br />
      */
      protected function __initializeFilter(){

         // get filter
         $this->__FilterMethod = $this->getAttribute('filter');

         // apply filter
         if($this->__FilterMethod !== false && !empty($this->__FilterMethod)){

            // read the filter class attribute
            $filterClass = $this->getAttribute('filterclass');
            if($filterClass !== null){

               // check for the class-to-namespace delimiter. if it is not given, do not filter,
               // because a filter must be defined by it's namespace and(!) it's class.
               $classDelimiter = strpos($filterClass,$this->__FilterClassDelimiter);
               if($classDelimiter !== false){
                  $this->__FilterNamespace = trim(substr($filterClass,0,$classDelimiter));
                  $this->__FilterClass = trim(substr($filterClass,$classDelimiter + strlen($this->__FilterClassDelimiter)));
                  $this->__FilterObject = true;
                // end if
               }
               else{
                  trigger_error('['.get_class($this).'::__filter()] The "filterclass" attribute must contain a correct namespace and class description of the desired filter (e.g. "my::filter::namespace|FilterClassName"). Please check your tag definition or consult the documentation!',E_USER_WARNING);
                // end else
               }

             // end if
            }
            else{
               $this->__FilterClass = 'FormFilter';
               $this->__FilterNamespace = 'tools::form::filter';
               $this->__FilterObject = true;
             // end else
            }

          // end if
         }

       // end function
      }


      /**
      *  @protected
      *
      *  Implements the filter method, that is used to filter user input. Uses the
      *  $this->__FilterMethod, $this->__FilterNamespace and $this->__FilterClass properties.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.12.2008<br />
      *  Version 0.2, 13.12.2208 (Removed benchmarker)<br />
      */
      protected function __filter(){

         // initialize filter
         $this->__initializeFilter();

         // filter input
         if($this->__FilterObject === true){
            $filter = FilterFactory::getFilter(new FilterDefinition($this->__FilterNamespace,$this->__FilterClass));
            $this->setAttribute('value',$filter->filter($this->__FilterMethod,$this->getAttribute('value')));
          // end if
         }

       // end function
      }

    // end class
   }
?>