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
import('tools::filesystem', 'FilesystemItem');
import('tools::filesystem', 'File');

/**
 * @class   Folder
 * @package tools::filesystem
 *
 * @author  Nicolas Pecher
 * @version Version 0.1, 30.04.2012
 */
final class Folder extends FilesystemItem {

   /**
    * @public
    *
    * @param   string $path The path of the Folder
    * @param   int $permission The permission to create the folder with.
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    * @version Version 0.2, 14.08.2012 (Bug-fix: default permissions are now set to ug+rwx; using PHP's built in recursive path creation)
    */
   public function create($path, $permission = 0770) {
      if (!is_dir($path)) {
         
         // due to a potential PHP bug with directly passing the permissions
         // we have to initiate a workaround containing explicit formatting as
         // well as umask setting to create the folders with correct permissions
         // to provide a common API, octal numbers must be convert to the
         // internal string representation. otherwise we will get wrong
         // permissions with octal numbers!
         if (is_int($permission)) {
            $permission = decoct($permission);
         }

         // now the correct string representation must be created to ensure
         // correct permissions (leading zero is important!)
         $oct = sprintf('%04u', $permission);

         // to be able to pass the argument as an octal number, the string must
         // be correctly formatted
         $permission = octdec($oct);

         // on some boxes, the current umask prevents correct permission appliance.
         // thus, the umask is set to 0000 to avoid permission shifts. this maybe
         // a PHP bug but umasks unlike 0000 lead to wrong permissions, however.
         $oldUmask = umask(0);
         
         // the last parameter allows the creation of nested directories
         mkdir($path, $permission, true);
         
         umask($oldUmask);
      }
      $this->open($path);
      return $this;
   }

   /**
    * @public
    *
    * @param string $path The path of the Folder.
    * @return Folder The domain object for further usage.
    * @throws FilesystemException In case the applied folder does not exist.
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function open($path) {
      if (!is_dir($path)) {
         throw new FilesystemException('[Folder::open()] A folder with the passed path '
               . 'does not exists.', E_USER_ERROR);
      }
      $stat = stat($path);
      $pathParts = pathinfo($path);
      $this->owner = $stat['uid'];
      $this->permissions = $stat['mode'];
      $this->name = $pathParts['basename'];
      $this->basePath = $pathParts['dirname'];
      return $this;
   }

   /**
    * @public
    *
    * @param   Folder $folder The Folder where the copy should be stored
    * @param   string $copyName The new name of the copy (optional)
    * @param   boolean $getCopy If true, this method returns the copy (optional)
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function createCopy(Folder $folder, $copyName = null, $getCopy = true) {
      $copyPath = ($folder->getPath()) . '/';
      $copyPath .= ($copyName !== null) ? $copyName : $this->getName();

      $copy = new Folder();
      $copy->create($copyPath);

      $children = $this->getContent();
      foreach ($children as $child) {
         $child->createCopy($copy);
      }

      return $getCopy === true ? $copy : $this;
   }

   /**
    * @public
    *
    * @param   Folder $folder The Folder into which it should be moved
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function moveTo(Folder $folder) {
      $newFolder = new Folder();
      $newFolder->create($folder->getPath() . '/' . $this->getName());

      $children = $this->getContent();
      foreach ($children as $child) {
         $child->moveTo($newFolder);
      }

      $this->delete();
      $this->basePath = $folder->getPath();
      return $this;
   }

   /**
    * @public
    *
    * @return  FilesystemItem[] An array of File- and Folder-Objects
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function getContent() {
      $children = scandir($this->getPath());
      $content = array();
      $i = 0;
      foreach ($children as $child) {
         if ($child !== '.' && $child !== '..') {
            $childPath = $this->getPath() . '/' . $child;
            if (is_dir($childPath)) {
               $content[$i] = new Folder();
            } elseif (is_file($childPath)) {
               $content[$i] = new File();
            }
            $content[$i]->open($childPath);
            $i++;
         }
      }

      return $content;
   }

   /**
    * @public
    *
    * Deletes child-files, -directories and itself
    *
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function delete() {
      $this->deleteContent();
      rmdir($this->getPath());
      return $this;
   }

   /**
    * @public
    *
    * Deletes only the child-files and -directories
    *
    * @return  Folder The domain object for further usage
    *
    * @author  Nicolas Pecher
    * @version Version 0.1, 01.05.2012
    */
   public function deleteContent() {
      $children = $this->getContent();
      foreach ($children as $child) {
         $child->delete();
      }
      return $this;
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
      $children = $this->getContent();

      $size = 0;
      foreach ($children as $child) {
         $size += $child->getSize();
      }

      return $size;
   }

}
