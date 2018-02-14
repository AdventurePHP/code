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
namespace APF\tools\cache\provider;

use APF\tools\cache\CacheKey;

/**
 * Implements the cache provider for serialized php objects.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 * Version 0.2, 23.11.2008 (The reader now inherits from the TextCacheReader because of same functionalities)<br />
 * Version 0.3, 24.11.2008 (Refactoring tue to provider introduction.)<br />
 */
class ObjectCacheProvider extends TextCacheProvider {

   public function read(CacheKey $cacheKey) {

      $content = parent::read($cacheKey);
      if ($content === null) {
         return null;
      } else {

         $unserialized = @unserialize($content);
         if ($unserialized === false) {
            $this->clear($cacheKey);

            return null;
         } else {
            return $unserialized;
         }

      }

   }

   public function write(CacheKey $cacheKey, $object) {
      $serialized = @serialize($object);

      return ($serialized !== false)
            ? parent::write($cacheKey, $serialized)
            : false;
   }

   public function clear(CacheKey $cacheKey = null) {
      return parent::clear($cacheKey);
   }

}
