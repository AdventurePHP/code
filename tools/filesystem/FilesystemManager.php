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
 * @class FileException
 * @package tools::filesystem
 *
 * Represents an exception, that is thrown on errors concerning file operations.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.08.2010<br />
 */
class FileException extends Exception {
}

/**
 * @static
 * @class FilesystemManager
 * @package tools::filesystem
 *
 * Implements a helper tool for filesystem access, directory and file handling.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.11.2008<br />
 */
class FilesystemManager {

   private function __construct() {
   }

   /**
    * @static
    * @public
    *
    * Deletes the content of a folder (without it's directories) or the entire folder, if
    * the <em>$recursive</em> argument is switched to true.
    *
    * @param string $folder the base folder.
    * @param bool $recursive false = just current content, true = recursive.
    * @return bool Status code (true = ok, false = error).
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2008<br />
    * Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory separator problems)<br />
    */
   public static function deleteFolder($folder, $recursive = false) {

      // clear stat cache to prevent interference with previous calls
      clearstatcache();

      if (!is_dir($folder)) {
         return false;
      }

      // grab content of current dir
      $dirContent = glob(realpath($folder) . '/*');

      // Bug 941: empty directories led to an error since glob() returns false in this case.
      if (is_array($dirContent)) {
         foreach ($dirContent as $file) {
            $file = str_replace('\\', '/', $file);
            if (is_dir($file) && $recursive) {
               FilesystemManager::deleteFolder($file, true);
            } else {
               FilesystemManager::removeFile($file);
            }

            clearstatcache();
         }
      }

      rmdir($folder);

      clearstatcache();

      return true;
   }

   /**
    * @static
    * @public
    *
    * Creates a folder recursively  with the given permission mask.
    *
    * @param string $folder the desired folder to create.
    * @param int|string $permissions the desired folder permissions. See "man umask" for details.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 13.08.2010 (Bugfix: default permissions are now set to ug+rwx; using php's built in recursive path creation)<br />
    */
   public static function createFolder($folder, $permissions = 0770) {

      // normalize folder structure
      $folder = str_replace('\\', '/', $folder);

      if (!is_dir($folder)) {

         // due to a potential PHP bug with directly passing the permissions
         // we have to initiate a workaround containing explicit formatting as
         // well as umask setting to create the folders with correct permissions
         // to provide a common API, octal numbers must be convert to the
         // internal string representation. otherwise we will get wrong
         // permissions with octal numbers!
         if (is_int($permissions)) {
            $permissions = decoct($permissions);
         }

         // now the correct string representation must be created to ensure
         // correct permissions (leading zero is important!)
         $oct = sprintf('%04u', $permissions);

         // to be able to pass the argument as an octal number, the string must
         // be correctly formatted
         $permissions = octdec($oct);

         // on some boxes, the current umask prevents correct permission appliance.
         // thus, the umask is set to 0000 to avoid permission shifts. this maybe
         // a PHP bug but umasks unlike 0000 lead to wrong permissions, however.
         $oldUmask = umask(0);
         mkdir($folder, $permissions, true);
         umask($oldUmask);
      }
   }

   /**
    * @static
    * @public
    *
    * Copies one file to another. If the target already exists, you can switch $force to true.
    * This indicates, that the target file will be overwritten.
    *
    * @param string $sourceFile the source file path.
    * @param string $targetFile the target file path.
    * @param bool $force true = overwrite, false = return error.
    * @return bool Status code (true = ok, false = error).
    * @throws FileException In case the source file is not present.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2008<br />
    * Version 0.2, 29.11.2008 (Fixed bug, that non existing source was not indicated)<br />
    * Version 0.3, 07.08.2010 (Removed trigger_error(), introduced exception)<br />
    */
   public static function copyFile($sourceFile, $targetFile, $force = false) {

      // create realpath from the source and target file
      $source = str_replace('\\', '/', realpath($sourceFile));
      $target = str_replace('\\', '/', $targetFile);
      if (!file_exists($source)) {
         throw new FileException('[FilesystemManager::copyFile()] The source file "'
               . $sourceFile . '" does not exist!', E_USER_NOTICE);
      }

      // copy source to target
      if ((file_exists($target) && $force === true) || !file_exists($target)) {

         if (copy($source, $target)) {
            return true;
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   /**
    * @static
    * @public
    *
    * Removes a given file from the filesystem.
    *
    * @param string $file the file to delete.
    * @return bool Status code (true = ok, false = error).
    * @throws FileException In case the file to remove is not existent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2008<br />
    * Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory seperator problems)<br />
    * Version 0.3, 29.11.2008 (Added check, if the file to delete does exist)<br />
    * Version 0.4, 07.08.2010 (Removed trigger_error(), introduced exception)<br />
    */
   public static function removeFile($file) {

      // check if file exists
      $realFile = str_replace('\\', '/', realpath($file));
      if (!file_exists($realFile)) {
         throw new FileException('[FilesystemManager::removeFile()] The file "' . $file
               . '" does not exist!', E_USER_NOTICE);
      }

      return unlink($realFile);
   }

   /**
    * @public
    * @static
    *
    * Uploads a file sent via PHP's file upload mechanism. The method checks, if the filesize is
    * not above the limit given and whether the mime type is one of the present. If the file is
    * not valid, false will be returned.
    *
    * @param string $dir the target dir to upload the file to.
    * @param string $temp_file the temporary file including it's directory.
    * @param string $file_name name of the target file.
    * @param string $file_size size of the current file in bytes.
    * @param string $file_max_size allowed files size in bytes.
    * @param string $file_type mime type of the current file.
    * @param array $allowed_mime_types list of allowed mime types.
    * @return bool True in case of success, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008 (Added the function to the new FilesystemManager class.)<br />
    */
   public static function uploadFile($dir, $temp_file, $file_name, $file_size, $file_max_size, $file_type, $allowed_mime_types) {

      // check, if the mime type and the size is ok
      if (in_array($file_type, $allowed_mime_types) && ($file_size < $file_max_size)) {

         // if file is a valid uploaded file, handle it
         if (is_uploaded_file($temp_file)) {

            // check if target already exists. if not, upload it
            $target_file = $dir . '/' . $file_name;
            if (file_exists($target_file)) {
               return false;
            } else {
               return move_uploaded_file($temp_file, $dir . '/' . $file_name);
            }
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   /**
    * @static
    * @public
    *
    * Renames the source file to the target file. If the target already exists, you can switch
    * $force to true. This indicates, that the target file will be overwritten.
    *
    * @param string $sourceFile the source file path.
    * @param string $targetFile the target file path.
    * @param bool $force true = overwrite, false = return error.
    * @return bool Status code (true = ok, false = error).
    * @throws FileException In case the source file to rename is not existent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2008<br />
    * Version 0.2, 29.11.2008 (Fixed bug, that non existing source was not indicated)<br />
    * Version 0.3, 07.08.2010 (Removed trigger_error(), introduced exception)<br />
    */
   public static function renameFile($sourceFile, $targetFile, $force = false) {

      // create realpath from the source and target file
      $source = str_replace('\\', '/', realpath($sourceFile));
      $target = str_replace('\\', '/', $targetFile);
      if (!file_exists($source)) {
         throw new FileException('[FilesystemManager::renameFile()] The source file "'
               . $sourceFile . '" does not exist!', E_USER_NOTICE);
      }

      // copy source to target
      if ((file_exists($target) && $force === true) || !file_exists($target)) {

         if (rename($source, $target)) {
            return true;
         } else {
            return false;
         }
      } else {
         return false;
      }
   }

   /**
    * @public
    * @static
    *
    * Returns a list of files/dirs within the given folder. If $fullpath is set to true, the
    * full path to the file/dir is included in the list. Set to false, only the file/dir name
    * is included.
    *
    * @param string $folder the folder that should be read out.
    * @param bool $fullpath false (list contains only file/dir names) | true (full path is returned).
    * @return array A list of files within the given folder.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.11.2008<br />
    */
   public static function getFolderContent($folder, $fullpath = false) {

      // check if folder exists
      $realFolder = str_replace('\\', '/', realpath($folder));
      if (!file_exists($realFolder)) {
         trigger_error('[FilesystemManager::getFolderContent()] The given folder ("' . $folder . '") does not exist!');
         return array();
      }

      // gather folder content
      $folderContent = glob($realFolder . '/*');

      if ($fullpath === false) {

         $count = count($folderContent);

         for ($i = 0; $i < $count; $i++) {
            $folderContent[$i] = basename($folderContent[$i]);
         }
      }

      return $folderContent;
   }

   /**
    * @public
    * @static
    *
    * Returns the attributes of the given file. If the file exists, additional attributes are
    * included. The associative array contains the following offsets:
    * <ul>
    *   <li>extension: the file extentsion</li>
    *   <li>filename: the name of the file without the folder path</li>
    *   <li>folderpath: the folder path</li>
    *   <li>filebody: the filename without extension</li>
    *   <li>modificationdate: the modification date in the format "YYYY-MM-DD" (if file exists only)</li>
    *   <li>modificationtime: the modification time in the format "HH:MM:SS" (if file exists only)</li>
    *   <li>size: the file size in bytes(if file exists only)</li>
    * </ul>
    * If the second argument contains a file attribute, the value is returned instead of a list!
    *
    * @param string $file the desired file.
    * @param string $attributeName the name of the attribute, that should be returned.
    * @return array A list of files within the given folder.
    * @throws FileException In case the file to get the attributes from is not existent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.11.2008<br />
    * Version 0.2, 01.02.2009 (Added the possibility to only return one attribute)<br />
    * Version 0.3, 07.08.2010 (Removed trigger_error(), introduced exception)<br />
    */
   public static function getFileAttributes($file, $attributeName = null) {

      // clear the stat cache to avoid interference with previous calls
      clearstatcache();

      // check if folder exists
      $realFile = str_replace('\\', '/', realpath($file));
      if (!file_exists($realFile)) {
         throw new FileException('[FilesystemManager::getFileAttributes()] The given file ("'
               . $file . '") does not exist!', E_USER_WARNING);
      }

      // gather attributes
      $fileInfo = pathinfo($realFile);
      $attributes['extension'] = $fileInfo['extension'];
      $attributes['filename'] = $fileInfo['basename'];
      $attributes['folderpath'] = $fileInfo['dirname'];
      $attributes['filebody'] = str_replace('.' . $attributes['extension'], '', $attributes['filename']);
      $modTime = filemtime($file);
      $attributes['modificationdate'] = date('Y-m-d', $modTime);
      $attributes['modificationtime'] = date('H:i:s', $modTime);
      $attributes['size'] = intval(filesize($file));

      // return single attribute
      if ($attributeName !== null) {

         if (isset($attributes[$attributeName])) {
            return $attributes[$attributeName];
         } else {
            throw new FileException('[FilesytemManager::getFileAttributes()] The desired file '
                  . 'attribute ("' . $attributes . '") is not a valid attribute. Please consult the '
                  . ' API documentation!', E_USER_ERROR);
         }
      }

      // return whole list
      return $attributes;
   }

   /**
    * @public
    * @static
    *
    * Returns the size of the given folder. The size includes all files and subfolders.
    *
    * @param string $folder the desired folder.
    * @return int The size of the given folder.
    * @throws FileException In case the given folder is not existent.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.11.2008<br />
    * Version 0.2, 07.08.2010 (Removed trigger_error(), introduced exception)<br />
    */
   public static function getFolderSize($folder) {

      // check if folder exists
      $realFolder = str_replace('\\', '/', realpath($folder));
      if (!file_exists($realFolder)) {
         throw new FileException('[FilesystemManager::getFolderSize()] The given folder ("'
               . $folder . '") does not exist!', E_USER_ERROR);
      }

      // get content of the desired folder
      $folderContent = FilesystemManager::getFolderContent($realFolder, true);
      $size = (int)0;

      // collect size recursively
      $count = count($folderContent);
      for ($i = 0; $i < $count; $i++) {

         if (is_dir($folderContent[$i])) {
            $size = (int)FilesystemManager::getFolderSize($folderContent[$i]) + $size;
         } else {
            $fileAttributes = FilesystemManager::getFileAttributes($folderContent[$i]);
            $size = (int)$fileAttributes['size'] + $size;
         }
      }

      return $size;
   }

   /**
    * @public
    * @static
    *
    * This method formats the applied amount of bytes. You are able to provide the number of digits
    * after the colon as well as the unit the value is transformed to.
    * <p/>
    * In case the unit is not defined, the <em>$decimal</em> parameter is used to guess the desired
    * format and unit. In case nothing is provided, the method detects the target or source system
    * to calculate the size in binary mode.
    *
    * @param int $size The byte value to format.
    * @param int $round The number of digits after the colon.
    * @param boolean $server <em>true</em> in case the server OS should be detected to calculate the size,
    *                        <em>false</em> in case the client OS should be used.
    * @param string $unit The unit to transform the value to.
    * @param boolean $decimal Returns (?) the unit of decimal prefix or the unit of the binary prefix.
    * @return string The formatted size of the applied byte amount.
    *
    * @author Werner Liemberger <wpublicmail [at] gmail DOT com>
    * @version
    * Version 1.0, 08.08.2011<br />
    */
   public static function formatByte($size, $round = 2, $server = false, $unit = null, $decimal = null) {

      $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');

      if ($decimal === null) {
         if ($server == false) {
            $userAgent = strtolower($_SERVER['SERVER_SOFTWARE']);
         } else {
            $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
         }
         if (preg_match('/windows|win32/', $userAgent)) {
            $decimal = false;
         }
      }

      if ($decimal == false) {
         $divisor = 1024;
      } else {
         $divisor = 1000;
      }

      if ($unit == null) {
         $i = 0;
         $j = count($units);
         while ($size >= $divisor && $i < $j) {
            $size /= $divisor;
            $i++;
         }
         return round($size, $round) . ' ' . $units[$i];
      } else {
         $key = array_keys($units, $unit, true);
         return round($size / pow($divisor, $key[0]), $round) . ' ' . $unit;
      }
   }

}

?>