<?php
   import('core::database','MySQLHandler');


   /**
   *  @package core::database
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

      function MySQLxHandler(){
      }


      /**
      *  @public
      *
      *  Initializes the MySQLxHandler.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.02.2008<br />
      *  Version 0.2, 16.11.2008 (Bugfix: debug mode was not activated correctly)<br />
      */
      function init($ConfigSection){

         // initialize if not already done
         if($this->__isInitialized == false){

            // fill connection data
            $this->__dbHost = $ConfigSection['DB.Host'];
            $this->__dbUser = $ConfigSection['DB.User'];
            $this->__dbPass = $ConfigSection['DB.Pass'];
            $this->__dbName = $ConfigSection['DB.Name'];

            // activate / deactivate debug mode
            if(isset($ConfigSection['DB.DebugMode'])){

               if($ConfigSection['DB.DebugMode'] == 'true' || $ConfigSection['DB.DebugMode'] == '1'){
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

            // create logger instance
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