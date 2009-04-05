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

   /**
   *  @namespace core::database
   *  @class connectionManager
   *
   *  The ConnectionManager is a database connection fabric. To gain performance and to enable
   *  APF style configuration the manager must be created as a service object. Example:
   *  <pre>$connMgr = &$this->__getServiceObject('core::database','connectionManager');
   *  $dBConn = &$connMgr->getConnection('&lt;ConnectionKey&gt;');</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 09.11.2007<br />
   *  Version 0.2, 24.02.2008 (Existing connections are cached now)<br />
   */
   final class connectionManager extends coreObject
   {

      /**
      *  @private
      *  Cache for existing database connections.
      */
      private $__Connections = array();


      function connectionManager(){
      }


      /**
      *  @public
      *
      *  Returns the initialized handler for the desired connection key. Caches connections, that
      *  were created previously.
      *
      *  @param string $connectionKey desired configuration section
      *  @return AbstractDatabaseHandler $databaseHandler instance of a connection layer
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2007<br />
      *  Version 0.2, 23.02.2008<br />
      *  Version 0.3, 24.02.2008 (Introduced caching)<br />
      *  Version 0.4, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      *  Version 0.5, 05.10.2008 (Bugfix: usage of two or more identical connections (e.g. of type MySQLx) led to interferences. Thus, service object usage was changed)<br />
      *  Version 0.6, 30.01.2009 (Added a check, that the old MySQLHandler cannot be used with the ConnectionManager. Doing so leads to bad connection interference!)<br />
      *  Version 0.7, 22.03.2009 (Added the context to the error message, to ease debugging)<br />
      */
      function &getConnection($connectionKey){

         // check, if connection was already created
         $ConnectionHash = md5($connectionKey);

         if(isset($this->__Connections[$ConnectionHash])){
            return $this->__Connections[$ConnectionHash];
          // end if
         }

         // read configuration
         $Config = &$this->__getConfiguration('core::database','connections');

         // get config section
         $Section = $Config->getSection($connectionKey);

         if($Section == null){
            $Reg = &Singleton::getInstance('Registry');
            $Environment = $Reg->retrieve('apf::core','Environment');
            trigger_error('[connectionManager::getConnection()] The given configuration section ("'.$connectionKey.'") does not exist in configuration file "'.$Environment.'_connections.ini" in namespace "core::database" for context "'.$this->__Context.'"!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // check, if the handler was "MySQL", because this class is not allowed
         if(strtolower($Section['DB.Type']) === 'mysql'){
            trigger_error('[connectionManager::getConnection()] The connection type may not be equal to "MySQL". Please check the connection configuration at the "'.$connectionKey.'" section!',E_USER_ERROR);
            exit(1);
          // end if
         }

         // include the handler
         if(!class_exists($Section['DB.Type'].'Handler')){
            import('core::database',$Section['DB.Type'].'Handler');
          // end if
         }

         // create the handler
         $this->__Connections[$ConnectionHash] = &$this->__getAndInitServiceObject('core::database',$Section['DB.Type'].'Handler',$Section,'NORMAL');

         // return the handler
         return $this->__Connections[$ConnectionHash];

       // end function
      }

    // end class
   }
?>