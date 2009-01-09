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
   *  @class form_taglib_area
   *
   *  Repräsentiert ein Text-Area-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.01.2007<br />
   */
   class form_taglib_area extends ui_element
   {

      function form_taglib_area(){
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
      *  @return string $TextArea; HTML-Code der Text-Area
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // HTML-Tag zurückgeben
         return  '<textarea '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).'>'.$this->__Content.'</textarea>';

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die Methode "__presetValue" der Eltern-Klasse neu für die Text-Area.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      */
      function __presetValue(){

         if(isset($_REQUEST[$this->__Attributes['name']])){
            $this->__Content = $_REQUEST[$this->__Attributes['name']];
          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die Methode "__presetValue" der Eltern-Klasse neu für die Text-Area.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 05.05.2007 ("valid or not"-Report an Form ergänzt)<br />
      *  Version 0.3, 22.08.2007 (Fehler in Fehlermeldung korrigiert)<br />
      */
      function __validate(){

         // Prüfen, ob eine Validierung notwendig ist
         $this->__setValidateObject();


         // Validierung durchführen
         if($this->__ValidateObject == true){

            // Validierung durchführen
            $ValidatorMethode = 'validate'.$this->__Validator;

            if(in_array(strtolower($ValidatorMethode),get_class_methods('myValidator'))){

               if(!myValidator::$ValidatorMethode($this->__Content) || !isset($_REQUEST[$this->__Attributes['name']])){

                  // Style setzen
                  if(isset($this->__Attributes['style'])){
                     $this->__Attributes['style'] .= ' '.$this->__ValidatorStyle;
                   // end if
                  }
                  else{
                     $this->__Attributes['style'] = $this->__ValidatorStyle;
                   // end else
                  }

                  // Form als nicht valide kennzeichnen
                  $this->__ParentObject->set('isValid',false);

                // end if
               }

             // end if
            }
            else{
               trigger_error('['.get_class($this).'::__validate()] Validation methode "'.$ValidatorMethode.'" is not supported in class "myValidator"! Please consult the api documentation for further details!');
             // end else
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>