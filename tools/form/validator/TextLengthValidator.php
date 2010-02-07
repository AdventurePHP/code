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
    * @package tools::form::validator
    * @class TextLengthValidator
    *
    * Validates a given form control to contain a string with a defined length.
    * The default value is three, to configure the length, please specify the
    * <em>minlength</em> and <em>maxlength</em> attribute within the target form
    * control definition.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   class TextLengthValidator extends TextFieldValidator {

      private static $MIN_LENGTH_ATTRIBUTE_NAME = 'minlength';
      private static $MAX_LENGTH_ATTRIBUTE_NAME = 'maxlength';

      /**
       * @public
       *
       * Re-implements the <em>validate()</em> method for the text length validator.
       * Supports min and max length definition of the text to validate.
       *
       * @param string The text to validate.
       * @return boolean true, in case the control to validate is considered valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.02.2010<br />
       */
      public function validate($input){
         
         $minlength = $this->getMinLength();
         $maxlength = $this->getMaxLength();

         // the maxlength beeing null, the text may contain an infinite number of characters
         if($maxlength === 0){
            if(!empty($input) && strlen($input) >= $minlength){
               return true;
            }
         }
         else {
            if(!empty($input) && strlen($input) >= $minlength && strlen($input) <= $maxlength){
               return true;
            }
         }
         return false;
         
       // end function
      }

      /**
       * @private
       *
       * Returns the min length of the text, that must be contained within
       * the target control. Tries to load the min length from the
       * <em>minlength</em> attribute within the target form control definition.
       *
       * @return int The min length of the text.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      private function getMinLength(){
         $minLength = $this->__Control->getAttribute(self::$MIN_LENGTH_ATTRIBUTE_NAME);
         if($minLength === null){
            $minLength = 3;
         }

         // remove the "minlenght" attribute, so that it is not rendered on
         // transformation time of the control
         $this->__Control->deleteAttribute(self::$MIN_LENGTH_ATTRIBUTE_NAME);

         return (int)$minLength;
      }

      /**
       * @private
       *
       * Returns the max length of the text, that must be contained within
       * the target control. Tries to load the max length from the
       * <em>maxlength</em> attribute within the target form control definition.
       *
       * @return int The max length of the text.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.02.2010<br />
       */
      private function getMaxLength(){
         $maxLength = $this->__Control->getAttribute(self::$MAX_LENGTH_ATTRIBUTE_NAME);
         
         // remove the "maxlenght" attribute, so that it is not rendered on
         // transformation time of the control
         $this->__Control->deleteAttribute(self::$MAX_LENGTH_ATTRIBUTE_NAME);

         // max length beeing null is ok, because we consider it to indicate infinite length!
         return (int)$maxLength;
      }

    // end function
   }
?>