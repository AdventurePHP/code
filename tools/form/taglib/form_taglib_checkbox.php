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
   *  @class form_taglib_checkbox
   *
   *  Repr�sentiert ein Checkbox-Objekt (HTML-Form).<br />
   *
   *  @author Christian Sch�fer
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
      *  Implementiert die abstrakte Methode "onParseTime". Setzt das Attribut 'checked', falls Checkbox im<br />
      *  REQUEST-Array vorhanden ist.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      */
      function onParseTime(){

         // Auf 'selected' setzen, wenn Request-Offset vorhanden ist
         if(isset($_REQUEST[$this->__Attributes['name']])){
            $this->__Attributes['checked'] = 'checked';
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend".<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      */
      function onAfterAppend(){

         // Inhalt �bertragen
         $this->__presetValue();

         // Validierung durchf�hren
         $this->__validate();

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert ein Checkbox-Objekt.<br />
      *
      *  @return string $Checkbox; HTML-Code der Checkbox
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // Checkbox zur�ckgeben
         return '<input type="checkbox" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }

    // end class
   }
?>