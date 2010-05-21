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

   register_shutdown_function('flushAdvancedLogger');

   /**
    * Wrapper for flushing the advanced logger instances.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   function flushAdvancedLogger(){

      // get logger factory
      $aLF = &Singleton::getInstance('AdvancedLoggerFactory');

      // get registered logger
      $logger = Registry::retrieve('apf::core','AdvancedLogger');
      $count = count($logger);
      if($count > 0){

         for($i = 0; $i < $count; $i++){

            $log = &$aLF->getAdvancedLogger($logger[$i]['section']);
            $log->flushLogBuffer();
            unset($log);

          // end for
         }

       // end if
      }

    // end function
   }

   /**
    * @package core::logging
    * @class AdvancedLogEntry
    *
    * Implements a  logEntry object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.03.2007<br />
    */
   class AdvancedLogEntry extends APFObject {

      /**
      *  @private
      *  Date of the message.
      */
      private $__Date;

      /**
      *  @private
      *  Time of the message.
      */
      private $__Time;

      /**
      *  @private
      *  Message text.
      */
      private $__Message;

      /**
      *  @private
      *  Message type (aka severity).
      */
      private $__Type;

      /**
      *  @public
      *
      *  Constructor of the class. Creates a new logEntry object.
      *
      *  @param string $Message; Desrired error message
      *  @param string $Type; Error message type
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.03.2007<br />
      */
      function AdvancedLogEntry($message,$type){

         $this->__Date = date('Y-m-d');
         $this->__Time = date('H:i:s');
         $this->__Message = $message;
         $this->__Type = $type;

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
      *  Version 0.1, 09.11.2008<br />
      */
      function toString($timestamp = true,$type = true){

         $logString = (string)'';
         if($timestamp === true){
            $logString .= '['.$this->__Date.' '.$this->__Time.'] ';
          // end if
         }
         if($type === true){
            $logString .= '['.$this->__Type.'] ';
          // end if
         }
         return $logString.$this->__Message;

       // end function
      }

    // end class
   }

   /**
    * @package core::logging
    * @class AdvancedLoggerFactory
    *
    * Implements the factory for the AdvancedLogger. Must be created singleton using the
    * service manager!
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   class AdvancedLoggerFactory extends APFObject {

      /**
       * @private
       * The logger cache.
       */
      private $__Logger = array();

      public function AdvancedLoggerFactory(){
      }

      /**
      *  @public
      *
      *  Returns the logger for the desired config section. Caches all previously created logger.
      *
      *  @param string $section the section, the logger should be initialized with
      *  @return AdvancedLogger $logger the desired logger
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      function &getAdvancedLogger($section){

         // calculate logger cache key
         $loggerKey = md5($section);

         // create logger, if it does not exist
         if(!isset($this->__Logger[$loggerKey])){

            // create logger
            $this->__Logger[$loggerKey] = &$this->__getAndInitServiceObject('core::logging','AdvancedLogger',$section,'NORMAL');

            // register current instance in the registry so that the flush function can get the
            // instances from the service manager in correct service type configuration
            $logger = Registry::retrieve('apf::core','AdvancedLogger');
            if(count($logger) == 0){
               $logger = array();
             // end if
            }
            $logger[] = array(
                              'context' => $this->__Context,
                              'language' => $this->__Language,
                              'section' => $section
                             );
            Registry::register('apf::core','AdvancedLogger',$logger);

          // end if
         }

         // return advanced logger
         return $this->__Logger[$loggerKey];

       // end function
      }

    // end function
   }


   /**
    * @package core::logging
    * @class AdvancedLogger
    *
    * Implements an advanced logger for the adventure php framework. In contrast to the default
    * logger, this component must be configured for each usage. The advantage of the component is
    * that multiple targets, log formats and output targets can be choosen.
    * Usage:
    * <pre>
    *   $logFactory = &$this->__getServiceObject('core::logging','AdvancedLoggerFactory');
    *    $log = &$logFactory->getAdvancedLogger('<section_name>');
    * </pre>
    * Please note, that flushing the log buffer to stdout and file is much more faster. Here's an
    * benchmark example of the three possibilities:
    * <pre>flushLogBuffer_file      0.0026059151 s
    * flushLogBuffer_stdout    0.0001997948 s
    * flushLogBuffer_database  0.0228970051 s</pre>
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 09.11.2008<br />
    */
   class AdvancedLogger extends APFObject {

      /**
       * @private
       * The log buffer;
       */
      private $__LogBuffer = array();

      /**
       * @private
       * Contains the desired log configuration section.
       */
      private $__LogConfig = null;

      /**
       * @private
       * New line character used for the file and stdout target.
       */
      private $__CRLF = PHP_EOL;

      public function AdvancedLogger(){
      }

      /**
       * @public
       *
       * Implements the init() method used by the service manager to initialize the service object.
       *
       * @param string $initParam the desired configuration section
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 09.11.2008<br />
       */
      function init($initParam){

         // initialize the current log configuration section
         if($this->__LogConfig === null){

            // initialize config
            $config = &$this->__getConfiguration('core::logging','logconfig');
            $this->__LogConfig = $config->getSection($initParam);

            if($this->__LogConfig === null){
               $env = Registry::retrieve('apf::core','Environment');
               trigger_error('[AdvancedLogger::init()] The configuration section ("'.$initParam.'") cannot be loaded from the logging configuration file "'.$env.'_logconfig.ini" for namespace "core::logging" and context "'.$this->__Context.'"!',E_USER_ERROR);
               exit();
             // end if
            }

            // check for the target directive
            if(!isset($this->__LogConfig['LogTarget'])){
               trigger_error('[AdvancedLogger::init()] The configuration section ("'.$initParam.'") does not contain a "LogTarget" directive! Please check your configuration.',E_USER_ERROR);
               exit();
             // end if
            }

          // end if
         }

       // end function
      }

      /**
      *  @public
      *
      *  The logEntry() function let's you append a log message to the current AdvancedLogger instance.
      *  Configuration is done by the init() method.
      *
      *  @param string $message the log entry's message
      *  @param string $type the log entry's type (aka severity)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      function logEntry($message,$type = 'INFO'){
         $this->__LogBuffer[] = new AdvancedLogEntry($message,$type);
       // end function
      }

      /**
      *  @public
      *
      *  Flushes the log buffer. Must be called after each request.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      function flushLogBuffer(){

         switch($this->__LogConfig['LogTarget']){

            case 'file':
               $this->__flush2File();
               break;
            case 'database':
               $this->__flush2Database();
               break;
            case 'stdout':
               $this->__flush2Stdout();
               break;
            default:
               trigger_error('[AdvancedLogger::flushLogBuffer()] The choosen log target ("'.$this->__LogConfig['LogTarget'].'") is not implementend. Please take one out of "file", "database" and "stdout"!',E_USER_ERROR);
               break;

          // end switch
         }

       // end function
      }

      /**
      *  @private
      *
      *  Implements the log flushing for the database target.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      private function __flush2Database(){

         // read params from the configuration
         if(!isset($this->__LogConfig['LogDatabase'])){
            trigger_error('[AdvancedLogger::__flush2Database()] The configuration section does not contain a "LogDatabase" definition! Please check your configuration.');
            return;
          // end if
         }
         if(!isset($this->__LogConfig['LogTable'])){
            trigger_error('[AdvancedLogger::__flush2Database()] The configuration section does not contain a "LogTable" definition! Please check your configuration.');
            return;
          // end if
         }
         $logDatabase = $this->__LogConfig['LogDatabase'];
         $logTable = $this->__LogConfig['LogTable'];

         // create database connection
         $cM = &$this->__getServiceObject('core::database','ConnectionManager');
         $db = &$cM->getConnection($logDatabase);

         // flush log entries to the table
         foreach($this->__LogBuffer as $entry){

            $timestamp = $entry->get('Date').' '.$entry->get('Time');
            $insert = 'INSERT INTO `'.$logTable.'`
                       (`Timestamp`,`Type`,`Message`)
                       VALUES
                       (\''.$timestamp.'\',\''.$entry->get('Type').'\',\''.$entry->get('Message').'\');';
            $db->executeTextStatement($insert);

          // end foreach
         }

       // end function
      }

      /**
      *  @private
      *
      *  Implements the log flushing for the file target.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      private function __flush2File(){

         // read params from the configuration
         if(!isset($this->__LogConfig['LogDir'])){
            trigger_error('[AdvancedLogger::__flush2Database()] The configuration section does not contain a "LogDir" definition! Please check your configuration.');
            return;
          // end if
         }
         if(!isset($this->__LogConfig['LogFileName'])){
            trigger_error('[AdvancedLogger::__flush2Database()] The configuration section does not contain a "LogFileName" definition! Please check your configuration.');
            return;
          // end if
         }
         $logDir = $this->__LogConfig['LogDir'];
         $logFileName = date('Y_m_d').'_'.$this->__LogConfig['LogFileName'].'.log';

         // create folder, if it does not exist
         if(!is_dir($logDir)){
            trigger_error('[AdvancedLogger::__flush2File()] Given log directory "'.$logDir.'" does not exist! Please create it.');
            return;
          // end if
         }

         // flush buffer to file
         $fH = fopen($logDir.'/'.$logFileName,'a+');

         foreach($this->__LogBuffer as $entry){
            fwrite($fH,$this->__getLogEntryString($entry).$this->__CRLF);
          // end foreach
         }

         fclose($fH);

       // end function
      }

      /**
      *  @private
      *
      *  Implements the log flushing for the stdout target.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      private function __flush2Stdout(){

         foreach($this->__LogBuffer as $entry){
            echo $this->__getLogEntryString($entry).$this->__CRLF;
          // end foreach
         }

       // end function
      }

      /**
      *  @private
      *
      *  Generates the log entry string by a given AdvancedLogEntry object.
      *
      *  @param AdvancedLogEntry $entry the current log entry
      *  @return string $logString the corresponding log string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 09.11.2008<br />
      */
      private function __getLogEntryString($entry){

         // configure timestamp
         $timestamp = true;
         if(!isset($this->__LogConfig['LogTimestamp']) || $this->__LogConfig['LogTimestamp'] == 'false'){
            $timestamp = false;
          // end if
         }

         // configure type
         $type = true;
         if(!isset($this->__LogConfig['LogType']) || $this->__LogConfig['LogType'] == 'false'){
            $type = false;
          // end if
         }

         // return log string
         return $entry->toString($timestamp,$type);

       // end function
      }

    // end class
   }
?>