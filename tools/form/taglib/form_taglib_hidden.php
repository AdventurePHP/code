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
    * @package tools::form::taglib
    * @class form_taglib_hidden
    *
    * Represents a HTML hidden field within the APF form tags.
    *
    * @author Christian Sch�fer
    * @version
    * Version 0.1, 05.01.2007<br />
    */
   class form_taglib_hidden extends form_control {

      public function form_taglib_hidden(){
      }

      /**
       * @public
       *
       * Returns the HTML code of the hidden field.
       *
       * @return string The HTML code of the hidden field.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 05.01.2007<br />
       */
      public function transform(){
         return '<input type="hidden" '.$this->__getAttributesAsString($this->__Attributes).' />';
       // end function
      }

    // end class
   }
?>