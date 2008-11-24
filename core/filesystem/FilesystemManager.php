<?php
   /**
   *  @static
   *
   *  Implements a helper tool for filesystem access, dir and file handling.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 20.11.2008<br />
   */
   class FilesystemManager
   {

      function FilesystemManager(){
      }


      /**
      *  @static
      *  @public
      *
      *  Deletes the content of a folder (without it's directories) or the entire folder, if
      *  $recursive is switched to true.
      *
      *  @param string $folder the base folder
      *  @param bool $recursive false = just current content, true = recursive
      *  @return bool $status status code (true = ok, false = error)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.11.2008<br />
      *  Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory seperator problems)<br />
      */
      function deleteFolder($folder,$recursive = false){

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
      *  @static
      *  @public
      *
      *  @param string $folder the desired folder to create
      *  @param int $permissions the desired folder permissions. See "man umask" for details
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function createFolder($folder,$permissions = 066){

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
      *  @static
      *  @public
      *
      *  Copies one file to another. If the target already exists, you can switch $force to true.
      *  This indicates, that the target file will be overwritten.
      *
      *  @param string $sourceFile the source file path
      *  @param string $targetFile the target file path
      *  @param bool $force true = overwrite, false = return error
      *  @return bool $status status code (true = ok, false = error)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.11.2008<br />
      */
      function copyFile($sourceFile,$targetFile,$force = false){

         $target = str_replace('\\','/',realpath($targetFile));

         if(file_exists($target) && $force === false){
            return false;
          // end if
         }
         else{
            $source = str_replace('\\','/',realpath($sourceFile));

            if(copy($source,$target)){
               return true;
             // end if
            }
            else{
               return false;
             // end else
            }

          // end else
         }

       // end if
      }


      /**
      *  @static
      *  @public
      *
      *  Removes a given file from the filesystem.
      *
      *  @param string $file the file to delete
      *  @return bool $status status code (true = ok, false = error)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.11.2008<br />
      *  Version 0.2, 24.11.2008 (Bugfix: recursion on windows systems broken due to directory seperator problems)<br />
      */
      function removeFile($file){
         return unlink(str_replace('\\','/',realpath($file)));
       // end function
      }


      /**
      *  @public
      *
      *  Uploads a file sent via PHP's file upload mechanism. The method checks, if the filesize is
      *  not above the limit given and whether the mime type is one of the present. If the file is
      *  not valid, false will be returned.
      *
      *  @param string $dir the target dir to upload the file to
      *  @param string $temp_file the temporary file including it's directory
      *  @param string $file_name name of the target file
      *  @param string $file_size size of the current file in bytes
      *  @param string $file_max_size allowed files size in bytes
      *  @param string $file_type mime type of the current file
      *  @param array $allowed_mime_types list of allowed mime types
      *  @return bool $status true in case of success, false otherwise
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.11.2008 (Added the function to the new FilesystemManager class.)<br />
      */
      function uploadFile($dir,$temp_file,$file_name,$file_size,$file_max_size,$file_type,$allowed_mime_types){

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
      *  @static
      *  @public
      *
      *  Renames the source file to the target file. If the target already exists, you can switch
      *  $force to true. This indicates, that the target file will be overwritten.
      *
      *  @param string $sourceFile the source file path
      *  @param string $targetFile the target file path
      *  @param bool $force true = overwrite, false = return error
      *  @return bool $status status code (true = ok, false = error)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.11.2008<br />
      */
      function renameFile($sourceFile,$targetFile,$force = false){

         $target = str_replace('\\','/',realpath($targetFile));

         if(file_exists($target) && $force === false){
            return false;
          // end if
         }
         else{
            $source = str_replace('\\','/',realpath($sourceFile));

            if(rename($source,$target)){
               return true;
             // end if
            }
            else{
               return false;
             // end else
            }

          // end else
         }

       // end function
      }

      function isFileUnique($file){
      }

      function showFolderContent($folder){
      }

      function showFileAttributes(){
      }

    // end function
   }
?>