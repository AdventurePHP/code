<?php
namespace APF\tools\cache\provider;

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
use APF\tools\cache\CacheBase;
use APF\tools\cache\CacheKey;
use APF\tools\cache\CacheProvider;
use APF\tools\cache\key\AdvancedCacheKey;
use APF\tools\filesystem\File;
use APF\tools\filesystem\FileException;
use APF\tools\filesystem\FilesystemManager;
use InvalidArgumentException;

/**
 * @package APF\tools\cache\provider
 * @class TextCacheProvider
 *
 * Implements the cache provider for normal text content.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 * Version 0.2, 20.11.2013 (Added TTL-based caching implementation option as described in ID#5)<br />
 */
class TextCacheProvider extends CacheBase implements CacheProvider {

   public function read(CacheKey $cacheKey) {

      // add a creation time check here to support it for all text-based providers
      $cacheFile = $this->getCacheFile($cacheKey);

      // Consider files only valid until *modification time* is at least not older than now (TTL).
      if (file_exists($cacheFile)) {

         $file = new File();
         $creationTime = $file->open($cacheFile)->getModificationTime();

         $creationTimeStamp = $creationTime->getTimestamp();
         $ttl = $this->getExpireTime($cacheKey); // get combined TTL with fallback on global configuration

         $now = time();

         // evaluate with "cache forever"-safe ttl check (TTL=0)
         $difference = $now - $creationTimeStamp;
         if ($ttl === 0 || $difference <= $ttl) {
            return file_get_contents($cacheFile);
         } else {
            return null;
         }
      } else {
         return null;
      }
   }

   public function write(CacheKey $cacheKey, $content) {

      // configure file mask
      try {
         $fileMask = sprintf('%04u', $this->getConfigAttribute('FolderPermission'));
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
    * Returns the complete cache file name. Due to filesystem performance reasons,
    * the cache key folder is separated into several parts:
    * <ul>
    * <li>base folder</li>
    * <li>namespace</li>
    * <li>2 letters of the cache key</li>
    * <li>cache key</li>
    * <li>2 letters of cache sub key (in case of an AdvancedCacheKey)</li>
    * <li>cache sub key as file name (in case of an AdvancedCacheKey)</li>
    * <li>apfc as file extension</li>
    * </ul>
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

      $baseFolder = $this->getConfigAttribute('BaseFolder');
      $namespace = str_replace('\\', '/', $this->getConfigAttribute('Namespace'));

      $key = md5($cacheKey->getKey());
      $folder = substr($key, 0, 2);

      if ($cacheKey instanceof AdvancedCacheKey) {
         $subKey = md5($cacheKey->getSubKey());
         $subFolder = substr($subKey, 0, 2);
         return $baseFolder . '/' . $namespace . '/' . $folder . '/' . $key . '/' . $subFolder . '/' . $subKey . '.apfc';
      } else {
         return $baseFolder . '/' . $namespace . '/' . $folder . '/' . $key . '.apfc';
      }
   }

   public function clear(CacheKey $cacheKey = null) {

      $baseFolder = $this->getConfigAttribute('BaseFolder');

      if ($cacheKey === null) {
         return FilesystemManager::deleteFolder($baseFolder, true);
      } else {
         $cacheFile = $this->getCacheFile($cacheKey);
         try {
            return FilesystemManager::removeFile($cacheFile);
         } catch (FileException $e) {
            return false;
         }
      }
   }

}
