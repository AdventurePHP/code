<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_file
   *
   *  Repräsentiert ein Datei-Feld-Objekt (HTML-Form).<br />
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
      *  Implementiert die abstrakte Methode "onAfterAppend". Setzt das Attribute 'enctype' des Formulars.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function onAfterAppend(){

         // Sobald ein File-Feld in der Form enthalten ist muss das
         // Attribut 'enctype' in der Form (ParentObject) gesetzt werden
         $this->__ParentObject->setAttribute('enctype','multipart/form-data');

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
      *  @return string $FileField; HTML-Code des Datei-Felds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // HTML-Tag zurückgeben
         return  '<input type="file" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';

       // end function
      }

    // end class
   }
?>