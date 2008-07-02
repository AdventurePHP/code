<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_text
   *
   *  Repräsentiert ein Text-Feld-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   *  Version 0.2, 12.01.2007 (Umbenannt in "form_taglib_text")<br />
   */
   class form_taglib_text extends ui_element
   {

      function form_taglib_text(){
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
      *  Implementiert die abstrakte Methode "transform".<br />
      *
      *  @return string $TextField; HTML-Code des Text-Feldes
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // HTML-Tag zurückgeben
         return  '<input type="text" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }

    // end class
   }
?>