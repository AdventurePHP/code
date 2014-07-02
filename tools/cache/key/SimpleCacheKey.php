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
namespace APF\tools\cache\key;

use APF\tools\cache\CacheKey;

/**
 * @package APF\tools\cache\key
 * @class SimpleCacheKey
 *
 * Implements a simple cache key letting you address a cache item with a simple string.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.08.2010<br />
 */
class SimpleCacheKey implements CacheKey {

   private $cacheKey;
   private $ttl;

   public function __construct($cacheKey, $ttl = null) {
      $this->cacheKey = $cacheKey;
      $this->ttl = $ttl;
   }

   public function getKey() {
      return $this->cacheKey;
   }

   public function setKey($cacheKey) {
      $this->cacheKey = $cacheKey;
   }

   public function getTtl() {
      return $this->ttl;
   }

   public function setTtl($ttl) {
      $this->ttl = $ttl;
   }

}
