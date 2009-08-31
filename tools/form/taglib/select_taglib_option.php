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
   *  @class select_taglib_option
   *
   *  Represents a select option of an APF select field.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 07.01.2007<br />
   *  Version 0.2, 12.01.2007 (Renamed to "select_taglib_option")<br />
   */
   class select_taglib_option extends form_control {

      function select_taglib_option(){
      }

      /**
       * @protected
       *
       * Overwrites the __presetValue() methode, because here is nothing to do.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.08.2009<br />
       */
      protected function __presetValue(){
      }

      /**
       * @public
       *
       * Returns the HTML code of the option.
       *
       * @return string The HTML source code.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 07.01.2007<br />
       */
      function transform(){
         return '<option '.$this->__getAttributesAsString($this->__Attributes).'>'.$this->__Content.'</option>';
       // end function
      }

    // end class
   }
?>