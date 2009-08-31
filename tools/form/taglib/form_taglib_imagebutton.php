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
    * @namespace tools::form::taglib
    * @class form_taglib_imagebutton
    *
    * Represents an image button.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 27.09.2009<br />
    */
   class form_taglib_imagebutton extends form_control {

      /**
       * @public
       * 
       * Overwrites the onParseTime() method, because here is nothing to do.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function onParseTime(){
      }

      /**
       * @public
       *
       * Generates the HTML code of the image button.
       *
       * @return string Image button html.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 27.09.2009<br />
       */
      public function transform(){
         return '<input type="image" '
            .$this->__getAttributesAsString($this->__Attributes)
            .' />';
       // end function
      }
      
    // end class
   }
?>