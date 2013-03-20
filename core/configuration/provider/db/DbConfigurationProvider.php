<?php
namespace APF\core\configuration\provider\db;

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
use APF\core\configuration\provider\db\DbConfiguration;
use APF\core\database\AbstractDatabaseHandler;
use APF\core\database\ConnectionManager;
use APF\core\service\ServiceManager;

/**
 * @package core::configuration::provider::db
 * @class DbConfigurationProvider
 *
 * This provider implements the APF configuration scheme for database tables. It enables
 * you to store the configuration within a table that depends on the namespace of the
 * configuration. Please note, that this provider does not support configuration deep tree
 * structures like the XmlConfigurationProvider or IniConfigurationProvider do! This is limited
 * due to the flat database table structure. In case you need to have complex configuration
 * structures, please enhance this provider.
 * <p/>
 * To operate this provider, a table has to be created that follows the naming scheme
 * <code>config_{$namespace}</code> The table itself consists of these columns:
 * <ul>
 * <li>context</li>
 * <li>language</li>
 * <li>environment</li>
 * <li>name (a.k. configuration name</li>
 * <li>section</li>
 * <li>key</li>
 * <li>value</li>
 * <li>creationtimestamp (information only)</li>
 * <li>modificationtimestamp (information only)</li>
 * </ul>
 * Please refer to the documentation for details on creating the configuration store or
 * take the <code>setup.sql</code> file located in the <code>data</code> folder of the provider.
 * <p/>
 * Since release 1.14 this provider supports environment fallback as well.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.10.2010<br />
 */
class DbConfigurationProvider extends BaseConfigurationProvider implements ConfigurationProvider {

   /**
    * @var string $connectionName The name of the connection to create.
    */
   private $connectionName;

   /**
    * @var string The file extension registered with this provider.
    */
   protected $extension;

   /**
    * @param string $connectionName The name of the database connection to use.
    */
   public function __construct($connectionName) {
      $this->connectionName = $connectionName;
   }

   public function loadConfiguration($namespace, $context, $language, $environment, $name) {

      $table = 'config_' . $this->getTableNameSuffix($namespace);

      $conn = &$this->getConnection($context, $language);
      $select = 'SELECT `section`, `key`, `value` FROM `' . $table . '`
                           WHERE
                              `context` = \'' . $context . '\' AND
                              `language` = \'' . $language . '\' AND
                              `environment` = \'' . $environment . '\' AND
                              `name` = \'' . $this->getConfigName($name) . '\'';
      $result = $conn->executeTextStatement($select);

      $config = new DbConfiguration();

      // in case of empty results, try to fallback to the DEFAULT environment
      if ($conn->getNumRows($result) == 0 && $this->activateEnvironmentFallback === true && $environment !== 'DEFAULT') {
         $environment = 'DEFAULT';
         $select = 'SELECT `section`, `key`, `value` FROM `' . $table . '`
                           WHERE
                              `context` = \'' . $context . '\' AND
                              `language` = \'' . $language . '\' AND
                              `environment` = \'' . $environment . '\' AND
                              `name` = \'' . $this->getConfigName($name) . '\'';
         $result = $conn->executeTextStatement($select);
      }

      while ($data = $conn->fetchData($result)) {

         $section = $config->getSection($data['section']);
         if ($section === null) {
            $section = new DbConfiguration();
         }

         $section->setValue($data['key'], $data['value']);
         $config->setSection($data['section'], $section);
      }

      return $config;

   }

   public function saveConfiguration($namespace, $context, $language, $environment, $name, Configuration $config) {

      $table = 'config_' . $this->getTableNameSuffix($namespace);

      // resolve entries by section since we have a flat structure only
      $conn = &$this->getConnection($context, $language);
      $configName = $this->getConfigName($name);

      foreach ($config->getSectionNames() as $sectionName) {

         $section = $config->getSection($sectionName);

         foreach ($section->getValueNames() as $valueName) {
            $insert = 'INSERT INTO `' . $table . '`
                              SET
                                 `context` = \'' . $context . '\',
                                 `language` = \'' . $language . '\',
                                 `environment` = \'' . $environment . '\',
                                 `name` = \'' . $configName . '\',
                                 `section` = \'' . $sectionName . '\',
                                 `key` = \'' . $valueName . '\',
                                 `value` = \'' . $section->getValue($valueName) . '\'
                              ON DUPLICATE KEY UPDATE
                                 `value` = \'' . $section->getValue($valueName) . '\',
                                 `modificationtimestamp` = NOW()';
            $conn->executeTextStatement($insert);
         }
      }
   }

   /**
    * @private
    *
    * Creates the config table suffix. Since MySQL supports only a limited amount of
    * characters to be used as table name, we are sanitizing the given namespace.
    *
    * @param string $namespace The namespace the configuration is located in.
    * @return string The sanitized table name prefix.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.10.2010<br />
    */
   private function getTableNameSuffix($namespace) {
      return preg_replace('/([^a-z_]+)/i', '', strtolower(str_replace('::', '_', $namespace)));
   }

   /**
    * @private
    *
    * Removes the extension from the
    *
    * @param string $name The name of the configuration "file".
    * @return string The name of the configuration without the extension.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.10.2010<br />
    */
   private function getConfigName($name) {
      return str_replace('.' . $this->extension, '', $name);
   }

   /**
    * @private
    *
    * Creates the database connection.
    *
    * @param string $context The current context.
    * @param string $language The current language.
    * @return AbstractDatabaseHandler The database connection to read the configuration from and store it.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.10.2010<br />
    */
   private function &getConnection($context, $language) {
      // create service "manually", since we have no convenience method
      $connMgr = &ServiceManager::getServiceObject('core::database', 'ConnectionManager', $context, $language);
      /* @var $connMgr ConnectionManager */
      return $connMgr->getConnection($this->connectionName);
   }

   /**
    * Deletes the configuration specified by the given params.
    *
    * @param string $namespace The namespace of the configuration.
    * @param string $context The current application's context.
    * @param string $language The current application's language.
    * @param string $environment The environment, the applications runs on.
    * @param string $name The name of the configuration to delete including it's extension.
    * @throws ConfigurationException In case the row(s) cannot be deleted.
    *
    * @author Tobias Lückel
    * @version
    * Version 0.1, 27.10.2011<br />
    */
   public function deleteConfiguration($namespace, $context, $language, $environment, $name) {
      $table = 'config_' . $this->getTableNameSuffix($namespace);

      $conn = &$this->getConnection($context, $language);
      $textStatement = "DELETE FROM `" . $table . "`
                          WHERE
                            `context` = '" . $context . "',
                            `language` = '" . $language . "',
                            `environment` = '" . $environment . "',
                            `name` = '" . $this->getConfigName($name) . "'";
      $result = $conn->executeTextStatement($textStatement);

      $affectedRows = $conn->getAffectedRows($result);

      if ($affectedRows == 0 && $this->activateEnvironmentFallback === true && $environment !== 'DEFAULT') {
         $environment = 'DEFAULT';

         $textStatement = "DELETE FROM `" . $table . "`
                          WHERE
                            `context` = '" . $context . "',
                            `language` = '" . $language . "',
                            `environment` = '" . $environment . "',
                            `name` = '" . $this->getConfigName($name) . "'";
         $result = $conn->executeTextStatement($textStatement);

         $affectedRows = $conn->getAffectedRows($result);
      }

      if ($affectedRows == 0) {
         throw new ConfigurationException('[DbConfigurationProvider::deleteConfiguration()] '
               . 'Configuration with name "' . $this->getConfigName($name) . '" cannot be deleted! Please check your '
               . 'database configuration, the given parameters, or your environment configuration.');
      }
   }
}
