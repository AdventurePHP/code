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

   import('tools::form::validator','AbstractFormValidator');

   /**
    * @namespace tools::form::validator
    * @class SelectFieldValidator
    *
    * Implements a base class for all select field validators.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.08.2009<br />
    */
   abstract class SelectFieldValidator extends AbstractFormValidator {

      /**
       * @public
       * 
       * Re-implements the notify() method, due to the fact, that select fields
       * have to be handled differently.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      public function notify(){
         $this->__Control->markAsInvalid();
         $this->__Control->addAttribute('style','; background-color: red;');
         $this->__Control->notifyValidationListeners();
       // end function
      }

    // end function
   }
?>