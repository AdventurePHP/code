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

   import('core::errorhandler','AbstractErrorHandler');

   /**
    * @package core::errorhandler
    * @class DefaultErrorHandler
    *
    * Implements the default error handler of the APF. Logs errors to a logfile and displays the
    * standard error page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 30.01.2009<br />
    */
   class DefaultErrorHandler extends AbstractErrorHandler {

      /**
       * @private
       * @var string Error number.
       */
      protected $errorNumber;

      /**
       * @private
       * @var string Error message,
       */
      protected $errorMessage;

      /**
       * @private
       * @var string Error file.
       */
      protected $errorFile;

      /**
       * @private
       * @var string Error line.
       */
      protected $errorLine;

      /**
       * @public
       *
       * Implements the error handling function, that is called by the APF error handling function.
       *
       * @param string $errorNumber error number
       * @param string $errorMessage error message
       * @param string $errorFile error file
       * @param string $errorLine error line
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.01.2007<br />
       */
      public function handleError($errorNumber,$errorMessage,$errorFile,$errorLine){

         // fill attributes
         $this->errorNumber = $errorNumber;
         $this->errorMessage = $errorMessage;
         $this->errorFile = $errorFile;
         $this->errorLine = $errorLine;

         // log error
         $this->__logError();

         // build nice error page
         echo $this->__buildErrorPage();

       // end function
      }

      /**
       * @private
       *
       * Creates a log entry containing the error occured.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 21.01.2007<br />
       * Version 0.2, 29.03.2007 (Changed to new logger)<br />
       */
      protected function __logError(){
         $message = '['.($this->__generateErrorID()).'] '.$this->errorMessage.' (Number: '.$this->errorNumber.', File: '.$this->errorFile.', Line: '.$this->errorLine.')';
         import('core::logging','Logger');
         $log = &Singleton::getInstance('Logger');
         $log->logEntry('php',$message,'ERROR');
      }

      /**
       * @private
       *
       * Creates the error page.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.01.2007<br />
       * Version 0.2, 03.03.2007<br />
       * Version 0.3, 04.03.2007 (Context now is set)<br />
       * Version 0.4, 29.03.2007<br />
       * Version 0.5, 13.08.2008 (Removed text only error page messages)<br />
       */
      protected function __buildErrorPage(){

         // at this point we have to re-include the benchmark timer, because PHP
         // sometimes forgets about this import and throws a
         // Fatal error: Exception thrown without a stack frame in Unknown on line 0
         // exception.
         import('core::benchmark','BenchmarkTimer');

         // create page
         $stacktrace = new Page('Stacktrace');
         $stacktrace->setContext('core::errorhandler');
         $stacktrace->loadDesign('core::errorhandler::templates','errorpage');

         // inject error information into the document attributes array
         $doc = &$stacktrace->getRootDocument();
         $doc->setAttribute('id',$this->__generateErrorID());
         $doc->setAttribute('message',$this->errorMessage);
         $doc->setAttribute('number',$this->errorNumber);
         $doc->setAttribute('file',$this->errorFile);
         $doc->setAttribute('line',$this->errorLine);

         // create error page
         return $stacktrace->transform();

       // end function
      }

      /**
       * @private
       *
       * Generates the error id.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 21.01.2007<br />
       */
      protected function __generateErrorID(){
         return md5($this->errorMessage.$this->errorNumber.$this->errorFile.$this->errorLine);
       // end function
      }

    // end class
   }
?>