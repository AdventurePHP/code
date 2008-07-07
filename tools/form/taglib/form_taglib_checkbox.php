<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_checkbox
   *
   *  Repräsentiert ein Checkbox-Objekt (HTML-Form).<br />
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
      *  Implementiert die abstrakte Methode "onParseTime". Setzt das Attribut 'checked', falls Checkbox im<br />
      *  REQUEST-Array vorhanden ist.<br />
      *
      *  @author Christian Schäfer
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
      *  @return string $Checkbox; HTML-Code der Checkbox
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // Checkbox zurückgeben
         return '<input type="checkbox" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }

    // end class
   }
?>