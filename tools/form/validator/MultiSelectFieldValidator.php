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

   import('tools::form::validator','SelectFieldValidator');

   /**
    * Implements a base class for all text field validators.
    */
   class MultiSelectFieldValidator extends SelectFieldValidator {

      public function validate($input){

         if(empty($input)){
            return false;
         }
         return true;

       // end function
      }

      /**
       * @protected
       *
       * Reimplements the validation of the multiselect field.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.06.2008 (Reimplemented the __validate() method for the form_taglib_multiselect, because validation is different here)<br />
       */
      protected function __validate(){ // not needed any more?

         // check, if validation is enabled
         $this->__setValidateObject();

         if($this->__ValidateObject == true){

            // generate the offset of the request array from the name attribute
            $requestOffset = trim(str_replace('[','',str_replace(']','',$this->__Attributes['name'])));

            // execute validation
            if(!isset($_REQUEST[$requestOffset])){

               if(isset($this->__Attributes['style'])){
                  $this->__Attributes['style'] .= ' '.$this->__ValidatorStyle;
                // end if
               }
               else{
                  $this->__Attributes['style'] = $this->__ValidatorStyle;
                // end else
               }

               // mark form as invalid
               //$this->__ParentObject->set('isValid',false);

             // end if
            }

          // end if
         }

       // end function
      }

    // end function
   }
?>