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
   *  @class AbstractErrorHandler
   *
   *  Defines the interface, that a dedicated error handler must implement.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 30.01.2009<br />
   */
   class AbstractErrorHandler extends coreObject
   {

      function AbstractErrorHandler(){
      }


      /**
      *  @public
      *
      *  This method is called by the global APF error handler function.
      *
      *  @param string $errorNumber numeric error code
      *  @param string $errorMessage message of the error
      *  @param string $errorFile file in which the error occures
      *  @param string $errorLine line where the error occures
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 30.01.2009<br />
      */
      function handleError($errorNumber,$errorMessage,$errorFile,$errorLine){
      }

    // end class
   }
?>