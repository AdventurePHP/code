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
use APF\core\configuration\ConfigurationException;
use APF\core\database\config\StatementConfiguration;
use APF\core\logging\Logger;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;

/**
 * @package APF\core\database
 * @class AbstractDatabaseHandler
 * @abstract
 *
 * Defines the scheme of a database handler. Forms the base class for all database
 * abstraction layer classes.
 * <p/>
 * To initialize database connections using the DIServiceManager, you may use the
 * following service definition section:
 * <code>
 * [news-store-db]
 * servicetype = "SINGLETON"
 * class = "APF\core\database\MySQLiHandler"
 * setupmethod = "setup"
 *
 * conf.host.method = "setHost"
 * conf.host.value = "..."
 *
 * conf.port.method = "setPort"
 * conf.port.value = "..."
 *
 * conf.name.method = "setDatabaseName"
 * conf.name.value = "..."
 *
 * conf.user.method = "setUser"
 * conf.user.value = "..."
 *
 * conf.pass.method = "setPass"
 * conf.pass.value = "..."
 *
 * [conf.socket.method = "setSocket"
 * conf.socket.value = "..."]
 *
 * conf.charset.method = "setCharset"
 * conf.charset.value = "..."
 *
 * conf.collation.method = "setCollation"
 * conf.collation.value = "..."
 *
 * [conf.debug.method = "setDebug"
 * conf.debug.value = "..."]
 *
 * [conf.log-file-name.method = "setLogTarget"
 * conf.log-file-name.value = "..."]
 * </code>
 * This connection definition may be injected into another service using some kind of
 * service definition like this:
 * <code>
 * [GORM]
 * servicetype = "SINGLETON"
 * class = "APF\modules\genericormapper\data\GenericORRelationMapper"
 * setupmethod = "setup"
 * ...
 * init.db-conn.method = "setDatabaseConnection"
 * init.db-conn.namespace = "VENDOR\sample\namespace"
 * init.db-conn.name = "news-store-db"
 * </code>
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 10.02.2008<br />
 * Version 0.2, 07.08.2010 (Added *_FETCH_MODE constants and optional second fetchData() parameter)<br />
 */
abstract class AbstractDatabaseHandler extends APFObject implements DatabaseConnection {

   /**
    * @protected
    * @var boolean Indicates, whether the handler is already initialized or not.
    */
   protected $isInitialized = false;

   /**
    * @protected
    * @var string Database server.
    */
   protected $dbHost = null;

   /**
    * @protected
    * @var string Database user.
    */
   protected $dbUser = null;

   /**
    * @protected
    * @var string Password for the database.
    */
   protected $dbPass = null;

   /**
    * @protected
    * @var string Name of the database.
    */
   protected $dbName = null;

   /**
    * @protected
    * @var string Port for connection.
    */
   protected $dbPort = null;

   /**
    * @protected
    * @var string Socket for connection.
    */
   protected $dbSocket = null;

   /**
    * @protected
    * @var boolean Indicates, if the handler runs in debug mode. This means, that all
    * statements executed are written into a dedicated logfile.
    */
   protected $dbDebug = false;

   /**
    * @protected
    * @var resource Database connection resource.
    */
   protected $dbConn = null;

   /**
    * @protected
    * @var Logger Instance of the logger.
    */
   protected $dbLog = null;

   /**
    * @protected
    * @var string Name of the log target. Must be defined within the implementation class!
    */
   protected $dbLogTarget;

   /**
    * @protected
    * @var int Auto increment id of the last insert.
    */
   protected $lastInsertId;

   /**
    * @protected
    * @var string Indicates the charset of the database connection.
    *
    * For mysql databases, see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
    * for more details.
    */
   protected $dbCollation = null;

   /**
    * @protected
    * @var string Indicates the collation of the database connection.
    *
    * For mysql databases, see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
    * for more details.
    */
   protected $dbCharset = null;

   /**
    * @var array Default values for various statement execution methods.
    */
   protected $defaultPlaceholder = array(
         'executeStatement'     => self::PLACE_HOLDER_APF,
         'executeTextStatement' => self::PLACE_HOLDER_QUESTION_MARKS,
         'prepareStatement'     => self::PLACE_HOLDER_APF,
         'prepareTextStatement' => self::PLACE_HOLDER_QUESTION_MARKS
   );

   /**
    * @var bool
    */
   protected $emulate = false;

   /**
    * @public
    *
    * Defines the name of the log target for the debugging feature.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $logTarget The name of debug log file.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setLogTarget($logTarget) {
      $this->dbLogTarget = $logTarget;
   }

   /**
    * @public
    *
    * Implements the init() method, so that the derived classes can be initialized
    * by the service manager. Initializes the handler only one time.
    *
    * @param array $initParam Associative configuration array.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   public function init($initParam) {

      if ($this->isInitialized == false) {

         if (isset($initParam['Host'])) {
            $this->setHost($initParam['Host']);
         }

         if (isset($initParam['User'])) {
            $this->setUser($initParam['User']);
         }

         if (isset($initParam['Pass'])) {
            $this->setPass($initParam['Pass']);
         }

         $this->setDatabaseName($initParam['Name']);

         if (isset($initParam['Port'])) {
            $this->setPort($initParam['Port']);
         }

         if (isset($initParam['Socket'])) {
            $this->setSocket($initParam['Socket']);
         }

         if (isset($initParam['DebugMode'])) {
            $this->setDebug($initParam['DebugMode']);
         }

         if (isset($initParam['Charset'])) {
            $this->setCharset($initParam['Charset']);
         }
         if (isset($initParam['Collation'])) {
            $this->setCollation($initParam['Collation']);
         }

         $this->isInitialized = true;
         $this->setup();
      }
   }

   /**
    * @public
    *
    * Defines the database host to connect to.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $host The database host to connect to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setHost($host) {
      $this->dbHost = $host;
   }

   /**
    * @public
    *
    * Defines the user that is used to connect to the database.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $user The database user.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setUser($user) {
      $this->dbUser = $user;
   }

   /**
    * @public
    *
    * Defines the password used to connect to the database.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $pass The database password.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setPass($pass) {
      $this->dbPass = $pass;
   }

   /**
    * @public
    *
    * Defines the database name to connect to.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $name Th name of the database to connect to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setDatabaseName($name) {
      $this->dbName = $name;
   }

   /**
    * @public
    *
    * Defines the database port to connect to.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param int $port The database port to connect to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setPort($port) {
      $this->dbPort = $port;
   }

   /**
    * @public
    *
    * Defines the socket to connect to.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $socket The socket descriptor.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setSocket($socket) {
      $this->dbSocket = $socket;
   }

   /**
    * @public
    *
    * Enables (true) or disables (false) the internal debugging feature (=statement logging).
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param boolean $debug <em>True</em> in case the logging feature should be switched on, <em>false</em> otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setDebug($debug) {
      $this->dbDebug = ($debug == 'true' || $debug == '1') ? true : false;
   }

   /**
    * @public
    *
    * Defines the character set of the database connection.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $charset The desired character set.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setCharset($charset) {
      $this->dbCharset = $charset;
   }

   /**
    * @public
    *
    * Defines the collation of the database connection.
    * <p/>
    * Can be used for manual or DI configuration.
    *
    * @param string $collation The desired collation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setCollation($collation) {
      $this->dbCollation = $collation;
   }

   /**
    * @public
    *
    * Implements an initializer method to setup derived classes using the
    * DIServiceManager.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 07.05.2012<br />
    */
   public function setup() {
      $this->dbLog = & Singleton::getInstance('APF\core\logging\Logger');
      $this->connect();
   }

   /**
    * @protected
    * @abstract
    *
    * Provides internal service to open a database connection.
    *
    * @throws DatabaseHandlerException In case of connection issues.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   abstract protected function connect();

   /**
    * @param null $statementType
    *
    * @return array|string
    *
    * TODO streamline API to not return two different data types!
    */
   public function getDefaultPlaceholder($statementType = null) {
      if ($statementType === null) {
         return $this->defaultPlaceholder;
      } else {
         return $this->defaultPlaceholder[$statementType];
      }
   }

   /**
    * @param string $method Name of the method to set the default place holder type for.
    * @param int $placeHolderType The desired place holder type (see <em>DatabaseConnection::PLACE_HOLDER_*</em>).
    */
   public function setDefaultPlaceholder($method, $placeHolderType) {
      $this->defaultPlaceholder[$method] = $placeHolderType;
   }

   public function executeStatement($namespace, $statementName, array $params = array(), $logStatement = false, $emulatePrepare = null, $placeHolderType = null) {
      $statement = $this->getPreparedStatement($namespace, $statementName);
      if ($placeHolderType === null) {
         $placeHolderType = $this->defaultPlaceholder['executeStatement'];
      }

      // execute the statement with use of the current connection!
      return $this->executeTextStatement($statement, $params, $logStatement, $emulatePrepare, $placeHolderType);
   }

   /**
    * @protected
    *
    * Loads a statement file.
    *
    * @param string $namespace The namespace of the statement file.
    * @param string $name The name of the statement's file body (e.g. load_entries.sql).
    *
    * @return string The statement.
    * @throws DatabaseHandlerException In case the statement file cannot be loaded.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.02.2011<br />
    */
   protected function getPreparedStatement($namespace, $name) {
      try {
         $config = $this->getConfiguration($namespace, $name);
      } catch (ConfigurationException $e) {
         throw new DatabaseHandlerException($e->getMessage(), E_USER_ERROR, $e);
      }

      /* @var $config StatementConfiguration */

      return $config->getStatement();
   }

   public function executeTextStatement($statement, array $params = array(), $logStatement = false, $emulatePrepare = null, $placeHolderType = null) {
      if (empty($params)) {
         return $this->execute($statement, $logStatement);
      }
      if ($placeHolderType === null) {
         $placeHolderType = $this->defaultPlaceholder['executeTextStatement'];
      }

      $emulatePrepare = $emulatePrepare === null ? : $this->emulate;

      if ($emulatePrepare === false) {
         $prepare = $this->prepareTextStatement($statement, $logStatement, $placeHolderType);

         return $prepare->execute($params);
      }
      if ($placeHolderType === self::PLACE_HOLDER_QUESTION_MARKS) {
         $statement = $this->replaceQuestionMarks($statement, $params);
      } else {
         $statement = $this->replaceParams($this->splitStatement($statement, $placeHolderType), $params);
      }

      return $this->execute($statement, $logStatement);
   }

   /**
    * @param $statement
    * @param bool $logStatement
    *
    * @return mixed
    */
   abstract protected function execute($statement, $logStatement = false);

   public function prepareTextStatement($statement, $logStatement = false, $placeHolderType = null) {
      $params = array();
      if ($placeHolderType === null) {
         $placeHolderType = $this->defaultPlaceholder['prepareTextStatement'];
      }
      if ($placeHolderType !== self::PLACE_HOLDER_QUESTION_MARKS) {
         $statement = $this->replaceParamsWithPlaceholder($this->splitStatement($statement, $placeHolderType), $params);
      }

      return $this->prepare($statement, $params, $logStatement);
   }

   /**
    * @param string $statement The statement to split up.
    * @param int $placeholderStyle The desired place holder style.
    *
    * @return array The split statement.
    * @throws DatabaseHandlerException
    */
   protected function splitStatement($statement, $placeholderStyle) {
      switch ($placeholderStyle) {
         case self::PLACE_HOLDER_APF:
            $pregString = '/\[([A-Za-z0-9_]+)\]/u';
            break;
         case self::PLACE_HOLDER_PDO:
            $pregString = '/:([A-Za-z0-9_]+)/u';
            break;
         default:
            throw new DatabaseHandlerException('Wrong Parameter given');
      }
      return preg_split($pregString, $statement, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
   }

   /**
    * @param array $parts Parts of a statement returned from <em>splitStatement()</em>.
    * @param array $params The parameters for the statement.
    *
    * @return string Statement with question marks for prepare.
    * @throws DatabaseHandlerException If parameter is not given.
    */
   protected function replaceParamsWithPlaceholder(array $parts, &$params) {
      $statement = '';
      $params[] = null;
      foreach ($parts as $key => $value) {
         if ($key % 2 === 0) {
            $statement .= $value;
         } else {
            $statement .= '?';
            $params[] = $value;
         }
      }

      return $statement;
   }

   /**
    * @param $statement
    * @param array $params The parameters for the statement.
    * @param $logStatement
    *
    * @return mixed
    */
   protected abstract function prepare($statement, array $params, $logStatement);

   /**
    * @param string $statement
    * @param array $params The parameters for the statement.
    *
    * @return string
    */
   protected function replaceQuestionMarks($statement, array $params) {
      $parts = explode('?', $statement);
      $statement = '';
      foreach ($parts as $key => $value) {
         if ($key !== 0) {
            if ($params[$key - 1] === null) {
               $statement .= 'NULL';
            } else {
               $statement .= $this->quoteValue($params[$key - 1]);
            }
         }
         $statement .= $value;
      }

      return $statement;
   }

   /**
    * @param array $parts Parts of a statement returned from <em>splitStatement()</em>.
    * @param array $params the parameters for the Statement
    *
    * @return string The prepared statement.
    * @throws DatabaseHandlerException In case of unknown params.
    */
   protected function replaceParams(array $parts, array $params) {
      $statement = '';
      foreach ($parts as $key => $value) {
         if ($key % 2 === 0) {
            $statement .= $value;
         } elseif (isset($params[$value])) {
            if ($value === '__limit__') {
               $statement .= (int) $params[$value];
            } else {
               $statement .= $this->quoteValue($params[$value]);
            }
         } else {
            throw new DatabaseHandlerException('Unknown param "' . $value . '" for statement parts "' . implode(', ', $parts) . '"!');
         }
      }

      return $statement;
   }

   public function prepareStatement($namespace, $fileName, $logStatement = false, $placeHolderType = null) {
      return $this->prepareTextStatement($this->getPreparedStatement($namespace, $fileName), $logStatement, $placeHolderType);
   }

   /**
    * @protected
    * @abstract
    *
    * Provides internal service to close a database connection.
    *
    * @throws DatabaseHandlerException In case of connection issues.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   abstract protected function close();

   /**
    * @protected
    *
    * Configures the client connection's charset and collation.
    *
    * @see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 08.02.2010<br />
    */
   protected function initCharsetAndCollation() {
      if ($this->dbCharset !== null || $this->dbCollation !== null) {
         $setArray = array();
         if ($this->dbCharset !== null) {
            $setArray[] = ' NAMES \'' . $this->dbCharset . '\'';
         }
         if ($this->dbCollation !== null) {
            $setArray[] = ' collation_connection = \'' . $this->dbCollation . '\'';
            $setArray[] = ' collation_database = \'' . $this->dbCollation . '\'';
         }
         $statement = 'SET' . implode(',', $setArray);
         $this->executeTextStatement($statement);
      }
   }


   /**
    * @public
    *
    * @deprecated Use executeStatement() with fetchAll() instead.
    *
    * @param $namespace
    * @param $statementFile
    * @param array $params
    * @param bool $logStatement
    *
    * @return array
    */
   public function executeBindStatement($namespace, $statementFile, array $params = array(), $logStatement = false) {
      $result = $this->executeStatement($namespace, $statementFile, $params, $logStatement);

      return $result->fetchAll(Result::FETCH_ASSOC);
   }


   /**
    * @public
    *
    * @deprecated  Use executeTextStatement with fetchAll instead
    *
    * @param $statement
    * @param array $params
    * @param bool $logStatement
    *
    * @return array
    */
   public function executeTextBindStatement($statement, array $params = array(), $logStatement = false) {
      $result = $this->executeTextStatement($statement, $params, $logStatement);

      return $result->fetchAll();
   }

   /**
    * @public
    *
    * @deprecated Use Result->fetchData() instead.
    *
    * Fetches a record from the database.
    *
    * @param Result $result The result of the current statement.
    * @param int $type The type the returned data should have. Use the static FETCH_* constants.
    *
    * @return mixed The result array. Returns false if no row was found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.09.2009<br />
    * Version 0.2, 08.08.2010 (Added optional second parameter) <br />
    */
   public function fetchData(Result $result, $type = self::ASSOC_FETCH_MODE) {
      return $result->fetchData($type);
   }

   /**
    * @public
    *
    * @deprecated Use Result->getNumRows() instead.
    *
    * Returns the number of selected rows by a select Statement. Some databases do not support
    * this so you should not relied on this behavior for portable applications.
    *
    * @param Result $result The result of the current statement.
    *
    * @return int The number of selected rows.
    *
    * @author Tobias Lückel (megger)
    * @version
    * Version 0.1, 11.04.2012<br />
    */
   public function getNumRows(Result $result) {
      return $result->getNumRows();
   }

}
