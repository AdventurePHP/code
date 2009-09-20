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

   /**
    * @namespace core::database
    * @class connectionManager
    *
    * The ConnectionManager is a database connection fabric. You can use it to create APF style
    * database connections. To gain performance and to enable APF style configuration the manager
    * must be created as a service object.
    * <p/>
    * <pre>
    * $connMgr = &$this->__getServiceObject('core::database','connectionManager');
    * $dBConn = &$connMgr->getConnection('{ConnectionKey}');
    * </pre>
    * The appropriate configuration file must reside under the <em>core::database</em> namespace
    * and the current application's context and environment. The content of the file is as follows:
    * <p/>
    * <pre>
    * [{ConnectionKey}]
    * DB.Host = ""
    * DB.User = ""
    * DB.Pass = ""
    * DB.Name = ""
    * DB.Type = "MySQLx|SQLite|..."
    * [DB.Charset = ""]
    * [DB.Collation = ""]
    * </pre>
    * <p/>
    * Furter examples can be obtained in the <em>apf-configpack-*</em> release files.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2007<br />
    * Version 0.2, 24.02.2008 (Existing connections are now cached)<br />
    */
   final class connectionManager extends coreObject {

      /**
       * @private
       * @var AbstractDatabaseHandler[] Cache for existing database connections.
       */
      private $__Connections = array();

      function connectionManager(){
      }

      /**
       * @public
       *
       * Returns the initialized handler for the desired connection key. Caches connections, that
       * were created previously.
       *
       * @param string $connectionKey desired configuration section
       * @return AbstractDatabaseHandler An instance of a connection layer implementation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.11.2007<br />
       * Version 0.2, 23.02.2008<br />
       * Version 0.3, 24.02.2008 (Introduced caching)<br />
       * Version 0.4, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
       * Version 0.5, 05.10.2008 (Bugfix: usage of two or more identical connections (e.g. of type MySQLx) led to interferences. Thus, service object usage was changed)<br />
       * Version 0.6, 30.01.2009 (Added a check, that the old MySQLHandler cannot be used with the ConnectionManager. Doing so leads to bad connection interference!)<br />
       * Version 0.7, 22.03.2009 (Added the context to the error message, to ease debugging)<br />
       * Version 0.8, 20.09.2009 (Removed check for MySQLHandler usage, due to removal of the MySQLHandler)<br />
       */
      function &getConnection($connectionKey){

         // check, if connection was already created
         $connectionHash = md5($connectionKey);

         if(isset($this->__Connections[$connectionHash])){
            return $this->__Connections[$connectionHash];
          // end if
         }

         // read configuration
         $config = &$this->__getConfiguration('core::database','connections');

         // get config section
         $section = $config->getSection($connectionKey);

         if($section == null){
            $reg = &Singleton::getInstance('Registry');
            $env = $reg->retrieve('apf::core','Environment');
            trigger_error('[connectionManager::getConnection()] The given configuration section ("'.$connectionKey.'") does not exist in configuration file "'.$env.'_connections.ini" in namespace "core::database" for context "'.$this->__Context.'"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // include the handler
         if(!class_exists($section['DB.Type'].'Handler')){
            import('core::database',$section['DB.Type'].'Handler');
          // end if
         }

         // create the handler
         $this->__Connections[$connectionHash] = &$this->__getAndInitServiceObject('core::database',$section['DB.Type'].'Handler',$section,'NORMAL');
         return $this->__Connections[$connectionHash];

       // end function
      }

    // end class
   }
?>