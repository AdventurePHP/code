<?php
namespace APF\tools\filesystem;

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
use APF\tools\filesystem\FilesystemItem;
use APF\tools\http\HeaderManager;

/**
 * @package APF\tools\filesystem
 * @class   File
 *
 * @author  Nicolas Pecher
 * @version Version 0.1, 30.04.2012
 */
class File extends FilesystemItem {

   /**
    * @private
    * @var resource A file pointer resource
    */
   protected $fileHandle = null;

   /**
    * @private
    * @var string The content of the file
    */
   protected $content = null;

   /**
    * @private
    * @var string The mime type of the file
    */
   protected $mimeType = null;

   /**
    * @public
    *
    * @param string $path A path that will be used as file path for the new file
    * @return File This instance for further usage.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public function create($path) {
      if (!is_file($path)) {
         $this->fileHandle = fopen($path, "w+");
      }
      $this->open($path);
      return $this;
   }

   /**
    * @public
    *
    * @param string $path The path to the file that should be opened.
    * @return File This instance for further usage.
    * @throws FilesystemException In case the file does not exist.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public function open($path) {

      if (!is_file($path)) {
         throw new FilesystemException('[File::open()] A file with the passed '
               . 'path does not exists.', E_USER_ERROR);
      }

      if (!is_resource($this->fileHandle)) {
         $this->fileHandle = fopen($path, "r+");
      }

      $stat = stat($path);
      $this->content = file_get_contents($path);
      $this->owner = $stat['uid'];
      $this->permissions = $stat['mode'];

      $pathParts = pathinfo($path);
      $this->name = $pathParts['basename'];
      $this->basePath = $pathParts['dirname'];

      // check if fileinfo extension is available
      if (extension_loaded('fileinfo')) {
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         $this->mimeType = finfo_file($finfo, $this->getPath());
      }

      return $this;
   }

   /**
    * @public
    *
    * Closes the opened file handle
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function __destruct() {
      if (is_resource($this->fileHandle)) {
         fclose($this->fileHandle);
      }
   }

   /**
    * @public
    *
    * @param Folder $folder The Folder where the copy should be stored.
    * @param string $copyName The new name of the copy (optional).
    * @param boolean $getCopy If true, this method returns the copy (optional).
    * @return File The domain object for further usage.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function createCopy(Folder $folder, $copyName = null, $getCopy = true) {
      $copyPath = $folder->getPath() . '/';
      $copyPath .= ($copyName !== null) ? $copyName : $this->getName();
      copy($this->getPath(), $copyPath);
      $copy = new File();
      $copy->open($copyPath);
      return ($getCopy === true) ? $copy : $this;
   }

   /**
    * @public
    *
    * @param   Folder $folder The Folder into which it should be moved
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function moveTo(Folder $folder) {
      copy($this->getPath(), $folder->getPath() . '/' . $this->getName());
      $this->delete();
      $this->basePath = $folder->getPath();
      return $this;
   }

   /**
    * @public
    *
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    *          Version 0.2, 24.04.2013, Added fclose() call.
    */
   public function delete() {
      fclose($this->fileHandle);
      unlink($this->getPath());
      return $this;
   }

   /**
    * @public
    *
    * @return  string The content of the file
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getContent() {
      return $this->content;
   }

   /**
    * @public
    *
    * @return  int The size in Bytes
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getSize() {
      return filesize($this->getPath());
   }

   /**
    * @public
    *
    * @return  string The content type (mime-format)
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getMimeType() {
      return $this->mimeType;
   }

   /**
    * @public
    *
    * @return  string The file-extension
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getExtension() {
      $name = $this->getName();
      return substr(strrchr($name, '.'), 1);
   }

   /**
    * @public
    *
    * @param string $content The content that should be inserted into the file
    * @return File This instance for further usage.
    * @throws FilesystemException In case the current object has not been opened or created before.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
	* 		   Version 0.2, 14.11.2013 (Bug #84: Truncate content and then write new content [Megger])
    */
   public function writeContent($content) {

      if (!is_resource($this->fileHandle)) {
         throw new FilesystemException('[File::writeContent()] You have to set up this '
               . 'domain object by using File::open() or File::create() before calling '
               . 'this function.', E_USER_ERROR);
      }
	  
	  if (ftruncate($this->fileHandle, 0) === false) {
		  return false;
	  }

      if (fwrite($this->fileHandle, $content) === false) {
         return false;
      }

      $this->content = $content;
      return $this;
   }

   /**
    * @public
    *
    * @param   string $content The content that should be appended to the actual content
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function appendContent($content) {
      $newContent = $this->content . $content;
      $this->writeContent($newContent);
      return $this;
   }

   /**
    * @public
    *
    * @param   string $content The content that should be prepended to the actual content
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function prependContent($content) {
      $newContent = $content . $this->content;
      $this->writeContent($newContent);
      return $this;
   }

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function makeDownload($filename = '') {
      if (empty($filename)) {
         $filename = $this->getName();
      }
      HeaderManager::send('Content-type: ' . $this->getMimeType());
      HeaderManager::send('Content-Disposition: attachment; filename="' . $filename . '"');
      readfile($this->getPath());
   }

}
