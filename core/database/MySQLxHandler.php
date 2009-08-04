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

   import('core::database','MySQLHandler');


   /**
   *  @namespace core::database
   *  @class MySQLxHandler
   *
   *  Compatibility class to be able to use the MySQLHandler with the connectionManager.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.02.2008<br />
   */
   class MySQLxHandler extends MySQLHandler
   {

      public function MySQLxHandler(){
      }


      /**
       * @public
       *
       * Initializes the MySQLxHandler.
       *
       * @param $configSection The content of the database configuration.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.02.2008<br />
       * Version 0.2, 16.11.2008 (Bugfix: debug mode was not activated correctly)<br />
       * Version 0.3, 15.07.2009 (Added collation support)<br />
       */
      public function init($initParam){

         // initialize if not already done
         if($this->__isInitialized == false){

            // fill connection data
            $this->__dbHost = $initParam['DB.Host'];
            $this->__dbUser = $initParam['DB.User'];
            $this->__dbPass = $initParam['DB.Pass'];
            $this->__dbName = $initParam['DB.Name'];

            // activate / deactivate debug mode
            if(isset($initParam['DB.DebugMode'])){

               if($initParam['DB.DebugMode'] == 'true' || $initParam['DB.DebugMode'] == '1'){
                  $this->__dbDebug = true;
                // end if
               }
               else{
                  $this->__dbDebug = false;
                // end else
               }

             // end if
            }
            else{
               $this->__dbDebug = false;
             // end else
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

            // refer to the logger instance
            $this->__dbLog = &Singleton::getInstance('Logger');

            // mark as initialized
            $this->__isInitialized = true;

            // create connection
            $this->__connect();

          // end if
         }

       // end function
      }

    // end class
   }
?>