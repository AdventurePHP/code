<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\tools\form\model;

/**
 * Represents a file object created during http file upload.
 *
 * @author Thalo
 * @version
 * Version 0.1, 14.01.2010<br />
 */
final class FileModel {

   /**
    * The name of the file.
    *
    * @var string $name
    */
   private $name;

   /**
    * The size of the file in <em>bytes</em>.
    *
    * @var string $size
    */
   private $size;

   /**
    * The extension of the file.
    *
    * @var string $extension
    */
   private $extension;

   /**
    * The MIME type - if applicable.
    *
    * @var string $mimeType
    */
   private $mimeType;

   /**
    * The name of the uploaded temporary file.
    *
    * @var string $temporaryName
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
      $this->size = (int) $size;
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
