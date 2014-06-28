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
use APF\core\configuration\Configuration;
use APF\core\service\APFService;

/**
 * Implements the cache manager component. Due to the generic implementation, all forms of
 * caches can be implemented. For this reason, various targets and cache types are supported
 * by the included reader and writer concept. For application examples, please refer to the
 * online documentation. The configuration is accessible from the outside via the getAttribute()
 * method.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 31.10.2008<br />
 * Version 0.2, 05.08.2010 (Added cache sub key to make cms page caching possible)<br />
 */
final class CacheManager extends CacheBase {

   /**
    * Name of the cache configuration sub section.
    *
    * @var string CACHE_CONFIG_SECTION_NAME
    */
   const CACHE_CONFIG_SECTION_NAME = 'Cache';

   /**
    * The current cache provider.
    *
    * @var CacheProvider $provider
    */
   private $provider = null;

   /**
    * Indicates, if the cache is active. Can be influenced by set('Active') or the
    * cache configuration file. The cache is off by default to avoid strange behavior, if the
    * config value is not set properly.
    * True, in case the cache is active, false otherwise.
    *
    * @var boolean $active
    */
   private $active = false;

   /**
    * Implements the init() method used by the service manager. Initializes the cache
    * manager with the corresponding cache configuration section.
    *
    * @param string[] $initParam The desired cache config section.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 22.11.2008 (Refactored due to fabric introduction.)<br />
    * Version 0.3, 24.11.2008 (Refactoring due to discussions on the fusion of writers and readers)<br />
    * Version 0.4, 24.06.2014 (ID#207: Refactoring due to separation of attribute handling from APFObject)<br />
    */
   public function init($initParam) {

      /* @var $initParam Configuration */
      $cacheConfig = $initParam->getSection(self::CACHE_CONFIG_SECTION_NAME);
      $class = $cacheConfig->getValue('Provider');

      /* @var $provider CacheProvider */
      $provider = $this->getServiceObject($class, APFService::SERVICE_TYPE_NORMAL);

      // inject configuration
      $provider->setConfiguration($initParam);

      $this->provider = $provider;

      // map the active configuration key
      $active = $cacheConfig->getValue('Active');
      if ($active == 'true') {
         $this->active = true;
      }

   }

   /**
    * Returns the content from the cache. If the content is not found in cache,
    * the provider returns null.
    * <p/>
    * The sub cache key was introduced in 1.13 to be able to cache different states
    * of an object. This is especially necessary for page caching, where one single
    * page can be called with different parameters (e.g. for CMS page caching).
    *
    * @param CacheKey $cacheKey The application's cache key.
    *
    * @return mixed The cache content concerning the provider implementation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 05.08.2010 (Added cache sub key to make cms page caching possible)<br />
    */
   public function getFromCache(CacheKey $cacheKey) {
      return ($this->active === true)
            ? $this->provider->read($cacheKey)
            : null;
   }

   /**
    * Writes the desired content to the cache.
    *
    * @param CacheKey $cacheKey the application's cache key.
    * @param mixed $content The content to cache.
    *
    * @return bool True in case of success, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 05.08.2010 (Added cache sub key to make cms page caching possible)<br />
    */
   public function writeToCache(CacheKey $cacheKey, $content) {
      return ($this->active === true)
            ? $this->provider->write($cacheKey, $content)
            : false;
   }

   /**
    * Clears the whole cache in case the cache key is null, or the cache item specified by
    * the given cache key.
    * <p/>
    * Since 1.13, passing the sub cache key, only the specific part of the cache item will be
    * cleared, that belongs to the cache sub key. In case the sub key is null, all
    * entries are deleted belonging to the given cache key.
    *
    * @param CacheKey $cacheKey the application's cache key.
    *
    * @return bool True in case of success, otherwise false.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.11.2008<br />
    * Version 0.2, 05.08.2010 (Added cache sub key to make cms page caching possible)<br />
    */
   public function clearCache(CacheKey $cacheKey = null) {
      return $this->provider->clear($cacheKey);
   }

}
