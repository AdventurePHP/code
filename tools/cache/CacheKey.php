<?php
namespace APF\tools\cache;

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
 * @package APF\tools\cache
 * @class CacheKey
 *
 * Describes the requirements of a cache key, a selection criterion for cache items..
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.08.2010<br />
 */
interface CacheKey {

   /**
    * @public
    *
    * Returns the cache key.
    *
    * @return string The cache key to identify the cache entry with.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.08.2010<br />
    */
   public function getKey();

   /**
    * @public
    *
    * Returns the time-to-live of the current cache key.
    *
    * @return int The time to live for the cache entry in seconds.
    *
    * @author Daniel Basedow
    */
   public function getTtl();

}
