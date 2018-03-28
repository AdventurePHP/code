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
namespace APF\core\configuration\provider\php;

use APF\core\configuration\Configuration;
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\BaseConfigurationProvider;

/**
 * Implements a configuration provider supporting PHP file based configurations. The following
 * features can be activated:
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
 * The configuration provider interprets array offsets as sections and sub-sections.
 * Key-value-couples are interpreted as keys and values within the section they are defined in.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 07.03.2015 (ID#243: added PHP-array file based configuration file support)<br />
 */
class PhpConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   public function loadConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      if (file_exists($fileName)) {

         /** @noinspection PhpIncludeInspection */
         $php = @include($fileName);

         if ($php === false) {
            throw new ConfigurationException('[PhpConfigurationProvider::loadConfiguration()] '
                  . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
                  . ' language "' . $language . '", environment "' . $environment . '", and name '
                  . '"' . $name . '" isn\'t well-formed (file name: ' . $fileName . ')!', E_USER_ERROR);
         }

         $config = new PhpConfiguration();

         foreach ($php as $key => $value) {
            if (is_array($value)) {
               $subSection = new PhpConfiguration();
               $this->parseSection($subSection, $value);
               $config->setSection($key, $subSection);
            } else {
               $config->setValue($key, $value);
            }
         }

         return $config;
      }

      if ($this->activateEnvironmentFallback && $environment !== 'DEFAULT') {
         return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
      }

      throw new ConfigurationException('[PhpConfigurationProvider::loadConfiguration()] '
            . 'Configuration with namespace "' . $namespace . '", context "' . $context . '", '
            . ' language "' . $language . '", environment "' . $environment . '", and name '
            . '"' . $name . '" cannot be loaded (file name: ' . $fileName . ')!', E_USER_ERROR);
   }

   /**
    * @param Configuration $config
    * @param array $section
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.03.2015<br />
    */
   private function parseSection(Configuration $config, array $section) {
      foreach ($section as $key => $value) {
         if (is_array($value)) {
            $subSection = new PhpConfiguration();
            $this->parseSection($subSection, $value);
            $config->setSection($key, $subSection);
         } else {
            $config->setValue($key, $value);
         }
      }
   }

   public function saveConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name, Configuration $config) {

      $php = '<?php' . PHP_EOL . 'return [' . PHP_EOL;

      foreach ($config->getSectionNames() as $sectionName) {
         $this->processSection($php, $sectionName, $config->getSection($sectionName), 3);
      }

      $this->processValues($php, $config, 3);

      // directly save file to gain performance and decrease memory usage
      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      // create file path if necessary to avoid "No such file or directory" errors
      $this->createFilePath($fileName);

      $php .= '];' . PHP_EOL;

      if (file_put_contents($fileName, $php) === false) {
         throw new ConfigurationException('[XmlConfigurationProvider::saveConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be saved! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }

   }

   protected function processValues(&$output, Configuration $config, $indention) {
      $valueNames = $config->getValueNames();
      $valuesCount = count($valueNames);
      $i = 1;
      foreach ($valueNames as $valueName) {
         $this->processValue($output, $valueName, $config->getValue($valueName), $indention, $valuesCount == $i);
         $i++;
      }
   }

   protected function processValue(&$output, $key, $value, $indention, $isLast) {
      $output .= str_repeat(' ', $indention) . '\'' . $key . '\' => \'' . $value . '\'' . ($isLast ? '' : ',') . PHP_EOL;
   }

   protected function processSection(&$output, $sectionName, Configuration $section, $indention) {
      $output .= str_repeat(' ', $indention) . '\'' . $sectionName . '\' => [' . PHP_EOL;

      foreach ($section->getSectionNames() as $sectionName) {
         $this->processSection($output, $sectionName, $section->getSection($sectionName), $indention + 3);
      }

      $this->processValues($output, $section, $indention + 3);

      $output .= str_repeat(' ', $indention) . '],' . PHP_EOL;
   }

   public function deleteConfiguration(string $namespace, string $context = null, string $language = null, string $environment = null, string $name) {
      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);
      if (unlink($fileName) === false) {
         throw new ConfigurationException('[PhpConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be deleted! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

}
