<?php
namespace APF\core\database\config;

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
use APF\core\configuration\ConfigurationException;
use APF\core\configuration\ConfigurationProvider;
use APF\core\configuration\provider\BaseConfigurationProvider;
use APF\core\database\config\StatementConfiguration;

/**
 * @package APF\core\database\config
 * @class StatementConfigurationProvider
 *
 * Implements the configuration provider to handle stored statements to treat them as "normal"
 * configuration files (e.g. to support the omitContext feature).
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.02.2011
 */
class StatementConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   public function loadConfiguration($namespace, $context, $language, $environment, $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      if (file_exists($fileName)) {
         $config = new StatementConfiguration();
         $config->setStatement(file_get_contents($fileName));
         return $config;
      }

      if ($this->activateEnvironmentFallback && $environment !== 'DEFAULT') {
         return $this->loadConfiguration($namespace, $context, $language, 'DEFAULT', $name);
      }

      throw new ConfigurationException('[StatementConfigurationProvider::loadConfiguration()] '
            . 'Statement with namespace "' . $namespace . '", context "' . $context . '", '
            . ' language "' . $language . '", environment "' . $environment . '", and name '
            . '"' . $name . '" cannot be loaded!', E_USER_ERROR);
   }

   public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);

      // create file path if necessary to avoid "No such file or directory" errors
      $this->createFilePath($fileName);

      /* @var $config StatementConfiguration */
      if (file_put_contents($fileName, $config->getStatement()) === false) {
         throw new ConfigurationException('[StatementConfigurationProvider::saveConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be saved! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

   public function deleteConfiguration($namespace, $context, $language, $environment, $name) {

      $fileName = $this->getFilePath($namespace, $context, $language, $environment, $name);
      if (unlink($fileName) === false) {
         throw new ConfigurationException('[StatementConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $fileName . '" cannot be deleted! Please check your '
               . 'file system configuration, the file name, or your environment configuration.');
      }
   }

}
