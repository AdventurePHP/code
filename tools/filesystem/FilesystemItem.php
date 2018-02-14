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
namespace APF\tools\filesystem;

use APF\core\pagecontroller\APFObject;
use DateTime;

/**
 * Defines the base class for File and Folder
 *
 * @author  Nicolas Pecher
 * @version Version 0.1, 30.04.2012
 */
abstract class FilesystemItem extends APFObject {

   /**
    * The name of the FilesystemItem
    *
    * @var string $name
    */
   protected $name = null;

   /**
    * The base path of the FilesystemItem
    *
    * @var string $basePath
    */
   protected $basePath = null;

   /**
    * The permissions of the FilesystemItem
    *
    * @var int $permissions
    */
   protected $permissions = null;

   /**
    * The user-id of the file-owner
    *
    * @var int $owner
    */
   protected $owner = null;

   /**
    * @param string $path The path of the item to open.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public abstract function open($path);

   /**
    * @author  Jan Wiese
    * @version Version 0.1, 27.02.2014
    */
   public abstract function close();

   /**
    * @param string $path The path of the item to create.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 06.08.2012
    */
   public abstract function create($path);

   /**
    * @param   Folder $folder
    * @param   string $copyName
    * @param   boolean $getCopy
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function createCopy(Folder $folder, $copyName = null, $getCopy = true);

   /**
    * @param   Folder $folder
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function moveTo(Folder $folder);

   /**
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function delete();

   /**
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function getContent();

   /**
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public abstract function getSize();

   /**
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
    * @return  int The permissions of the FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getPermissions() {
      return $this->permissions;
   }

   /**
    * @return  int The user-id of the owner of the FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getOwner() {
      return $this->owner;
   }

   /**
    * @return  Folder The parent folder of the actual FilesystemItem
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getParentFolder() {
      return new Folder($this->getBasePath());
   }

   /**
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
    * @param   string $newName The new name of the FilesystemItem
    * @param   boolean $force true = overwrite, false = return false
    *
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function renameTo($newName, $force = false) {
      $newPath = $this->getBasePath() . '/' . $newName;
      $fileExists = file_exists($newPath);
      if (($fileExists === true && $force === true) || $fileExists === false) {

         $this->close();

         $renameError = (rename($this->getPath(), $newPath) === false);

         if ($renameError) {
            $this->open($this->getPath());

            return false;
         }

         $this->name = $newName;
         $this->open($newPath);

         return true;
      }

      return false;
   }

   /**
    * @param   mixed $owner The name or the id of the owner
    *
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
    * @param   int $permissions The permissions of the FilesystemItem
    *
    * @return  boolean
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function changeModeTo($permissions) {
      chmod($this->getPath(), $permissions);

      return true;
   }

   /**
    *
    * DateTime::setTimestamp is only available since PHP Version 5.3.0
    * First, we check if the method is available, otherwise we use an alternative for PHP 5.2.0
    * Check also documentation: http://de.php.net/manual/en/datetime.settimestamp.php
    *
    * Please keep in mind that there is no way for a creation time of a file in most Unix filesystems.
    * filectime returns also a new timestamop when owner or rights of the file has been changed!
    * Check also documentation: http://de.php.net/manual/en/function.filectime.php
    *
    * @return \DateTime The creation time as a DateTime-Instance of the file
    *
    * @author  dave
    * @version Version 0.1, 16.08.2012
    */
   public function getCreationTime() {

      clearstatcache();

      $time = new DateTime();

      if (!method_exists($time, 'setTimestamp')) {
         $Timestamp = filectime($this->getPath());

         return new DateTime("@$Timestamp");
      } else {
         return $time->setTimestamp(filectime($this->getPath()));
      }
   }

   /**
    *
    * DateTime::setTimestamp is only available since PHP Version 5.3.0
    * First, we check if the method is available, otherwise we use an alternative for PHP 5.2.0
    * Check also documentation: http://de.php.net/manual/en/datetime.settimestamp.php
    *
    * Please keep in mind that time resolution may differ from one file system to another.
    * Check also documentation: http://de.php.net/manual/en/function.filemtime.php
    *
    * @return \DateTime The modification time as a DateTime-Instance of the file
    *
    * @author  dave
    * @version Version 0.1, 16.08.2012
    */
   public function getModificationTime() {

      clearstatcache();

      $time = new DateTime();

      if (!method_exists($time, 'setTimestamp')) {
         $Timestamp = filemtime($this->getPath());

         return new DateTime("@$Timestamp");
      } else {
         return $time->setTimestamp(filemtime($this->getPath()));
      }
   }

}
