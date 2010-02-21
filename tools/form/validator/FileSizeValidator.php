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
    * @class FileSizeValidator
    *
    * Implements a simple validator, that checks the uploaded file
    * to have the desired file size. Min and max size can be defined.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.02.2010<br />
    */
   class FileSizeValidator extends TextFieldValidator {

      private static $MAX_ALLOWED_SIZE_ATTRIBUTE_NAME = 'maxsize';
      private static $MIN_REQUIRED_SIZE_ATTRIBUTE_NAME = 'minsize';

      /**
       * @public
       *
       * Validates the file control attached to.
       *
       * @param string $input The input of the file field (not relevant here).
       * @return boolean True in case the control is valid, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.02.2010<br />
       * Version 0.2, 21.02.2010 (Added min required file size)<br />
       */
      public function validate($input){

         // check, whether file was specified
         if($this->__Control->hasUploadedFile()){

            // retrieve file model to check the file size against the max size
            $fileModel = $this->__Control->getFile();

            $size = (int)$fileModel->getSize();
            $allowed = (int)$this->getMaxSize();
            $required = (int)$this->getMinSize();
            if($size >= $required && $size <= $allowed){
               return true;
            }
            
         }
         return false;
         
       // end function
      }

      /**
       * @private
       *
       * Returns the maximum allowed file size.
       *
       * @return int The maximum allowed file size.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 03.02.2010<br />
       */
      private function getMaxSize(){
         $maxSize = $this->__Control->getAttribute(self::$MAX_ALLOWED_SIZE_ATTRIBUTE_NAME);
         if(empty($maxSize)){
            return (int)1024000; // 1MB in bytes
         }
         return (int)$maxSize;
      }

      /**
       * @private
       *
       * Returns the minimum required file size.
       *
       * @return int The minimum required file size.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2010<br />
       */
      private function getMinSize(){
         $minSize = $this->__Control->getAttribute(self::$MIN_REQUIRED_SIZE_ATTRIBUTE_NAME);
         if(empty($minSize)){
            return (int)0; // 0 bytes allowed!
         }
         return (int)$minSize;
      }
      
    // end class
   }
?>