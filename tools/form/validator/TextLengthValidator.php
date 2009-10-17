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

   import('tools::form::validator','TextFieldValidator');

   /**
    * @namespace tools::form::validator
    * @class NumberValidator
    *
    * Validates a given form control to contain a string with a defined length.
    * The default value us three, to configure the length, please specify the
    * <em>minlength</em> attribute within the target form control definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   class TextLengthValidator extends TextFieldValidator {

      public function validate($input){
         if(!empty($input) && strlen($input) >= $this->__getMinLength()){
            return true;
         }
         return false;
       // end function
      }

      /**
       * @private
       *
       * Returns the min length of the min length of the text, that must
       * be contained within the target control. Tries to load the min
       * length from the <em>minlength</em> attribute within the target
       * form control definition.
       *
       * @return int The min length of the text.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      private function __getMinLength(){
         $minLength = $this->__Control->getAttribute('minlength');
         if($minLength === null){
            $minLength = 3;
         }

         // remove the "minlenght" attribute, so that it is not rendered on
         // transformation time of the control
         $this->__Control->deleteAttribute('minlength');

         return $minLength;
      }

    // end function
   }
?>