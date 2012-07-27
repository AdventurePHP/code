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
 * @package tools::form::model
 * @class FileModel
 *
 * Represents a file object created during http file upload.
 *
 * @author Thalo
 * @version
 * Version 0.1, 14.01.2010<br />
 */
final class FileModel {

   /**
    * @var string The name of the file.
    */
   private $name;

   /**
    * @var string The size of the file in <em>bytes</em>.
    */
   private $size;

   /**
    * @var string The extension of the file.
    */
   private $extension;

   /**
    * @var string The MIME type - if applicable.
    */
   private $mimeType;

   /**
    * @var string The name of the uploaded temporary file.
    */
   private $temporaryName;

   public function getName() {
      return $this->name;
   }

   public function setName($name) {
      $this->name = $name;
   }

   public function getSize() {
      return $this->size;
   }

   public function setSize($size) {
      $this->size = (int)$size;
   }

   public function getExtension() {
      return $this->extension;
   }

   public function setExtension($extension) {
      return $this->extension = $extension;
   }

   public function getMimeType() {
      return $this->mimeType;
   }

   public function setMimeType($mimeType) {
      $this->mimeType = $mimeType;
   }

   public function getTemporaryName() {
      return $this->temporaryName;
   }

   public function setTemporaryName($temporaryName) {
      $this->temporaryName = $temporaryName;
   }

}
