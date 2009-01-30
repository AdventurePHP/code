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

   // setup the PHP environment
   error_reporting(E_ALL);
   ini_set('display_errors','1');
   ini_set('html_errors','off');

   // set the error handling function
   set_error_handler('errorHandler');

   // include necessary classes
   import('core::errorhandler','ErrorHandlerDefinition');

   // setup the registry with the default APF error handler
   $reg = &Singleton::getInstance('Registry');
   $reg->register('apf::core','ErrorHandler',new ErrorHandlerDefinition('core::errorhandler','DefaultErrorHandler'));


   /**
   *  @namespace core::errorhandler
   *  @function errorHandler
   *
   *  This function is the global APF error handler function. Calls the error manager configured in
   *  the registry.
   *
   *  @param string $errorNumber error number
   *  @param string $errorMessage error message
   *  @param string $errorFile error file
   *  @param string $errorLine error line
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 30.11.2005<br />
   *  Version 0.2, 04.12.2005<br />
   *  Version 0.3, 15.01.2005<br />
   *  Version 0.4, 21.01.2007 (Introduced the errorManager)<br />
   *  Version 0.5, 20.06.2008 (Errors, that are triggered while using the @ sign are not raised anymore)<br />
   *  Version 0.6, 30.01.2009 (Updated to the new error handler concept)<br />
   */
   function errorHandler($errorNumber,$errorMessage,$errorFile,$errorLine){

      // Don't raise error, if @ was applied
      if(error_reporting() == 0){
         return;
       // end if
      }

      // raise error and display error message
      $reg = &Singleton::getInstance('Registry');
      $errorHandlerDef = $reg->retrieve('apf::core','ErrorHandler');

      if($errorHandlerDef !== null && get_class($errorHandlerDef) === 'ErrorHandlerDefinition'){

         // get handler params
         $namespace = $errorHandlerDef->get('Namespace');
         $class = $errorHandlerDef->get('Class');

         // include error handler
         import($namespace,$class);

         // execute error handler
         $errHandler = new $class();

         if(is_subclass_of($errHandler,'AbstractErrorHandler') === true){
            $errHandler->handleError($errorNumber,$errorMessage,$errorFile,$errorLine);
          // end if
         }
         else{
            echo 'APF catchable error: '.$errorMessage.' (code: '.$errorNumber.') in '.$errorFile.' on line '.$errorLine.'!';
          // end else
         }

       // end if
      }
      else{
         echo 'APF catchable error: '.$errorMessage.' (code: '.$errorNumber.') in '.$errorFile.' on line '.$errorLine.'!';
       // end if
      }

    // end function
   }
?>