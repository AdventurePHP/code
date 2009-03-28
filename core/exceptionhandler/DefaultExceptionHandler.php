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

   import('core::exceptionhandler','AbstractExceptionHandler');
   import('core::logging','Logger');


   /**
   *  @namespace core::exceptionhandler
   *  @class DefaultExceptionHandler
   *
   *  Implements the default APF exception handler for uncaught exceptions.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 21.02.2009<br />
   */
   class DefaultExceptionHandler extends AbstractExceptionHandler
   {

      /**
      *  The number of the exception.
      */
      protected $__ExceptionNumber = null;

      /**
      *  The message of the exception.
      */
      protected $__ExceptionMessage = null;

      /**
      *  The file, the exception occures in.
      */
      protected $__ExceptionFile = null;

      /**
      *  The line, the exception occures in
      */
      protected $__ExceptionLine = null;

      /**
      *  The exception trace.
      */
      protected $__ExceptionTrace = array();


      public function DefaultExceptionHandler(){
      }


      /**
      *  @public
      *
      *  Implements the exception handling function, that is called by the APF exception handling
      *  function.
      *
      *  @param Exception $exception the thrown exception
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.02.2009<br />
      */
      public function handleException($exception){

         // fill attributes
         $this->__ExceptionNumber = $exception->getCode();
         $this->__ExceptionMessage = $exception->getMessage();
         $this->__ExceptionFile = $exception->getFile();
         $this->__ExceptionLine = $exception->getLine();
         $this->__ExceptionTrace = $exception->getTrace();

         // log exception
         $this->__logException();

         // build nice exception page
         echo $this->__buildExeptionPage();

       // end function
      }


      /**
      *  @private
      *
      *  Creates a log entry containing the exception occured.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.02.2009<br />
      */
      protected function __logException(){
         $message = '['.($this->__generateExceptionID()).'] '.$this->__ExceptionMessage.' (Number: '.$this->__ExceptionNumber.', File: '.$this->__ExceptionFile.', Line: '.$this->__ExceptionLine.')';
         $L = Singleton::getInstance('Logger');
         $L->logEntry('php',$message,'EXCEPTION');
       // end function
      }


      /**
      *  @private
      *
      *  Creates the exception page.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.02.2009<br />
      */
      protected function __buildExeptionPage(){

         // create page
         $stacktrace = new Page('Stacktrace');
         $stacktrace->set('Context','core::exceptionhandler');
         $stacktrace->loadDesign('core::exceptionhandler::templates','exceptionpage');

         // inject exception information into the document attributes array
         $doc = $stacktrace->get('Document');
         $doc->setAttribute('id',$this->__generateExceptionID());
         $doc->setAttribute('message',$this->__ExceptionMessage);
         $doc->setAttribute('number',$this->__ExceptionNumber);
         $doc->setAttribute('file',$this->__ExceptionFile);
         $doc->setAttribute('line',$this->__ExceptionLine);
         $doc->setAttribute('trace',array_reverse($this->__ExceptionTrace));

         // create exception page
         return $stacktrace->transform();

       // end function
      }


      /**
      *  @private
      *
      *  Generates the exception id.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.02.2009<br />
      */
      protected function __generateExceptionID(){
         return md5($this->__ExceptionMessage.$this->__ExceptionNumber.$this->__ExceptionFile.$this->__ExceptionLine);
       // end function
      }

    // end class
   }
?>