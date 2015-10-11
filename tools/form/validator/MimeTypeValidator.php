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
namespace APF\tools\form\validator;

use APF\tools\form\taglib\FileUploadTag;

/**
 * Implements a simple validator, that checks the uploaded file
 * to have the desired MIME type.
 *
 * @author Thalo
 * @version
 * Version 0.1, 12.01.2010<br />
 */
class MimeTypeValidator extends TextFieldValidator {

   private static $ACCEPTS_ATTRIBUTE_NAME = 'accepts';

   /**
    * Validates the file control attached to.
    *
    * @param string $input The input of the file field (not relevant here).
    *
    * @return boolean True in case the control is valid, false otherwise.
    *
    * @author Thalo, Christian Achatz
    * @version
    * Version 0.1, 02.02.2010<br />
    */
   public function validate($input) {

      // check, whether file was specified
      /* @var $control FileUploadTag */
      $control = $this->control;
      if ($control->hasUploadedFile()) {

         // retrieve file model to check the MIME type against the accepted types
         $fileModel = $control->getFile();

         // store values in variables to not break the in_[] functionality
         $acceptedTypes = $this->getAcceptedMIMETypes();
         $mimeType = $fileModel->getMimeType();
         if (in_array($mimeType, $acceptedTypes, true)) {
            return true;
         }

      }

      return false;

   }

   /**
    * Returns the list of accepted MIME types.
    *
    * @return string[] The list of accepted MIME types.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2010<br />
    */
   private function getAcceptedMIMETypes() {
      $accepts = $this->control->getAttribute(self::$ACCEPTS_ATTRIBUTE_NAME);
      if (empty($accepts)) {
         return [];
      }

      return explode('|', $accepts);
   }

}
