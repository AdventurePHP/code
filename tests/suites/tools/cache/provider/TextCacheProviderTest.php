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
use APF\tools\cache\key\AdvancedCacheKey;
use APF\tools\cache\key\SimpleCacheKey;
use APF\tools\cache\provider\TextCacheProvider;
use APF\tools\filesystem\Folder;
use PHPUnit\Framework\TestCase;

class TextCacheProviderTest extends TestCase {

   const CACHE_DIR = __DIR__ . '/test';

   protected function tearDown() {
      (new Folder())->open(self::CACHE_DIR)->delete();
   }

   public function testProviderWithSimpleCacheKey() {

      $provider = $this->getProvider();

      $key = new SimpleCacheKey('foo');

      $this->assertNull($provider->read($key));

      $provider->write($key, 'foo');
      $this->assertNotNull($provider->read($key));
      $this->assertEquals('foo', $provider->read($key));

      $provider->clear($key);

      $this->assertNull($provider->read($key));

   }

   public function testProviderWithAdvancedCacheKey() {

      $provider = $this->getProvider();

      $key = new AdvancedCacheKey('foo', 'bar');

      $this->assertNull($provider->read($key));

      $provider->write($key, 'foo');
      $this->assertNotNull($provider->read($key));
      $this->assertEquals('foo', $provider->read($key));

      $provider->clear($key);

      $this->assertNull($provider->read($key));

   }

   protected function getProvider() {
      $provider = new TextCacheProvider();

      $conf = new IniConfiguration();
      $conf->setValue('BaseFolder', self::CACHE_DIR);
      $conf->setValue('Namespace', 'APF\tests');

      $provider->setConfiguration($conf);

      return $provider;
   }

}
