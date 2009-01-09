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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   register_shutdown_function('flushLogger');


   /**
   *  @namespace core::logging
   *
   *  Wrapper for flushing the log buffer.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   function flushLogger(){
      $L = &Singleton::getInstance('Logger');
      $L->flushLogBuffer();
    // end function
   }


   /**
   *  @namespace core::logging
   *  @class logEntry
   *
   *  Implements a  logEntry object.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   class logEntry
   {

      /**
      *  @private
      *  Date of the message.
      */
      var $__Date;


      /**
      *  @private
      *  Time of the message.
      */
      var $__Time;


      /**
      *  @private
      *  Message text.
      */
      var $__Message;


      /**
      *  @private
      *  Message type.
      */
      var $__Type;


      /**
      *  @public
      *
      *  Construktor of the class. Creates a new logEntry object.
      *
      *  @param string $Message; Desrired error message
      *  @param string $Type; Error message type
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function logEntry($Message,$Type){

         $this->__Date = date('Y-m-d');
         $this->__Time = date('H:i:s');
         $this->__Message = $Message;
         $this->__Type = $Type;

       // end function
      }


      /**
      *  @public
      *
      *  Returns the message string used to write into a log file.<br />
      *
      *  @return string $Message; Complete error message including date and time
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function toString(){
         return '['.$this->__Date.' '.$this->__Time.'] ['.$this->__Type.'] '.$this->__Message;
       // end function
      }

    // end class
   }


   /**
   *  @namespace core::logging
   *  @class Logger
   *
   *  Implements a generic logger used in the framework's core components and your applications. The
   *  class must be initialized singleton! Flushing is done automatically ba shutdown function after
   *  a request.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.03.2007<br />
   */
   class Logger
   {

      /**
      *  @private
      *  Log entry store.
      */
      var $__LogEntries = array();


      /**
      *  @private
      *  Directory, where log files are stored.
      */
      var $__LogDir;


      /**
      *  @private
      *  Permission that is applied to a newly created log folder.
      */
      var $__LogFolderPermissions = 0777;


      /**
      *  @private
      *  Newline sign. Uses the PHP's standard newline sign if not configured in different way.
      */
      var $__CRLF = PHP_EOL;



      /**
      *  @public
      *
      *  Constructor of the Logger. Creates the LogDir if it does not exist.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      *  Version 0.2, 02.04.2007 (Fehler beim Anlegen des Log-Verzeichnisses behoben)<br />
      *  Version 0.3, 21.06.2008 (Replaced APPS__LOG_PATH with a value from the registry)<br />
      *  Version 0.4, 14.08.2008 (LogDir initialization was moved do the flushLogBuffer() method)<br />
      */
      function Logger(){

         $Reg = &Singleton::getInstance('Registry');
         $this->__LogDir = $Reg->retrieve('apf::core','LogDir');

       // end function
      }


      /**
      *  @public
      *
      *  Create a log entry.
      *
      *  @param string $LogFileName Name of the log file to log to
      *  @param string $Message Log message
      *  @param string $Type Desired type of the message
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function logEntry($LogFileName,$Message,$Type = 'INFO'){
         $this->__LogEntries[$LogFileName][] = new logEntry($Message,$Type);
       // end function
      }


      /**
      *  @public
      *
      *  Flushes the log buffer to the desired files.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      *  Version 0.2, 14.08.2008 (LogDir now is created during flush instead of during initialization)<br />
      */
      function flushLogBuffer(){

         // check, if buffer contains log entries
         if(count($this->__LogEntries) > 0){

            // check if lock dir exists
            if(!is_dir($this->__LogDir)){

               // try to create non existing log dir
               if(!mkdir($this->__LogDir,$this->__LogFolderPermissions)){
                  trigger_error('[Logger->Logger()] The log directory "'.$this->__LogDir.'" cannot be created du to permission restrictions! Please check config and specify the "LogDir" (namespace: "apf::core") parameter in the registry!');
                  exit();
                // end if
               }

             // end if
            }

            // flush entries to the filesystem
            foreach($this->__LogEntries as $LogFileName => $LogEntries){

               // generate complete log file name
               $LogFileName = $this->__getLogFileName($LogFileName);

               // generate complete log file pathe
               $LogFile = $this->__LogDir.'/'.$LogFileName;

               // flush entries to filesystem
               if(count($LogEntries) > 0){

                  // open file
                  $lFH = fopen($LogFile,'a+');

                  for($i = 0; $i < count($LogEntries); $i++){
                     fwrite($lFH,$LogEntries[$i]->toString().$this->__CRLF);
                   // end for
                  }

                  // close file!
                  fclose($lFH);

                // end if
               }

             // end foreach
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Returns the name of the log file by the body of the name. Each log file will be named like jjjj_mm_dd__{filename}.log.
      *
      *  @param string $FileName Name of the log file
      *  @return string $CompleteFileName Complete file name, that contains a date prefix and an file extension
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function __getLogFileName($FileName){
         return date('Y_m_d').'__'.str_replace('-','_',strtolower($FileName)).'.log';
       // end function
      }

    // end class
   }
?>