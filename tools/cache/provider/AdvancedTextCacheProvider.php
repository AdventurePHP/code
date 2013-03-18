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
use APF\tools\filesystem\FilesystemManager;
use APF\tools\cache\provider\TextCacheProvider;

/**
 * @package tools::cache::provider
 * @class AdvancedTextCacheProvider
 *
 * Implements the cache provider for normal text content using
 * an enhanced cache key.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 */
class AdvancedTextCacheProvider extends TextCacheProvider {

   public function clear(CacheKey $cacheKey = null) {

      $baseFolder = $this->getConfigAttribute('Cache.BaseFolder');
      $namespace = str_replace('::', '/', $this->getConfigAttribute('Cache.Namespace'));

      // in case we do not have a cache key, remove the entire cache
      if ($cacheKey === null) {
         try {
            FilesystemManager::deleteFolder($baseFolder . '/' . $namespace, true);
            return true;
         } catch (FileException $e) {
            return false; // indicate, that nothing was to delete (e.g. cache not active or empty)
         }
      }

      /* @var $cacheKey AdvancedCacheKey */
      $key = $cacheKey->getKey();
      $subKey = $cacheKey->getSubKey();

      if ($key == null && $subKey == null) {
         FilesystemManager::deleteFolder($baseFolder . '/' . $namespace, true);
      } elseif ($key != null && $subKey == null) {

         // in case we have the cache key only, delete the entire structure
         // including all sub cache entries
         $key = md5($key);
         $folder = $baseFolder . '/' . $namespace . '/' . substr($key, 0, 2) . '/' . $key;
         FilesystemManager::deleteFolder($folder, true);
         return true;

      } else {

         // in case we have both cache key and cache sub key, delete the local
         // cache entry structure
         $file = $this->getCacheFile($cacheKey);
         try {
            FilesystemManager::removeFile($file);
            return true;
         } catch (FileException $e) {
            return false;
         }

      }

      return false;
   }

}
