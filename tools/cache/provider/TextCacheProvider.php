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
import('tools::filesystem', 'FilesystemManager');
import('tools::cache::key', 'SimpleCacheKey');

/**
 * @package tools::cache::provider
 * @class TextCacheProvider
 *
 * Implements the cache provider for normal text content.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 */
class TextCacheProvider extends CacheBase implements CacheProvider {

   public function read(CacheKey $cacheKey) {
      $cacheFile = $this->getCacheFile($cacheKey);
      return file_exists($cacheFile)
            ? file_get_contents($cacheFile)
            : null;
   }

   public function write(CacheKey $cacheKey, $content) {

      // configure file mask
      try {
         $fileMask = sprintf('%04u', $this->getConfigAttribute('Cache.FolderPermission'));
      } catch (InvalidArgumentException $e) {
         $fileMask = '0770';
      }

      // build cache file name and create cache folder
      $cacheFile = $this->getCacheFile($cacheKey);
      FilesystemManager::createFolder(dirname($cacheFile), $fileMask);

      // write cache
      $fH = fopen($cacheFile, 'w+');
      fwrite($fH, $content);
      fclose($fH);
      return true;

   }

   /**
    * @protected
    *
    * Returns the complete cache file name. Due to performance reasons,
    * the folder structure is added a sub folder to not have all cache
    * files within one folder.
    *
    * @param CacheKey $cacheKey the application's cache key.
    * @return string The cache file name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 05.08.2010 (Enhanced cache file structure)<br />
    */
   protected function getCacheFile(CacheKey $cacheKey) {

      $baseFolder = $this->getConfigAttribute('Cache.BaseFolder');
      $namespace = str_replace('::', '/', $this->getConfigAttribute('Cache.Namespace'));

      $key = md5($cacheKey->getKey());
      $subFolder = substr($key, 0, 2);

      return $baseFolder . '/' . $namespace . '/' . $subFolder . '/' . $key . '.apfc';

   }

   public function clear(CacheKey $cacheKey = null) {

      $baseFolder = $this->getConfigAttribute('Cache.BaseFolder');

      if ($cacheKey === null) {
         FilesystemManager::deleteFolder($baseFolder, true);
      } else {
         $cacheFile = $this->getCacheFile($cacheKey);
         try {
            FilesystemManager::removeFile($cacheFile);
            return true;
         } catch (FileException $e) {
            return false;
         }
      }

      return false;
   }

}

?>