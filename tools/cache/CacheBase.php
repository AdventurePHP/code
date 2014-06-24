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
use APF\core\pagecontroller\APFObject;
use APF\core\registry\Registry;
use InvalidArgumentException;

/**
 * @package APF\tools\cache
 * @class CacheBase
 * @abstract
 *
 * Implements an abstract base class for the cache provider and the cache manager. Includes
 * a generic access method to the cache configuration attributes.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 24.11.2008<br />
 */
// TODO Refactor cache parameter injection as APFObject has no getAttribute()
abstract class CacheBase extends APFObject {

   /**
    * @protected
    *
    * Returns the value of the cache config attribute or throws an exception,
    * in case the attribute is not given within the attributes array.
    *
    * @param string $name The name of the desired attribute.
    * @return string Value of the attribute.
    * @throws InvalidArgumentException In case the desired attribute is not defined.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 24.11.2008<br />
    */
   protected function getConfigAttribute($name) {

      $value = $this->getAttribute($name);
      if ($value == null) {
         $env = Registry::retrieve('APF\core', 'Environment');
         throw new InvalidArgumentException('[' . get_class($this)
            . '::getConfigAttribute()] The configuration directive "' . $name . '" is not '
            . 'present or empty. Please check your cache configuration ("' . $env
            . '_cacheconfig.ini") for namespace "APF\tools\cache" and context "'
            . $this->getContext() . '" or consult the documentation!', E_USER_ERROR);
      }
      return $value;
   }

   /**
    * @protected
    *
    * Let's you retrieve the validity time of the cache entries.
    *
    * @param CacheKey $cacheKey The current cache key.
    * @return int The validity time of the cache entry in seconds.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.01.2013<br />
    */
   protected function getExpireTime(CacheKey $cacheKey) {
      if ($cacheKey->getTtl() !== null) {
         return $cacheKey->getTtl();
      }
      try {
         return intval($this->getConfigAttribute('Cache.ExpireTime'));
      } catch (InvalidArgumentException $e) {
         return 0; // cache forever
      }
   }

}
