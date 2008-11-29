<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::filesystem','FilesystemManager');


   /**
   *  @class TextCacheProvider
   *
   *  Implements the cache reader for normal text content.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 31.10.2008<br />
   */
   class TextCacheProvider extends AbstractCacheProvider
   {

      function TextCacheProvider(){
      }


      /**
      *  @public
      *
      *  Returns the desired cache content or null in case of failure.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @return string $content desired cache content
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      */
      function read($cacheKey){

         $cacheFile = $this->__getCacheFile($cacheKey);
         if(!file_exists($cacheFile)){
            return null;
          // end if
         }
         else{
            return file_get_contents($cacheFile);
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Writes the desired text content to cache.
      *
      *  @param string $cacheFile fully qualified cache file name
      *  @param string $content desired content to cache
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.10.2008<br />
      *  Version 0.2, 23.11.2008 (Adapted to the new reader/writer strategy)<br />
      *  Version 0.3, 24.11.2008 (Refactoring due to provider introduction)<br />
      */
      function write($cacheKey,$content){

         // build cache file name and create cache folder
         $cacheFile = $this->__getCacheFile($cacheKey);
         FilesystemManager::createFolder(dirname($cacheFile));

         // write cache
         $fH = fopen($cacheFile,'w+');
         fwrite($fH,$content);
         fclose($fH);
         return true;

       // end function
      }


      /**
      *  @private
      *
      *  Returns the complete cache file name.
      *
      *  @param string $cacheKey the application's cache key
      *  @return string $cacheFile the cache file name
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.11.2008<br />
      */
      function __getCacheFile($cacheKey){

         $cacheKey = md5($cacheKey);
         $subFolder = substr($cacheKey,0,2);
         $baseFolder = $this->__getCacheConfigAttribute('Cache.BaseFolder');
         $namespace = $this->__getCacheConfigAttribute('Cache.Namespace');
         return $baseFolder.'/'.str_replace('::','/',$namespace).'/'.$subFolder.'/'.$cacheKey.'.apfc';

       // end function
      }


      /**
      *  @public
      *
      *  Implements the provider's clear() method.
      *
      *  @param string $cacheKey the application's cache key or null (clear entire cache namespace)
      *  @return string $result true|false
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 25.11.2008<br />
      */
      function clear($cacheKey = null){

         if($cacheKey === null){
            $baseFolder = $this->__getCacheConfigAttribute('Cache.BaseFolder');
            FilesystemManager::deleteFolder($baseFolder,true);
          // end if
         }
         else{
            $cacheFile = $this->__getCacheFile($cacheKey);
            FilesystemManager::removeFile($cacheFile);
          // end else
         }

       // end function
      }

    // end class
   }
?>