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
namespace APF\tests\suites\tools\cache\provider;

use APF\core\configuration\provider\ini\IniConfiguration;
use APF\tools\cache\CacheKey;
use APF\tools\cache\key\AdvancedCacheKey;
use APF\tools\cache\key\SimpleCacheKey;
use APF\tools\cache\provider\ApcCacheProvider;

/**
 * Tests all capabilities of the ApcCacheProvider.
 */
class ApcCacheProviderTest extends \PHPUnit_Framework_TestCase {

   const CACHE_NAMESPACE = 'EXAMPLE\namespace';

   /**
    * @var string $namespace Cache namespace
    * @return ApcCacheProvider
    */
   protected function getProvider($namespace) {

      $config = new IniConfiguration();
      $config->setValue('Provider', 'APF\tools\cache\provider\ApcCacheProvider');
      $config->setValue('Active', 'true');
      $config->setValue('ExpireTime', '3600');
      $config->setValue('Namespace', $namespace);

      return (new ApcCacheProvider())->setConfiguration($config);
   }

   protected function getCacheIdentifier(CacheKey $key) {
      return self::CACHE_NAMESPACE . ApcCacheProvider::CACHE_KEY_DELIMITER . $key->getKey();
   }

   public function testRead() {

      $key = new SimpleCacheKey('foo');
      $cacheIdentifier = $this->getCacheIdentifier($key);
      $provider = $this->getProvider(self::CACHE_NAMESPACE);

      // assume null response in case of non-existing cache entry
      $this->assertNull($provider->read($key));

      // synthetically store content and read back (*invalid* content)
      apcu_store($cacheIdentifier, '§$%&/(');
      $this->assertNull($provider->read($key));

      // synthetically store content and read back (*valid* content)
      $entry = 'test';
      apcu_store($cacheIdentifier, serialize($entry));
      $this->assertEquals($entry, $provider->read($key));

   }

   public function testReadWrite() {

      $key = new SimpleCacheKey('bar');

      $config = new IniConfiguration();
      $config->setValue('Section.Value', 'foo');

      $provider = $this->getProvider(self::CACHE_NAMESPACE);

      $this->assertNull($provider->read($key));

      $provider->write($key, $config);

      $this->assertEquals($config, $provider->read($key));
      $this->assertEquals('foo', $provider->read($key)->getValue('Section.Value'));

   }

   public function testClear() {

      // ensure clean APC cache to ease expectation setup
      apcu_clear_cache();

      // build up mix of simple and complex cache keys for different namespaces
      // - ensure no interference between different namespaces
      // - ensure no interference between items within the same namespace
      // - ensure no interference between simple keys and complex keys
      $providerOne = $this->getProvider(self::CACHE_NAMESPACE);
      $providerTwo = $this->getProvider('OTHER\namespace');

      $keyOne = new SimpleCacheKey('foo');
      $keyTwo = new AdvancedCacheKey('foo', 'bar'); // main cache key intentionally the same

      $value = 'test';
      $providerOne->write($keyOne, $value);
      $providerOne->write($keyTwo, $value);

      $providerTwo->write($keyOne, $value);
      $providerTwo->write($keyTwo, $value);

      // check whether cache contains expected amount of entries
      $this->assertNotNull($providerOne->read($keyOne));
      $this->assertNotNull($providerOne->read($keyTwo));
      $this->assertNotNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      // step-wise remove entries and check...
      $providerOne->clear($keyOne);
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNotNull($providerOne->read($keyTwo));
      $this->assertNotNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      $providerOne->clear($keyTwo);
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNull($providerOne->read($keyTwo));
      $this->assertNotNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      $providerTwo->clear($keyOne);
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNull($providerOne->read($keyTwo));
      $this->assertNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      $providerTwo->clear($keyTwo);
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNull($providerOne->read($keyTwo));
      $this->assertNull($providerTwo->read($keyOne));
      $this->assertNull($providerTwo->read($keyTwo));

      // re-build cache entry and test namespace clearing
      $providerOne->write($keyOne, $value);
      $providerOne->write($keyTwo, $value);

      $providerTwo->write($keyOne, $value);
      $providerTwo->write($keyTwo, $value);

      $this->assertNotNull($providerOne->read($keyOne));
      $this->assertNotNull($providerOne->read($keyTwo));
      $this->assertNotNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      // remove all entries for one namespace but leave the other
      $providerOne->clear();
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNull($providerOne->read($keyTwo));
      $this->assertNotNull($providerTwo->read($keyOne));
      $this->assertNotNull($providerTwo->read($keyTwo));

      // remove second namespace and check for empty cache
      $providerTwo->clear();
      $this->assertNull($providerOne->read($keyOne));
      $this->assertNull($providerOne->read($keyTwo));
      $this->assertNull($providerTwo->read($keyOne));
      $this->assertNull($providerTwo->read($keyTwo));

      // ensure clean APC cache for other tests
      apcu_clear_cache();

   }

}
