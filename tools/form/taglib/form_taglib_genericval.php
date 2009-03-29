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

   import('tools::form::taglib','ui_validate');


   /**
   *  @namespace tools::form::taglib
   *  @class form_taglib_genericval
   *
   *  Generic validator tag, that displays it's content, if the checked control isn't valid.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 22.09.2007<br />
   *  Version 0.2, 15.08.2008 (Introduced new behavior of transform() method)<br />
   */
   class form_taglib_genericval extends ui_validate
   {

      /**
      *  @protected
      *  @since 0.2
      *  Stores, if the control to validate is valid (true = valid, false = invalid).
      */
      protected $__ControlIsValid = true;


      function form_taglib_genericval(){
      }


      /**
      *  @public
      *  @see http://forum.adventure-php-framework.org/de/viewtopic.php?p=186#p186
      *
      *  Implements coreObject's onAfterAppend() method. Validates a field and indicates, if the
      *  content of the tag should be displayed.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.09.2007<br />
      *  Version 0.2, 15.08.2008 (Made tag more generic. Added RegExp validator, added multi language support)<br />
      *  Version 0.3, 21.08.2008 (Fixed bug, that the form was not notified in case of invalid form control)<br />
      */
      function onAfterAppend(){

         // get the name of the fielt, that should be checked
         if(isset($this->__Attributes['field']) && !empty($this->__Attributes['field']) && isset($this->__Attributes['button']) && !empty($this->__Attributes['button'])){
            $Field = $this->__Attributes['field'];
            $Button = $this->__Attributes['button'];
          // end if
         }
         else{
            $Name = $this->__ParentObject->getAttribute('name');
            trigger_error('['.get_class($this).'::onAfterAppend()] Generic validator tag in form "'.$Name.'" has no or an empty button- or field- attributes!',E_USER_ERROR);
            $this->__Content = (string)'';
            $Field = null;
            $Button = null;
          // end else
         }

         // check if button is clicked
         if(isset($_REQUEST[$Button])){

            // Validierungs-Methode auslesen
            if(isset($this->__Attributes['validator']) && !empty($this->__Attributes['validator'])){
               $Validator = $this->__Attributes['validator'];
             // end if
            }
            else{
               $Validator = 'Text';
             // end else
            }

            // read string to validate from request
            if(isset($_REQUEST[trim($this->__Attributes['field'])])){
               $String = $_REQUEST[trim($this->__Attributes['field'])];
             // end if
            }
            else{
               $String = (string)'';
             // end else
            }

            // check, which kind of validation should be done
            if($Validator != 'RegExp'){

               // build validator method
               $ValidatorMethod = 'validate'.$Validator;

               if(myValidator::$ValidatorMethod($String) === false){
                  $this->__ControlIsValid = false;
                  $this->__ParentObject->set('isValid',false);
                // end if
               }

             // end if
            }
            else{

               // get reg exp from attribute "regexp"
               if(isset($this->__Attributes['regexp'])){

                  // get regexp
                  $RegExp = $this->__Attributes['regexp'];

                  // do regexp validation
                  if(myValidator::validateRegExp($String,$RegExp) === false){
                     $this->__ControlIsValid = false;
                     $this->__ParentObject->set('isValid',false);
                   // end if
                  }

                // end if
               }
               else{

                  // display error
                  $Name = $this->__ParentObject->getAttribute('name');
                  trigger_error('['.get_class($this).'::onAfterAppend()] Generic validator tag in form "'.$Name.'" has no regexp attribute for RegExp validation method! Control to validate is still considered valid!',E_USER_ERROR);

                // end else
               }

             // end else
            }

          // end if
         }

       // end function
      }


      /**
      *  @public
      *  @see http://forum.adventure-php-framework.org/de/viewtopic.php?p=186#p186
      *  @since 0.2
      *
      *  Implements the abstract transform() method. Returns the desired content, if the control to
      *  validate contains a invalid input.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.08.2008<br />
      */
      function transform(){

         if($this->__ControlIsValid == true){
            return (string)'';
          // end if
         }
         else{
            return $this->__Content;
          // end else
         }

       // end function
      }

    // end class
   }
?>