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

   // set the exception handling function
   set_exception_handler('exceptionHandler');

   // include necessary classes
   import('core::exceptionhandler','ExceptionHandlerDefinition');

   // setup the registry with the default APF exception handler
   $reg = Singleton::getInstance('Registry');
   $reg->register('apf::core','ExceptionHandler',new ExceptionHandlerDefinition('core::exceptionhandler','DefaultExceptionHandler'));

   /**
    * @package core::exceptionhandler
    * @function exceptionHandler
    *
    * This function is the global APF exception handler function. Calls the exception manager
    * configured in the registry.
    *
    * @param Exception $exception the thrown exception
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   function exceptionHandler($exception){

      // raise error and display error message
      $reg = Singleton::getInstance('Registry');
      $exceptionHandlerDef = $reg->retrieve('apf::core','ExceptionHandler');

      if($exceptionHandlerDef !== null && get_class($exceptionHandlerDef) === 'ExceptionHandlerDefinition'){

         // get handler params
         $namespace = $exceptionHandlerDef->getNamespace();
         $class = $exceptionHandlerDef->getClass();

         // include exception handler
         import($namespace,$class);

         // execute exception handler
         $excHandler = new $class();

         if(is_subclass_of($excHandler,'AbstractExceptionHandler') === true){
            $excHandler->handleException($exception);
          // end if
         }
         else{
            echo 'APF catchable exception: '.$exception->getMessage().' (code: '.$exception->getCode().') in '.$exception->getFile().' on line '.$exception->getLine().'!';
          // end else
         }

       // end if
      }
      else{
         echo 'APF catchable exception: '.$exception->getMessage().' (code: '.$exception->getCode().') in '.$exception->getFile().' on line '.$exception->getLine().'!';
       // end if
      }

    // end function
   }
?>