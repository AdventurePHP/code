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
    * Implements a base class for all text field validators.
    */
   abstract class SelectFieldValidator extends AbstractFormValidator {

      public function notify(){
         $this->__Control->markAsInvalid();
         $this->__Control->addAttribute('style','; background-color: red;');
         $this->__Control->notifyValidationListeners();
       // end function
      }

    // end function
   }
?>