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
   import('tools::form::model','FileModel');
   

   /**
    * @package tools::form::taglib
    * @class form_taglib_file
    *
    * Represents the APF form file field.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    * Version 0.2, 12.02.2010 (Introduced attribute black and white listing)<br />
    */
   class form_taglib_file extends form_taglib_text {

      public function form_taglib_file(){
         $this->attributeWhiteList[] = 'name';
         $this->attributeWhiteList[] = 'accesskey';
         $this->attributeWhiteList[] = 'disabled';
         $this->attributeWhiteList[] = 'readonly';
         $this->attributeWhiteList[] = 'tabindex';
         $this->attributeWhiteList[] = 'accept';
      }

      /**
       * @public
       *
       * Executes the presetting and validation. Adds the "enctype" to the form,
       * so that the developer must not care about that!
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 13.01.2007<br />
       * Version 0.2, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
       */
      public function onAfterAppend(){
         $this->__ParentObject->setAttribute('enctype','multipart/form-data');
         $this->__presetValue();
       // end function
      }

      /**
       * @public
       *
       * Returns the HTML code of the file selection field.
       *
       * @return string The HTML code of the file field.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 13.01.2007<br />
       * Version 0.2, 11.02.2007 (Moved presetting and validation to onAfterAppend())<br />
       */
      public function transform(){
         return '<input type="file" '.$this->getSanitizedAttributesAsString($this->__Attributes).' />';
       // end function
      }

      /**
       * @public
       *
       * Indicates, whether a file has been uploaded (true) or not (false).
       *
       * @return boolean Returns TRUE if a file has been transferred, FALSE otherwise.
       *
       * @author Thalo
       * @version
       * Version 0.1, 12.01.2010<br />
       */
      public function hasUploadedFile(){
         $fieldName = $this->getAttribute('name');
         if(isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === 0) {
            return true;
         }
         return false;
      }

      /**
       * @public
       *
       * Returns the File Domain Object
       *
       * @return FileModel Returns a file domain object or NULL.
       *
       * @version
       * Version 0.1, 12.01.2010<br />
       */
      public function getFile() {
         if($this->hasUploadedFile()){
            return $this->__mapFileArray2DomainObject($_FILES[$this->getAttribute('name')]);
         }
         return null;
      }

      /**
       * @private
       *
       * Maps the File Array to the File Domain Object
       *
       * @param string[] The content of the <em>$_FILES</em> array for the current form control.
       * @return FileModel The uploaded file' representation.
       *
       * @author Thalo
       * @version
       * Version 0.1, 12.01.2010<br />
       */
      private function __mapFileArray2DomainObject($file){

         // use PHP5.3's finfo extension, if possible
         if(class_exists('finfo',false)) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            $finfo = finfo_open($const);
            $mime = finfo_file($finfo,$file['tmp_name']);
            finfo_close($finfo);
         }
         else {
            $mime = $file['type'];
         }

         $fileModel = new FileModel();
         $fileModel->setMimeType($mime);
         $fileModel->setName($file['name']);
         $fileModel->setSize(filesize($file['tmp_name']));
         $fileModel->setTemporaryName($file['tmp_name']);
         $fileModel->setExtension(substr(strrchr($file['name'],'.'),1));
         return $fileModel;
      }

    // end class
   }
?>