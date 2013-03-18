<?php
namespace APF\core\database;

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
use APF\core\configuration\ConfigurationManager;
use APF\core\pagecontroller\APFObject;

use APF\core\database\config\StatementConfigurationProvider;
use APF\core\registry\Registry;
use APF\core\service\APFService;

/**
 * @package core::database
 * @class ConnectionManager
 *
 * The ConnectionManager is a database connection fabric. You can use it to create APF style
 * database connections. To gain performance and to enable APF style configuration the manager
 * must be created as a service object.
 * <p/>
 * <pre>
 * $connMgr = &$this->getServiceObject('core::database', 'ConnectionManager');
 * $dBConn = &$connMgr->getConnection('{ConnectionKey}');
 * </pre>
 * The appropriate configuration file must reside under the <em>core::database</em> namespace
 * and the current application's context and environment. The content of the file is as follows:
 * <p/>
 * <pre>
 * [{ConnectionKey}]
 * DB.Host = ""
 * [DB.Port = ""]
 * DB.User = ""
 * DB.Pass = ""
 * DB.Name = ""
 * DB.Type = "MySQLx|SQLite|..."
 * [DB.DebugMode = "true|false"]
 * [DB.Charset = ""]
 * [DB.Collation = ""]
 * </pre>
 * <p/>
 * Further examples can be obtained in the <em>apf-configpack-*</em> release files.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 09.11.2007<br />
 * Version 0.2, 24.02.2008 (Existing connections are now cached)<br />
 */
final class ConnectionManager extends APFObject {

   /**
    * @private
    * @var AbstractDatabaseHandler[] Cache for existing database connections.
    */
   private $connections = array();
   private static $STATEMENT_FILE_EXTENSION = 'sql';

   /**
    * @public
    *
    * Initializes the configuration provider for file based statements.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2011<br />
    * Version 0.2, 04.02.2011 (Switched initialization to a more comfortable implementation)<br />
    */
   public function __construct() {
      $providers = ConfigurationManager::getRegisteredProviders();
      if (!in_array(self::$STATEMENT_FILE_EXTENSION, $providers)) {
         ConfigurationManager::registerProvider(self::$STATEMENT_FILE_EXTENSION, new StatementConfigurationProvider());
      }
   }

   /**
    * @public
    *
    * Returns the initialized handler for the desired connection key. Caches connections, that
    * were created previously.
    * <p/>
    * Using the database driver instance id, you can force the ConnectionManager to create
    * several connections with the same connection key. Please be careful with this feature,
    * since it may significantly decrease performance!
    *
    * @param string $connectionKey Desired configuration section.
    * @param string $instanceId The id of the database driver instance.
    * @return AbstractDatabaseHandler An instance of a connection layer implementation.
    * @throws \InvalidArgumentException In case of missing configuration.
    *
    * @author Christian Achatz, Tobias Lückel (megger)
    * @version
    * Version 0.1, 09.11.2007<br />
    * Version 0.2, 23.02.2008<br />
    * Version 0.3, 24.02.2008 (Introduced caching)<br />
    * Version 0.4, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
    * Version 0.5, 05.10.2008 (Bug-fix: usage of two or more identical connections (e.g. of type MySQLx) led to interferences. Thus, service object usage was changed)<br />
    * Version 0.6, 30.01.2009 (Added a check, that the old MySQLHandler cannot be used with the ConnectionManager. Doing so leads to bad connection interference!)<br />
    * Version 0.7, 22.03.2009 (Added the context to the error message, to ease debugging)<br />
    * Version 0.8, 20.09.2009 (Removed check for MySQLHandler usage, due to removal of the MySQLHandler)<br />
    * Version 0.9, 07.05.2012 (Introduced connection identifier to enable multiple connections for the same connection key)<br />
    */
   public function &getConnection($connectionKey, $instanceId = 'default') {

      // check, if connection was already created
      $cacheKey = $connectionKey . $instanceId;
      if (isset($this->connections[$cacheKey])) {
         return $this->connections[$cacheKey];
      }

      // read configuration
      $config = $this->getConfiguration('core::database', 'connections.ini');

      // get config section
      $section = $config->getSection($connectionKey);

      if ($section == null) {
         $env = Registry::retrieve('apf::core', 'Environment');
         throw new \InvalidArgumentException('[ConnectionManager::getConnection()] The given '
               . 'configuration section ("' . $connectionKey . '") does not exist in configuration file "'
               . $env . '_connections.ini" in namespace "core::database" for context "'
               . $this->context . '"!', E_USER_ERROR);
      }

      // re-map options to array to be able to initialize the database connection using the service manager
      $options = array();
      foreach ($section->getValueNames() as $key) {
         $options[$key] = $section->getValue($key);
      }

      // create the connection lazily
      $this->connections[$cacheKey] = $this->getAndInitServiceObject('core::database', $section->getValue('DB.Type') . 'Handler', $options, APFService::SERVICE_TYPE_NORMAL);
      return $this->connections[$cacheKey];
   }

   /**
    * @public
    *
    * Let's you clear/close a dedicated connection.
    * <p/>
    * Please be careful with this functionality since you may significantly decrease performance!
    *
    * @param string $connectionKey Desired configuration section.
    * @param string $instanceId The id of the database driver instance.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function closeConnection($connectionKey, $instanceId = 'default') {
      $cacheKey = $connectionKey . $instanceId;
      if (isset($this->connections[$cacheKey])) {
         unset($this->connections[$cacheKey]);
      }
   }

}
