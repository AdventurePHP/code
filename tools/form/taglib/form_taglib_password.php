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
   *  @namespace tools::form::taglib
   *  @class form_taglib_password
   *
   *  Represents a APF password field.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.01.2007<br />
   *  Version 0.2, 12.01.2007 (Umbenannt in "form_taglib_password")<br />
   */
   class form_taglib_password extends form_taglib_text
   {

      function form_taglib_password(){
      }


      /**
      *  @public
      *
      *  Executes presetting, validation and filtering.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      *  Version 0.2, 07.11.2008 (Added filtering)<br />
      */
      function onAfterAppend(){

         // Preset the content of the field
         $this->__presetValue();

         // Execute filter, if desired
         $this->__filter();

         // Execute validation
         $this->__validate();

       // end function
      }


      /**
      *  @public
      *
      *  Returns the HTML source code of the text field.
      *
      *  @return string $passwordField HTML code of the password field
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting and validation was moved to the onAfterAppend() method)<br />
      */
      function transform(){
         return  '<input type="password" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';
       // end function
      }

    // end class
   }
?>