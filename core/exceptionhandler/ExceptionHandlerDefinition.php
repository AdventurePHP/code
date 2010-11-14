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

   /**
    * @final
    * @package core::errorhandler
    * @class ExceptionHandlerDefinition
    *
    * Represents the definition of an exception handler invoked by the framework.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.02.2009<br />
    */
   final class ExceptionHandlerDefinition {

      /**
       * @private
       * @var string Defines the namespace of the exception handler implementation.
       */
      private $namespace = null;

      /**
       * @private
       * @var string Indicates the class name of the exception handler implementation (=filename).
       */
      private $class = null;

      /**
       * @public
       *
       * Constructor of the exception handler description. Takes the namespace and the
       * class as an argument.
       *
       * @param string $namespace the namespace of the exception handler implementation
       * @param string $class the name of the class of the exception handler
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 21.02.20097<br />
       */
      public function __construct($namespace,$class){
         $this->namespace = $namespace;
         $this->class = $class;
      }

      /**
       * @public
       *
       * Returns the namespace of the exception handler implementation class.
       *
       * @return string The namespace of the exception handler implementation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getNamespace(){
         return $this->namespace;
      }

      /**
       * @public
       *
       * Returns the class name of the exception handler implementation class.
       *
       * @return string The class name of the exception handler implementation.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 20.02.2010<br />
       */
      public function getClass(){
         return $this->class;
      }

    // end class
   }
?>