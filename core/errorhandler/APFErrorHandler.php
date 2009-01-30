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

   import('core::errorhandler','AbstractErrorHandler');
   import('core::logging','Logger');


   /**
   *  @namespace core::errorhandler
   *  @class APFErrorHandler
   *
   *  Implements the default error handler of the APF. Logs errors to a logfile and displays the
   *  standard error page.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 30.01.2009<br />
   */
   class APFErrorHandler extends AbstractErrorHandler
   {

      /**
      *  @private
      *  Error number.
      */
      var $__ErrorNumber;

      /**
      *  @private
      *  Error message,
      */
      var $__ErrorMessage;

      /**
      *  @private
      *  Error file.
      */
      var $__ErrorFile;

      /**
      *  @private
      *  Error line.
      */
      var $__ErrorLine;


      function APFErrorHandler(){
      }


      /**
      *  @public
      *
      *  Implements the error handling function, that is called by the APF error handling function.
      *
      *  @param string $errorNumber error number
      *  @param string $errorMessage error message
      *  @param string $errorFile error file
      *  @param string $errorLine error line
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function handleError($errorNumber,$errorMessage,$errorFile,$errorLine){

         // fill attributes
         $this->__ErrorNumber = $errorNumber;
         $this->__ErrorMessage = $errorMessage;
         $this->__ErrorFile = $errorFile;
         $this->__ErrorLine = $errorLine;

         // log error
         $this->__logError();

         // build nice error page
         echo $this->__buildErrorPage();

       // end function
      }


      /**
      *  @private
      *
      *  Creates a log entry containing the error occured.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      *  Version 0.2, 29.03.2007 (Changed to new logger)<br />
      */
      function __logError(){
         $message = '['.($this->__generateErrorID()).'] '.$this->__ErrorMessage.' (Number: '.$this->__ErrorNumber.', File: '.$this->__ErrorFile.', Line: '.$this->__ErrorLine.')';
         $L = &Singleton::getInstance('Logger');
         $L->logEntry('php',$message,'ERROR');
       // end function
      }


      /**
      *  @private
      *
      *  Creates the error page.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 21.01.2007<br />
      *  Version 0.2, 03.03.2007<br />
      *  Version 0.3, 04.03.2007 (Context now is set)<br />
      *  Version 0.4, 29.03.2007<br />
      *  Version 0.5, 13.08.2008 (Removed text only error page messages)<br />
      */
      function __buildErrorPage(){

         // create page
         $stacktrace = new Page('Stacktrace');
         $stacktrace->set('Context','core::errorhandler');
         $stacktrace->loadDesign('core::errorhandler::templates','errorpage');

         // inject error information into the document attributes array
         $doc = & $stacktrace->getByReference('Document');
         $doc->setAttribute('id',$this->__generateErrorID());
         $doc->setAttribute('message',$this->__ErrorMessage);
         $doc->setAttribute('number',$this->__ErrorNumber);
         $doc->setAttribute('file',$this->__ErrorFile);
         $doc->setAttribute('line',$this->__ErrorLine);

         // create error page
         return $stacktrace->transform();

       // end function
      }


      /**
      *  @private
      *
      *  Generates the error id.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function __generateErrorID(){
         return md5($this->__ErrorMessage.$this->__ErrorNumber.$this->__ErrorFile.$this->__ErrorLine);
       // end function
      }

    // end class
   }
?>