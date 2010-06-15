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
    * @static
    * @class FilesystemManager
    * @package tools::filesystem
    *
    * Implements a helper tool for filesystem access, dir and file handling.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.11.2008<br />
    */
   class FilesystemManager {

      private function FilesystemManager(){
      }

      /**
       * @static
       * @public
       *
       * Deletes the content of a folder (without it's directories) or the entire folder, if
       * $recursive is switched to true.
       *
       * @param string $folder the base folder
       * @param bool $recursive false = just current content, true = recursive
       * @return bool $status status code (true = ok, false = error)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.11.2008<br />
       * Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory seperator problems)<br />
       */
      public static function deleteFolder($folder,$recursive = false){

         // clear stat cache to prevent interference with previous calls
         clearstatcache();

         if(!is_dir($folder)){
            return false;
          // end if
         }
         else{

            // grab content of current dir
            $dirContent = glob(realpath($folder).'/*');

            foreach($dirContent as $file){
               $file = str_replace('\\','/',$file);
               if(is_dir($file)){
                  FilesystemManager::deleteFolder($file,true);
                // end if
               }
               else{
                  FilesystemManager::removeFile($file);
                // end else
               }

               clearstatcache();

             // end foreach
            }

            rmdir($folder);

          // end else
         }

         clearstatcache();

         return true;

       // end function
      }

      /**
       * @static
       * @public
       *
       * @param string $folder the desired folder to create
       * @param int $permissions the desired folder permissions. See "man umask" for details
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.11.2008<br />
       */
      public static function createFolder($folder,$permissions = 660){

         // normalize folder structure
         $folder = str_replace('\\','/',$folder);

         if(!is_dir($folder)){

            // split path into it's peaces
            $folderArray = explode('/',$folder);

            // if the first part is empty (due to trailing slash) remove it
            if(empty($folderArray[0])){
               array_shift($folderArray);
             // end if
            }

            // initialize current path section
            $currentPath = '/';

            // initialize dir seperator
            $dirDelimiter = '';

            // Verzeichnisse rekursiv anlegen
            for($i = 0; $i < count($folderArray); $i++){

               $currentPath .= $dirDelimiter.$folderArray[$i];

               // Special case WINDOWS: if the current path sequence is
               // like "/e:", the "/" must be replaced
               if(preg_match('=/[a-z]{1}:=i',$currentPath)){
                  $currentPath = str_replace('/','',$currentPath);
                // end if
               }

               // Special case for relative paths, that start with "." or ".."
               if(substr($currentPath,0,2) == '/.'){
                  $currentPath = str_replace('/.','.',$currentPath);
                // end if
               }
               if(substr($currentPath,0,3) == '/..'){
                  $currentPath = str_replace('/..','..',$currentPath);
                // end if
               }

               // create folder if it is no symbolic link or dir
               if($folderArray[$i] != '..' && $folderArray[$i] != '.' && !is_dir($currentPath)){
                  mkdir($currentPath,$permissions);
                // end if
               }

               $dirDelimiter = '/';

             // end for
            }

          // end if
         }

       // end function
      }

      /**
       * @static
       * @public
       *
       * Copies one file to another. If the target already exists, you can switch $force to true.
       * This indicates, that the target file will be overwritten.
       *
       * @param string $sourceFile the source file path
       * @param string $targetFile the target file path
       * @param bool $force true = overwrite, false = return error
       * @return bool $status status code (true = ok, false = error)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.11.2008<br />
       * Version 0.2, 29.11.2008 (Fixed bug, that non existing source was not indicated)<br />
       */
      public static function copyFile($sourceFile,$targetFile,$force = false){

         // create realpath from the source and target file
         $source = str_replace('\\','/',realpath($sourceFile));
         $target = str_replace('\\','/',$targetFile);
         if(!file_exists($source)){
            trigger_error('[FilesystemManager::copyFile()] The source file "'.$sourceFile.'" does not exist!',E_USER_NOTICE);
            return false;
          // end if
         }

         // copy source to target
         if((file_exists($target) && $force === true) || !file_exists($target)){

            if(copy($source,$target)){
               return true;
             // end if
            }
            else{
               return false;
             // end else
            }

          // end if
         }
         else{
            return false;
          // end else
         }

       // end if
      }

      /**
       * @static
       * @public
       *
       * Removes a given file from the filesystem.
       *
       * @param string $file the file to delete
       * @return bool $status status code (true = ok, false = error)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.11.2008<br />
       * Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory seperator problems)<br />
       * Version 0.3, 29.11.2008 (Added check, if the file to delete does exist)<br />
       */
      public static function removeFile($file){

         // check if file exists
         $realFile = str_replace('\\','/',realpath($file));
         if(!file_exists($realFile)){
            trigger_error('[FilesystemManager::removeFile()] The file "'.$file.'" does not exist!',E_USER_NOTICE);
            return false;
          // end if
         }

         return unlink($realFile);

       // end function
      }

      /**
       * @public
       * @static
       *
       * Uploads a file sent via PHP's file upload mechanism. The method checks, if the filesize is
       * not above the limit given and whether the mime type is one of the present. If the file is
       * not valid, false will be returned.
       *
       * @param string $dir the target dir to upload the file to
       * @param string $temp_file the temporary file including it's directory
       * @param string $file_name name of the target file
       * @param string $file_size size of the current file in bytes
       * @param string $file_max_size allowed files size in bytes
       * @param string $file_type mime type of the current file
       * @param array $allowed_mime_types list of allowed mime types
       * @return bool $status true in case of success, false otherwise
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.11.2008 (Added the function to the new FilesystemManager class.)<br />
       */
      public static function uploadFile($dir,$temp_file,$file_name,$file_size,$file_max_size,$file_type,$allowed_mime_types){

         // check, if the mime type and the size is ok
         if(in_array($file_type,$allowed_mime_types) && ($file_size < $file_max_size)){

            // delete special characters that should not be contained in file names
            // BAD: dependency to the tools namespace! Due to this, the function is
            // now commented out and the application must care of this issue itself!
            //$file_name = stringAssistant::replaceSpecialCharacters($file_name);

            // if file is a valid uploaded file, handle it
            if(is_uploaded_file($temp_file)){

               // check if target already exists. if not, upload it
               $target_file = $dir.'/'.$file_name;
               if(file_exists($target_file)){
                  return false;
                // end if
               }
               else{
                  return move_uploaded_file($temp_file,$dir.'/'.$file_name);
                // end else
               }

             // end if
            }
            else{
               return false;
             // end else
            }

          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      /**
       * @static
       * @public
       *
       * Renames the source file to the target file. If the target already exists, you can switch
       * $force to true. This indicates, that the target file will be overwritten.
       *
       * @param string $sourceFile the source file path
       * @param string $targetFile the target file path
       * @param bool $force true = overwrite, false = return error
       * @return bool $status status code (true = ok, false = error)
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.11.2008<br />
       * Version 0.2, 29.11.2008 (Fixed bug, that non existing source was not indicated)<br />
       */
      public static function renameFile($sourceFile,$targetFile,$force = false){

         // create realpath from the source and target file
         $source = str_replace('\\','/',realpath($sourceFile));
         $target = str_replace('\\','/',$targetFile);
         if(!file_exists($source)){
            trigger_error('[FilesystemManager::renameFile()] The source file "'.$sourceFile.'" does not exist!',E_USER_NOTICE);
            return false;
          // end if
         }

         // copy source to target
         if((file_exists($target) && $force === true) || !file_exists($target)){

            if(rename($source,$target)){
               return true;
             // end if
            }
            else{
               return false;
             // end else
            }

          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      /**
       * @public
       * @static
       *
       * Returns a list of files/dirs within the given folder. If $fullpath is set to true, the
       * full path to the file/dir is included in the list. Set to false, only the file/dir name
       * is included.
       *
       * @param string $folder the folder that should be read out
       * @param bool $fullpath false (list contains only file/dir names) | true (full path is returned)
       * @return array $files a list of files within the given folder
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.11.2008<br />
       */
      public static function getFolderContent($folder,$fullpath = false){

         // check if folder exists
         $realFolder = str_replace('\\','/',realpath($folder));
         if(!file_exists($realFolder)){
            trigger_error('[FilesystemManager::getFolderContent()] The given folder ("'.$folder.'") does not exist!');
            return array();
          // end if
         }

         // gather folder content
         $folderContent = glob($realFolder.'/*');

         if($fullpath === false){

            $count = count($folderContent);

            for($i = 0; $i < $count; $i++){
               $folderContent[$i] = basename($folderContent[$i]);
             // end for
            }

          // end if
         }

         return $folderContent;

       // end function
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
       * @param string $file the desired file
       * @param string $attributeName the name of the attribute, that should be returned
       * @return array $files a list of files within the given folder
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.11.2008<br />
       * Version 0.2, 01.02.2009 (Added the possibility to only return one attribute)<br />
       */
      public static function getFileAttributes($file,$attributeName = null){

         // clear the stat cache to avoid interference with previous calls
         clearstatcache();

         // check if folder exists
         $realFile = str_replace('\\','/',realpath($file));
         if(!file_exists($realFile)){
            trigger_error('[FilesystemManager::getFileAttributes()] The given file ("'.$file.'") does not exist!');
            return array();
          // end if
         }

         // gather attributes
         $fileInfo = pathinfo($realFile);
         $attributes['extension'] = $fileInfo['extension'];
         $attributes['filename'] = $fileInfo['basename'];
         $attributes['folderpath'] = $fileInfo['dirname'];
         $attributes['filebody'] = str_replace('.'.$attributes['extension'],'',$attributes['filename']);
         $modTime = filemtime($file);
         $attributes['modificationdate'] = date('Y-m-d',$modTime);
         $attributes['modificationtime'] = date('H:i:s',$modTime);
         $attributes['size'] = intval(filesize($file));

         // return attribute
         if($attributeName !== null){

            if(isset($attributes[$attributeName])){
               return $attributes[$attributeName];
             // end if
            }
            else{
               trigger_error('[ImageManager::getImageAttributes()] The desired file attribute ("'.$attributes.'") does not exist!');
               return null;
             // end else
            }

          // end if
         }

         // return whole list
         return $attributes;

       // end function
      }

      /**
       * @public
       * @static
       *
       * Returns the size of the given folder. The size includes all files and subfolders.
       *
       * @param string $folder the desired folder
       * @return int $size the size of the given folder
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.11.2008<br />
       */
      public static function getFolderSize($folder){

         // check if folder exists
         $realFolder = str_replace('\\','/',realpath($folder));
         if(!file_exists($realFolder)){
            trigger_error('[FilesystemManager::getFolderSize()] The given folder ("'.$folder.'") does not exist!');
            return (int)0;
          // end if
         }

         // get content of the desired folder
         $folderContent = FilesystemManager::getFolderContent($realFolder,true);
         $size = (int)0;

         // collect size recursively
         $count = count($folderContent);
         for($i = 0; $i < $count; $i++){

            if(is_dir($folderContent[$i])){
               $size = (int)FilesystemManager::getFolderSize($folderContent[$i]) + $size;
             // end if
            }
            else{
               $fileAttributes = FilesystemManager::getFileAttributes($folderContent[$i]);
               $size = (int)$fileAttributes['size'] + $size;
             // end else
            }

          // end for
         }

         return $size;

       // end function
      }

    // end function
   }
?>