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
namespace APF\core\configuration\provider\json;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\BaseConfigurationProvider;

/**
 * Implements the configuration provider for the default APF json format. The
 * following features can be activated:
 * <ul>
 * <li>
 *    Disable context: in case $omitContext is set to true, the context will not be
 *    added to the configuration file path.
 * </li>
 * <li>
 *    Activate environment fallback: in case $activateEnvironmentFallback is set to true,
 *    the configuration provider first looks up the desired configuration file with the
 *    current environment prefix and falls back to DEFAULT environment. Having this feature
 *    activated you may only specify the configuration files, that are really depending on
 *    the environment.
 * </li>
 * <li>
 *    Disable environment: in case $omitEnvironment is set to true, the environment is not
 *    used as sub part of the file name.
 * </li>
 * </ul>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.11.2010<br />
 */
class JsonConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   public function loadConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      if (file_exists($fileName)) {
         return $this->mapStructure(file_get_contents($fileName));
      }

      if ($this->activateEnvironmentFallback && $environment !== 'DEFAULT') {
         return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
      }

      throw new ConfigurationException('[JsonConfigurationProvider::loadConfiguration()] '
            . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
            . ' language "' . $language . '", environment "' . $environment . '", and name '
            . '"' . $name . '" cannot be loaded (file name: ' . $fileName . ')!', E_USER_ERROR);
   }

   /**
    * Maps the content of a JSON configuration file into the APF configuration representation.
    *
    * @param string $fileContent The content of the configuration file.
    *
    * @return JsonConfiguration The configuration representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.11.2010<br />
    */
   private function mapStructure(string $fileContent) {

      $rawConfiguration = json_decode($fileContent, true);

      $config = new JsonConfiguration();

      foreach ($rawConfiguration as $name => $value) {
         if (is_array($value)) {
            $config->setSection($name, $this->mapSection($value));
         } else {
            $config->setValue($name, $value);
         }
      }

      return $config;
   }

   /**
    * Maps a section into the configuration representation.
    *
    * @param array $section The parsed JSON array representing a section.
    *
    * @return JsonConfiguration The configuration representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.11.2010<br />
    */
   private function mapSection(array $section) {

      $config = new JsonConfiguration();

      foreach ($section as $name => $value) {
         if (is_array($value)) {
            $config->setSection($name, $this->mapSection($value));
         } else {
            $config->setValue($name, $value);
         }
      }

      return $config;
   }

   public function saveConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name, Configuration $config) {
      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      // create file path if necessary to avoid "No such file or directory" errors
      $this->createFilePath($fileName);

      if (file_put_contents($fileName, $this->resolveStructure($config)) === false) {
         throw new ConfigurationException('[JsonConfigurationProvider::saveConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be saved! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

   /**
    * Creates a meta structure from the given configuration representation and returns the
    * json-formatted string to save.
    *
    * @param Configuration $config The config to resolve.
    *
    * @return array The meta structure of the given configuration representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.11.2010<br />
    */
   private function resolveStructure(Configuration $config) {

      $rawConfig = [];

      foreach ($config->getSectionNames() as $name) {
         $rawConfig[$name] = $this->resolveSection($config->getSection($name));
      }

      return json_encode($rawConfig);
   }

   /**
    * Resolves the configuration abstraction to the array meta format concerning one section.
    *
    * @param Configuration $config The config to resolve.
    *
    * @return array The meta structure of the given configuration representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.11.2010<br />
    */
   private function resolveSection(Configuration $config) {

      $rawConfig = [];

      foreach ($config->getValueNames() as $name) {
         $rawConfig[$name] = $config->getValue($name);
      }

      foreach ($config->getSectionNames() as $name) {
         $rawConfig[$name] = $this->resolveSection($config->getSection($name));
      }

      return $rawConfig;
   }

   public function deleteConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);
      if (unlink($fileName) === false) {
         throw new ConfigurationException('[JsonConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be deleted! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

}
