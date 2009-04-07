<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('core::logging','Logger');


   /**
   *  @namespace core::database
   *  @class AbstractDatabaseHandler
   *  @abstract
   *
   *  Defines the scheme of a database handler. Forms the base class for all
   *  database abstraction layer classes.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 10.02.2008<br />
   */
   abstract class AbstractDatabaseHandler extends coreObject
   {

      /**
      *  @protected
      *  Indicates, whether the handler is already initialized or not.
      */
      protected $__isInitialized = false;


      /**
      *  @protected
      *  Database server.
      */
      protected $__dbHost = null;


      /**
      *  @protected
      *  Database user.
      */
      protected $__dbUser = null;


      /**
      *  @protected
      *  Password for the database.
      */
      protected $__dbPass = null;


      /**
      *  @protected
      *  Name of the database.
      */
      protected $__dbName = null;


      /**
      *  @protected
      *  Indicates, if the handler runs in debug mode.
      */
      protected $__dbDebug = false;


      /**
      *  @protected
      *  Database connection resource.
      */
      protected $__dbConn = null;


      /**
      *  @protected
      *  Instance of the logger.
      */
      protected $__dbLog = null;


      /**
      *  @protected
      *  Name of the log file. Must be defined within the implementation class!
      */
      protected $__dbLogFileName;


      /**
      *  @protected
      *  Auto increment id of the last insert.
      */
      protected $__lastInsertID;


      function AbstractDatabaseHandler(){
      }


      /**
      *  @public
      *
      *  Implements the init() method, so that the derived classes can be initialized
      *  by the service manager. Initializes the handler only one time.
      *
      *  @param array $configSection Associative configuration array
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      function init($configSection){

         if($this->__isInitialized == false){

            // set server host
            if(isset($configSection['DB.Host'])){
               $this->__dbHost = $configSection['DB.Host'];
             // end if
            }

            // set user name
            if(isset($configSection['DB.User'])){
               $this->__dbUser = $configSection['DB.User'];
             // end if
            }

            // set password
            if(isset($configSection['DB.Pass'])){
               $this->__dbPass = $configSection['DB.Pass'];
             // end if
            }

            // set name of the database
            $this->__dbName = $configSection['DB.Name'];

            // set debug mode
            if(isset($configSection['DB.DebugMode'])){

               if($configSection['DB.DebugMode'] == 'true' || $configSection['DB.DebugMode'] == '1'){
                  $this->__dbDebug = true;
                // end if
               }
               else{
                  $this->__dbDebug = false;
                // end else
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
      *  @protected
      *  @abstract
      *
      *  Provides internal service to open a database connection.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      protected function __connect(){
      }


      /**
      *  @protected
      *  @abstract
      *
      *  Provides internal service to close a database connection.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      protected function __close(){
      }


      /**
      *  @public
      *  @abstract
      *
      *  Executes a statement, located within a statement file.
      *
      *  @param string $namespace Namespace of the statement file
      *  @param string $statementFile Name of the statement file (filebody!)
      *  @param array $params A list of statement parameters
      *  @param bool $showStatement Indicates, if the statement should be printed to screen
      *  @return resource The database result resource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      abstract function executeStatement($namespace,$statementFile,$params = array(),$showStatement = false);


      /**
      *  @public
      *  @abstract
      *
      *  Executes a statement applied as a string to the method and returns the
      *  result pointer.
      *
      *  @param string $statement The statement string
      *  @return resource The database result resource
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2008<br />
      */
      abstract function executeTextStatement($statement);


      /**
      *  @public
      *  @abstract
      *
      *  Escapes given values to be SQL injection save.
      *
      *  @param string $value The unescaped value
      *  @return string The escapted string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 23.02.2008<br />
      */
      abstract function escapeValue($value);


      /**
      *  @public
      *  @abstract
      *
      *  Returns the amount of rows, that are affected by a previous update or delete call.
      *
      *  @param resource $resultResource The result resource pointer
      *  @return int The number of affected rows
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 24.02.2008<br />
      */
      abstract function getAffectedRows($resultResource);

    // end class
   }
?>