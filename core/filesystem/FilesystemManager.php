<?php
   /**
   *  @static
   *
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
      */
      function deleteFolder($folder,$recursive = false){

         clearstatcache();

         if(!is_dir($folder)){
            return false;
          // end if
         }
         else{

            $dirContent = glob(realpath($folder).'/*');

            foreach($dirContent as $file){

               if(is_dir($file)){
                  FilesystemManager::deleteFolder($folder,true);
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

         $target = realpath($targetFile);

         if(file_exists($target) && $force === false){
            return false;
          // end if
         }
         else{
            $source = realpath($sourceFile);

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
      */
      function removeFile($file){
         return unlink(realpath($file));
       // end function
      }


      function uploadFile(){
      }

      function renameFile(){
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