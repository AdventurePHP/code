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

   import('core::logging','Logger');

   /**
    * @package core::database
    * @class AbstractDatabaseHandler
    * @abstract
    *
    * Defines the scheme of a database handler. Forms the base class for all database
    * abstraction layer classes.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.02.2008<br />
    */
   abstract class AbstractDatabaseHandler extends APFObject {

      /**
       * @protected
       * @var boolean Indicates, whether the handler is already initialized or not.
       */
      protected $__isInitialized = false;

      /**
       * @protected
       * @var string Database server.
       */
      protected $__dbHost = null;

      /**
       * @protected
       * @var string Database user.
       */
      protected $__dbUser = null;

      /**
       * @protected
       * @var string Password for the database.
       */
      protected $__dbPass = null;

      /**
       * @protected
       * @var string Name of the database.
       */
      protected $__dbName = null;

      /**
       * @protected
       * @var boolean Indicates, if the handler runs in debug mode. This means, that all
       * statements executed are written into a dedecated logfile.
       */
      protected $__dbDebug = false;

      /**
       * @protected
       * @var resource Database connection resource.
       */
      protected $__dbConn = null;

      /**
       * @protected
       * @var Logger Instance of the logger.
       */
      protected $__dbLog = null;

      /**
       * @protected
       * @var string Name of the log file. Must be defined within the implementation class!
       */
      protected $__dbLogFileName;

      /**
       * @protected
       * @var int Auto increment id of the last insert.
       */
      protected $__lastInsertID;

      /**
       * @protected
       * @var string Indicates the charset of the database connnection.
       *
       * For mysql databases, see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
       * for more details.
       */
      protected $__dbCollation = null;

      /**
       * @protected
       * @var string Indicates the collation of the database connnection.
       * 
       * For mysql databases, see http://dev.mysql.com/doc/refman/5.0/en/charset-connection.html
       * for more details.
       */
      protected $__dbCharset = null;

      public function AbstractDatabaseHandler(){
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
      public function init($initParam){

         if($this->__isInitialized == false){

            // set server host
            if(isset($initParam['DB.Host'])){
               $this->__dbHost = $initParam['DB.Host'];
             // end if
            }

            // set user name
            if(isset($initParam['DB.User'])){
               $this->__dbUser = $initParam['DB.User'];
             // end if
            }

            // set password
            if(isset($initParam['DB.Pass'])){
               $this->__dbPass = $initParam['DB.Pass'];
             // end if
            }

            // set name of the database
            $this->__dbName = $initParam['DB.Name'];

            // set debug mode
            if(isset($initParam['DB.DebugMode']) && ($initParam['DB.DebugMode'] == 'true' || $initParam['DB.DebugMode'] == '1')){
               $this->__dbDebug = true;
            }

            // set connection charset and collation
            if(isset($initParam['DB.Charset'])){
               $charset = trim($initParam['DB.Charset']);
               if(!empty($charset)){
                  $this->__dbCharset = $charset;
                // end if
               }
             // end if
            }
            if(isset($initParam['DB.Collation'])){
               $collation = trim($initParam['DB.Collation']);
               if(!empty($collation)){
                  $this->__dbCollation = $collation;
                // end if
               }
             // end if
            }

            $this->__dbLog = &Singleton::getInstance('Logger');
            $this->__isInitialized = true;
            $this->__connect();

          // end if
         }

       // end function
      }

      /**
       * @protected
       * @abstract
       *
       * Provides internal service to open a database connection.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2008<br />
       */
      abstract protected function __connect();

      /**
       * @protected
       * @abstract
       *
       * Provides internal service to close a database connection.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2008<br />
       */
      abstract protected function __close();

      /**
       * @public
       * @abstract
       *
       * Executes a statement, located within a statement file. The place holders contained in the
       * file are replaced by the given values.
       *
       * @param string $namespace Namespace of the statement file.
       * @param string $statementName Name of the statement file (filebody!).
       * @param string[] $params A list of statement parameters.
       * @param bool $logStatement Indicates, if the statement is logged for debug purposes.
       * @return resource The database result resource.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2008<br />
       */
      abstract public function executeStatement($namespace,$statementName,$params = array(),$logStatement = false);

      /**
       * @public
       * @abstract
       *
       * Executes a statement applied as a string to the method and returns the
       * result pointer.
       *
       * @param string $statement The statement string.
       * @param boolean $logStatement Inidcates, whether the given statement should be
       *                              logged for debug purposes.
       * @return resource The database result resource.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.02.2008<br />
       */
      abstract public function executeTextStatement($statement,$logStatement = false);

      /**
       * @public
       *
       * Fetches a record from the database using the given result resource.
       *
       * @param resource $resultCursor The result resource returned by executeStatement() or executeTextStatement().
       * @return string[] The associative result array. Returns false if no row was found.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.09.2009<br />
       */
      abstract public function fetchData($resultCursor);

      /**
       * @public
       * @abstract
       *
       * Escapes given values to be SQL injection save.
       *
       * @param string $value The unescaped value.
       * @return string The escapted string.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 23.02.2008<br />
       */
      abstract public function escapeValue($value);

      /**
       * @public
       * @abstract
       *
       * Returns the amount of rows, that are affected by a previous update or delete call.
       *
       * @param resource $resultCursor The result resource pointer.
       * @return int The number of affected rows.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 24.02.2008<br />
       */
      abstract public function getAffectedRows($resultCursor);

      /**
       * @public
       *
       * Returns the last insert id generated by auto_increment or trigger.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 04.01.2006<br />
       */
      public function getLastID(){
         return $this->__lastInsertID;
       // end function
      }

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
      protected function initCharsetAndCollation(){

         if($this->__dbCharset !== null){
            $this->executeTextStatement('SET character_set_client = \''.$this->__dbCharset.'\'');
            $this->executeTextStatement('SET character_set_connection = \''.$this->__dbCharset.'\'');
            $this->executeTextStatement('SET character_set_results = \''.$this->__dbCharset.'\'');
         }

         if($this->__dbCollation !== null){
            $this->executeTextStatement('SET collation_connection = \''.$this->__dbCollation.'\'');
            $this->executeTextStatement('SET collation_database = \''.$this->__dbCollation.'\'');
         }
         
       // end function
      }

    // end class
   }
?>