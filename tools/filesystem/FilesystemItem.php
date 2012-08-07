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
import('tools::filesystem', 'FilesystemException');

/**
 * @abstract
 * @class   FilesystemItem
 * @package tools::filesystem
 *
 * Defines the base class for File and Folder
 *
 * @author  Nicolas Pecher
 * @version Version 0.1, 30.04.2012
 */
abstract class FilesystemItem extends APFObject {

   /**
    * @protected
    * @var string The name of the FilesystemItem
    */
   protected $name = null;

   /**
    * @protected
    * @var string The base path of the FilesystemItem
    */
   protected $basePath = null;

   /**
    * @protected
    * @var int The permissions of the FilesystemItem
    */
   protected $permissions = null;

   /**
    * @protected
    * @var int The user-id of the file-owner
    */
   protected $owner = null;

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public abstract function open($path);

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public abstract function create($path);

   /**
    * @public
    *
    * @param   Folder $folder
    * @param   string $copyName
    * @param   boolean $getCopy
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function createCopy(Folder $folder, $copyName = null, $getCopy = true);

   /**
    * @public
    *
    * @param   Folder $folder
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function moveTo(Folder $folder);

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function delete();

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function getContent();

   /**
    * @public
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function getSize();

   /**
    * @public
    *
    * @return  string The name of the FilesystemItem
    * @throws FilesystemException In case there is no name defined.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getName() {
      if (empty($this->name)) {
         throw new FilesystemException('[Filesystem::getName()] The name is not '
               . 'defined', E_USER_ERROR);
      }
      return $this->name;
   }

   /**
    * @public
    *
    * @return  string The base path of the FilesystemItem
    * @throws FilesystemException In case there is no basePath defined.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getBasePath() {
      if (empty($this->basePath)) {
         throw new FilesystemException('[Filesystem::getName()] The base path is not '
               . 'defined', E_USER_ERROR);
      }
      return $this->basePath;
   }

   /**
    * @public
    *
    * The name of the FilesystemItem is appended to the base path.
    * So this function returns the whole path of the file.
    *
    * @return  string The whole path of the FilesystemItem
    * @throws FilesystemException In case there is no basePath.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getPath() {
      return $this->basePath . '/' . $this->name;
   }

   /**
    * @public
    *
    * @return  int The permissions of the FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getPermissions() {
      return $this->permissions;
   }

   /**
    * @public
    *
    * @return  int The user-id of the owner of the FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getOwner() {
      return $this->owner;
   }

   /**
    * @public
    *
    * @return  Folder The parent folder of the actual FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getParentFolder() {
      return new Folder($this->getBasePath());
   }

   /**
    * @public
    *
    * This function tells you if the actual FilesystemItem is readable
    *
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function isReadable() {
      return is_readable($this->getPath());
   }

   /**
    * @public
    *
    * This function tells you if the actual FilesystemItem is writable
    *
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function isWritable() {
      return is_writable($this->getPath());
   }

   /**
    * @public
    *
    * @param   string $newName The new name of the FilesystemItem
    * @param   boolean $force true = overwrite, false = return false
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function renameTo($newName, $force = false) {
      $newPath = $this->getBasePath() . '/' . $newName;
      $fileExists = file_exists($newPath);
      if (($fileExists === true && $force === true) || $fileExists === false) {
         if (rename($this->getPath(), $newPath) === false) {
            return false;
         }
         $this->name = $newName;
         return true;
      }
      return false;
   }

   /**
    * @public
    *
    * @param   mixed $owner The name or the id of the owner
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function changeOwnerTo($owner) {
      chown($this->getPath(), $owner);
      return true;
   }

   /**
    * @public
    *
    * @param   int $permissions The permissions of the FilesystemItem
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function changeModeTo($permissions) {
      chmod($this->getPath(), $permissions);
      return true;
   }
}
