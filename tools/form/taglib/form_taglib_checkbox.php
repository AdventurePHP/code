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
   *  @class form_taglib_checkbox
   *
   *  Represents an APF form checkbox.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.01.2007<br />
   */
   class form_taglib_checkbox extends ui_element
   {

      function form_taglib_checkbox(){
      }


      /**
      *  @public
      *
      *  Sets the checked attribute, if the checkbox name exists in the request.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      */
      function onParseTime(){
         if(isset($_REQUEST[$this->__Attributes['name']])){
            $this->__Attributes['checked'] = 'checked';
          // end if
         }
       // end function
      }


      /**
      *  @public
      *
      *  Executes presetting and validation.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      */
      function onAfterAppend(){

         // Preset the content of the field
         $this->__presetValue();

         // Execute validation
         $this->__validate();

       // end function
      }


      /**
      *  @public
      *
      *  Returns the HTML code of the checkbox.
      *
      *  @return string $Checkbox the HTML code of the checkbox
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Moved presetting and validation to the onAfterAppend() method)<br />
      */
      function transform(){
         return '<input type="checkbox" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';
       // end function
      }

    // end class
   }
?>