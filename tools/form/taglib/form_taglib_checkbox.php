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
    * @class form_taglib_checkbox
    *
    * Represents an APF form checkbox.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 13.01.2007<br />
    * Version 0.2, 12.02.2010 (Introduced attribute black and white listing)<br />
    */
   class form_taglib_checkbox extends form_control {

      public function __construct() {
         $this->attributeWhiteList[] = 'name';
         $this->attributeWhiteList[] = 'accesskey';
         $this->attributeWhiteList[] = 'disabled';
         $this->attributeWhiteList[] = 'tabindex';
         $this->attributeWhiteList[] = 'value';
         $this->attributeWhiteList[] = 'checked';
      }

      /**
       * @public
       *
       * Sets the checked attribute, if the checkbox name exists in the request.
       *
       * @author Christian Sch�fer
       * @version
       * Version 0.1, 13.01.2007<br />
       * Version 0.2, 28.08.2009 (Moved presetting to this method)<br />
       */
      public function onParseTime(){

         // check the box, desired
         $name = $this->getAttribute('name');
         if(isset($_REQUEST[$name])){
            $this->check();
          // end if
         }

         // preset the content of the field
         $this->__presetValue();

       // end function
      }

      /**
       * @public
       *
       * Returns the HTML code of the checkbox.
       *
       * @return string The HTML code of the checkbox.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 13.01.2007<br />
       * Version 0.2, 11.02.2007 (Moved presetting and validation to the onAfterAppend() method)<br />
       */
      public function transform(){
         return '<input type="checkbox" '.$this->getSanitizedAttributesAsString($this->__Attributes).' />';
      }

    // end class
   }
?>