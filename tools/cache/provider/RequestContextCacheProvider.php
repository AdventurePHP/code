<?php
namespace APF\tools\cache\provider;

use APF\tools\cache\CacheBase;
use APF\tools\cache\CacheKey;
use APF\tools\cache\CacheProvider;
use APF\tools\cache\key\AdvancedCacheKey;

/**
 * Cache provider to synchronize content within the current HTTP request (e.g. back-end calls that
 * are fired from different HMVC modules against the same back-end).
 * <p/>
 * Does not support CacheKey::getTtl() as the cache is only valid within one request.
 */
class RequestContextCacheProvider extends CacheBase implements CacheProvider {

   /**
    * @var array The static cache store.
    */
   private static $cacheStore = [];

   public function write(CacheKey $cacheKey, $object) {
      if ($cacheKey instanceof AdvancedCacheKey) {
         self::$cacheStore[$cacheKey->getKey()][$cacheKey->getSubKey()] = $object;
      } else {
         self::$cacheStore[$cacheKey->getKey()] = $object;
      }
   }

   public function read(CacheKey $cacheKey) {
      if ($cacheKey instanceof AdvancedCacheKey) {
         return isset(self::$cacheStore[$cacheKey->getKey()][$cacheKey->getSubKey()])
               ? self::$cacheStore[$cacheKey->getKey()][$cacheKey->getSubKey()]
               : null;
      } else {
         return isset(self::$cacheStore[$cacheKey->getKey()])
               ? self::$cacheStore[$cacheKey->getKey()]
               : null;
      }
   }

   public function clear(CacheKey $cacheKey = null) {
      unset(self::$cacheStore[$cacheKey->getKey()]);
   }

}
