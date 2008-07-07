<?php
   /**
   *  @package tools::form::taglib
   *  @class form_taglib_button
   *
   *  Repräsentiert ein Button-Feld-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   *  Version 0.2, 14.04.2007 (Methode "onAfterAppend" hinzugefügt)<br />
   */
   class form_taglib_button extends ui_element
   {

      function form_taglib_button(){
      }


      /**
      *  @public
      *  @since 0.2
      *
      *  Implementiert die abstrakte Methode "onAfterAppend". Zeigt dem Formular an, dass<br />
      *  der Button geklickt wurde.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.04.2007<br />
      */
      function onAfterAppend(){

         if(isset($this->__Attributes['name'])){

            if(isset($_REQUEST[$this->__Attributes['name']])){
               $this->__ParentObject->set('isSent',true);
             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform".<br />
      *
      *  @return string $Button; HTML-Code des Buttons
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function transform(){
         return '<input type="submit" '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).' />';
       // end function
      }

    // end class
   }
?>