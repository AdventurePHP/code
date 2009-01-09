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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace tools::form::taglib
   *  @class form_taglib_radio
   *
   *  Repräsentiert ein Radio-Checkbox-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.01.2007<br />
   */
   class form_taglib_radio extends ui_element
   {

      function form_taglib_radio(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      */
      function onAfterAppend(){

         // Inhalt übertragen
         $this->__presetValue();

         // Validierung durchführen
         $this->__validate();

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert ein Checkbox-Objekt.<br />
      *
      *  @return string $Checkbox; HTML-Code der Radio-Checkbox
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // Checkbox zurückgeben
         return '<input type="radio" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die Methode "__presetValue" der Eltern-Klasse neu für den Radio-Button.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      */
      function __presetValue(){

         if(isset($_REQUEST[$this->__Attributes['name']]) && $_REQUEST[$this->__Attributes['name']] == $this->__Attributes['value']){
            $this->__Attributes['checked'] = 'checked';
          // end if
         }

       // end function
      }

    // end class
   }
?>