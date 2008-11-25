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