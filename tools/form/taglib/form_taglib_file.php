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
   *  @class form_taglib_file
   *
   *  Represents the APF form file field.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.01.2007<br />
   */
   class form_taglib_file extends form_taglib_text
   {

      function form_taglib_file(){
      }


      /**
      *  @public
      *
      *  Executes the presetting and validation. Adds the "enctype" to the form.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Moved presetting and Validierung to onAfterAppend())<br />
      */
      function onAfterAppend(){

         // Add the "enctype" attribute to the parent object, so that the
         // developer must not care about that!
         $this->__ParentObject->setAttribute('enctype','multipart/form-data');

         // Preset the content of the field
         $this->__presetValue();

         // Execute validation
         $this->__validate();

       // end function
      }


      /**
      *  @public
      *
      *  Returns the HTML code of the file selection field.
      *
      *  @return string $FileField HTML code of the file field
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Moved presetting and Validierung to onAfterAppend())<br />
      */
      function transform(){
         return  '<input type="file" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';
       // end function
      }

    // end class
   }
?>