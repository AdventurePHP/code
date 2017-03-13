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

namespace APF\extensions\fileupload\biz;

class FileUploadHandler extends UploadHandler {

   /**
    * Randomizes a filename for better security of uploaded files
    *
    * @return string The randomized filename
    *
    * @author dave
    * @version
    * Version 0.1, 01.03.2017<br />
    */
   protected function trim_file_name($file_path, $name, $size, $type, $error, $index, $content_range) {
      $name = uniqid();
      return $name;
   }

   /**
    * Deletes the selected files from server uncluding previews or user-dirs.
    *
    * @param string $fileName The file to delete
    * @param bool $print_response True to print the response on screen
    *
    * @author dave
    * @version
    * Version 0.1, 01.03.2017<br />
    */
   public function deleteFile($fileName, $print_response) {
      $response = array();

      $file_path = $this->get_upload_path($fileName);
      $success = is_file($file_path) && $fileName[0] !== '.' && unlink($file_path);
      if ($success) {
         foreach ($this->options['image_versions'] as $version => $options) {
            if (!empty($version)) {
               $file = $this->get_upload_path($fileName, $version);
               if (is_file($file)) {
                  unlink($file);
               }
            }
         }
      }
      $response[$fileName] = $success;
      return $this->generate_response($response, $print_response);
   }
}