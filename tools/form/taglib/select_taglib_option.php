<?php
   /**
   *  @package tools::form::taglib
   *  @class select_taglib_option
   *
   *  Repräsentiert die Option eines Select-Feld-Objekts (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 07.01.2007<br />
   *  Version 0.2, 12.01.2007 (Umbenannt in "select_taglib_option")<br />
   */
   class select_taglib_option extends ui_element
   {

      function select_taglib_option(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform".<br />
      *
      *  @return string $SelectOption; HTML-Tag SelectOption
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function transform(){
         return  '<option '.$this->__getAttributesAsString($this->__Attributes).'>'.$this->__Content.'</option>';
       // end function
      }

    // end class
   }
?>