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

   import('core::exceptionhandler','AbstractExceptionHandler');
   import('core::logging','Logger');

   /**
    * @package core::exceptionhandler
    * @class DefaultExceptionHandler
    *
    * Implements the default APF exception handler for uncaught exceptions.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   class DefaultExceptionHandler extends AbstractExceptionHandler {

      /**
       * @var int The number of the exception.
       */
      protected $__ExceptionNumber = null;

      /**
       * @var string The message of the exception.
       */
      protected $__ExceptionMessage = null;

      /**
       * @var string The file, the exception occures in.
       */
      protected $__ExceptionFile = null;

      /**
       * @var int The line, the exception occures in
       */
      protected $__ExceptionLine = null;

      /**
       * @var string The exception type (name of the class).
       */
      protected $__ExceptionType = null;

      /**
       * @var string[] The exception trace.
       */
      protected $__ExceptionTrace = array();

      public function DefaultExceptionHandler(){
      }

      /**
       * @public
       *
       * Implements the exception handling function, that is called by the APF exception handling
       * function.
       *
       * @param Exception $exception the thrown exception.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2009<br />
       */
      public function handleException($exception){

         // fill attributes
         $this->__ExceptionNumber = $exception->getCode();
         $this->__ExceptionMessage = $exception->getMessage();
         $this->__ExceptionFile = $exception->getFile();
         $this->__ExceptionLine = $exception->getLine();
         $this->__ExceptionTrace = $exception->getTrace();
         $this->__ExceptionType = get_class($exception);

         // log exception
         $this->logException();

         // build nice exception page
         echo $this->buildExceptionPage();

       // end function
      }

      /**
       * @private
       *
       * Creates a log entry containing the exception occured.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2009<br />
       */
      protected function logException(){
         $message = '['.($this->generateExceptionID()).'] '.$this->__ExceptionMessage.' (Number: '.$this->__ExceptionNumber.', File: '.$this->__ExceptionFile.', Line: '.$this->__ExceptionLine.')';
         $L = Singleton::getInstance('Logger');
         $L->logEntry('php',$message,'EXCEPTION');
       // end function
      }

      /**
       * @private
       *
       * Creates the exception page.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2009<br />
       */
      protected function buildExceptionPage(){

         // create page
         $stacktrace = new Page();
         $stacktrace->setContext('core::exceptionhandler');
         $stacktrace->loadDesign('core::exceptionhandler::templates','exceptionpage');

         // inject exception information into the document attributes array
         $doc = $stacktrace->getRootDocument();
         $doc->setAttribute('id',$this->generateExceptionID());
         $doc->setAttribute('message',$this->__ExceptionMessage);
         $doc->setAttribute('number',$this->__ExceptionNumber);
         $doc->setAttribute('file',$this->__ExceptionFile);
         $doc->setAttribute('line',$this->__ExceptionLine);
         $doc->setAttribute('trace',array_reverse($this->__ExceptionTrace));
         $doc->setAttribute('type',$this->__ExceptionType);

         // create exception page
         return $stacktrace->transform();

       // end function
      }

      /**
       * @private
       *
       * Generates the exception id.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.2009<br />
       */
      protected function generateExceptionID(){
         return md5($this->__ExceptionMessage.$this->__ExceptionNumber.$this->__ExceptionFile.$this->__ExceptionLine);
       // end function
      }

    // end class
   }
?>