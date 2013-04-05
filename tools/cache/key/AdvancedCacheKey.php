<?php
namespace APF\tools\cache\key;

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
use APF\tools\cache\key\SimpleCacheKey;

/**
 * @package APF\tools\cache\key
 * @class AdvancedCacheKey
 *
 * Describes an enhanced cache key, that addresses a cache item using a
 * cache key and a sub key, that can be used for items, that differ only
 * a little from the original one. Usage is recommended or CMS pages
 * including modules generating different urls for the same page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.08.2010<br />
 */
class AdvancedCacheKey extends SimpleCacheKey {

   private $subKey;

   /**
    * @public
    *
    * Creates an advanced cache key including an additional sub key.
    *
    * @param string $cacheKey The cache key.
    * @param string $cacheSubKey The sub cache key.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2010<br />
    */
   public function __construct($cacheKey, $cacheSubKey) {
      parent::__construct($cacheKey);
      $this->subKey = $cacheSubKey;
   }

   public function getSubKey() {
      return $this->subKey;
   }

   public function setSubKey($cacheSubKey) {
      $this->subKey = $cacheSubKey;
   }

}
