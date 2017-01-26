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
namespace APF\core\configuration\provider\apc;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationManager;
use APF\core\configuration\ConfigurationProvider;

/**
 * Implements a configuration provider to store a configuration within an APC store.
 * This is done by using another configuration provider to read the persistent configuration
 * from.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.01.2013<br />
 */
class ApcConfigurationProvider implements ConfigurationProvider {

   /**
    * The file extension, the provider is registered with.
    *
    * @var string $extension
    */
   protected $extension;

   /**
    * The configuration provider to read the persistent configuration from.
    *
    * @var ConfigurationProvider $persistenceProviderExtension
    */
   private $persistenceProviderExtension;

   /**
    * Expires time in seconds. This is the time, the config is refreshed from the persistent file.
    *
    * @var int $expireTime
    */
   private $expireTime = 3600;

   /**
    * Initializes the memcached configuration provider.
    *
    * @param string $persistenceProviderExtension The name of the extension of the provider to use to load the persistent config with.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.01.2013<br />
    */
   public function __construct($persistenceProviderExtension) {
      $this->persistenceProviderExtension = $persistenceProviderExtension;
   }

   /**
    * @return int The entry expiring time in seconds.
    */
   public function getExpireTime() {
      return $this->expireTime;
   }

   /**
    * @param int $expireTime The expiring time in seconds.
    */
   public function setExpireTime($expireTime) {
      $this->expireTime = $expireTime;
   }

   /**
    * Remaps the configuration file name to the extension of the persistent configuration
    * file to be able to load and store the physical file.
    *
    * @param string $name The given in-memory configuration file name.
    *
    * @return string The remapped configuration file name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.10.2010<br />
    */
   protected function remapConfigurationName($name) {
      return str_replace('.' . $this->extension, '.' . $this->persistenceProviderExtension, $name);
   }

   protected function getStoreIdentifier($namespace, $context, $language, $environment, $name) {
      return md5($namespace . $context . $language . $environment . $name);
   }

   public function loadConfiguration($namespace, $context, $language, $environment, $name) {

      $name = $this->remapConfigurationName($name);

      // try to get the configuration from the APC store first if not available, read
      // persistent configuration and store it.
      $key = $this->getStoreIdentifier($namespace, $context, $language, $environment, $name);

      $config = $this->fetch($key);

      if ($config === false) {
         $config = ConfigurationManager::loadConfiguration($namespace, $context, $language, $environment, $name);
         $this->store($key, $config);
      }

      return $config;
   }

   public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {

      $name = $this->remapConfigurationName($name);

      // saving the configuration always includes saving in both the
      // persistent file and the APC store!
      $key = $this->getStoreIdentifier($namespace, $context, $language, $environment, $name);
      $this->store($key, $config);
      ConfigurationManager::saveConfiguration($namespace, $context, $language, $environment, $name, $config);
   }

   public function setExtension($extension) {
      $this->extension = $extension;
   }

   public function deleteConfiguration($namespace, $context, $language, $environment, $name) {

      $name = $this->remapConfigurationName($name);

      $key = $this->getStoreIdentifier($namespace, $context, $language, $environment, $name);
      $result = apcu_delete($key);

      if ($result === false) {
         throw new ConfigurationException('[ApcConfigurationProvider::deleteConfiguration()] '
               . 'ApcConfiguration with key "' . $key . '" cannot be deleted! Please check your '
               . 'APC configuration, the given parameters, or your environment configuration.');
      }

      ConfigurationManager::deleteConfiguration($namespace, $context, $language, $environment, $name);
   }

   /**
    * @param string $key Cache key.
    * @return mixed Cache content.
    */
   protected function fetch($key) {
      return apcu_fetch($key);
   }

   /**
    * @param string $key Cache key.
    * @param mixed $config Cache content.
    * @return array|bool
    */
   protected function store($key, $config) {
      return apcu_store($key, $config, $this->expireTime);
   }

}
