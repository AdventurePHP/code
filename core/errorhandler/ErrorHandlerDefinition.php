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

   /**
   *  @namespace core::errorhandler
   *  @class ErrorHandlerDefinition
   *
   *  Represents the definition of an error handler invoked by the framework.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 30.01.2009<br />
   */
   final class ErrorHandlerDefinition extends coreObject
   {

      /**
      *  @protected
      *  Defines the namespace of the error handler implementation.
      */
      protected $__Namespace = null;


      /**
      *  @protected
      *  Indicates the class name of the error handler implementation (=filename).
      */
      protected $__Class = null;


      /**
      *  @public
      *
      *  Constructor of the error handler description. Taks the namespace and the class as an argument.
      *
      *  @param string $namespace the namespace of the error handler implementation
      *  @param string $class the name of the class of the error handler
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.12.2007<br />
      */
      function ErrorHandlerDefinition($namespace,$class){
         $this->__Namespace = $namespace;
         $this->__Class = $class;
       // end function
      }

    // end class
   }
?>