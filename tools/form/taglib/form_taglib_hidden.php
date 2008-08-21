<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_hidden
   *
   *  Repräsentiert ein Hidden-Form-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   */
   class form_taglib_hidden extends ui_element
   {

      function form_taglib_hidden(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform".<br />
      *
      *  @return string $HiddenField; HTML-Code des Hidden-Felds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function transform(){
         return '<input type="hidden" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';
       // end function
      }

    // end class
   }
?>