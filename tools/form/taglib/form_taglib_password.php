<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_password
   *
   *  Repräsentiert ein Passwort-Feld-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.01.2007<br />
   *  Version 0.2, 12.01.2007 (Umbenannt in "form_taglib_password")<br />
   */
   class form_taglib_password extends form_taglib_text
   {

      function form_taglib_password(){
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
      *  @return string $PasswordField; HTML-Code des Passwort-Felds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // HTML-Tag zurückgeben
         return  '<input type="password" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }

    // end class
   }
?>