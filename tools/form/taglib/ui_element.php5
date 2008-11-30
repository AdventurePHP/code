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

   import('tools::validator','myValidator');


   /**
   *  @namespace tools::form::taglib
   *  @class ui_element
   *  @abstract
   *
   *  Basis-Klasse eines UI-Elements.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   *  Version 0.2, 02.06.2007 ($__ExclusionArray hinzugefügt, __getAttributesAsString() nach coreObject verlagert)<br />
   */
   class ui_element extends Document
   {

      /**
      *  @private
      *  Definiert den CSS-Style, der bei Fehlschlagen der Validierung
      *  angezeigt wird.
      */
      var $__ValidatorStyle = 'border: 2px solid red;';


      /**
      *  @private
      *  Speichet, ob die Validierung aktiviert ist.
      */
      var $__ValidateObject = false;


      /**
      *  @private
      *  Speichet den Validator-Typen.
      */
      var $__Validator;


      /**
      *  @private
      *  Speichet den Namespace.
      */
      var $__Namespace = 'tools::form::taglib';


      /**
      *  @private
      *  @since 0.2
      *  Exclusion-Array für die Transformation in HTML.
      */
      var $__ExclusionArray = array('validate','validator','button');


      function ui_element(){
      }


      /**
      *  @public
      *
      *  Erweiterung der Funktionen des coreObjects bzgl. der Attribute.<br />
      *
      *  @param string $Name; Name des adressierten Attributes
      *  @param string $Value; Wert des Attributes, der zum Attribut hinzugefügt werden soll
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 09.01.2007<br />
      */
      function addAttribute($Name,$Value){

         if(isset($this->__Attributes[$Name])){
            $this->__Attributes[$Name] .= $Value;
          // end if
         }
         else{
            $this->__Attributes[$Name] = $Value;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Prefills the value of the current control.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 08.08.2008 (Fixed bug, that the number "0" was not automatically prefilled)<br />
      */
      function __presetValue(){

         if(!isset($this->__Attributes['value']) || empty($this->__Attributes['value'])){

            if(
               isset($_REQUEST[$this->__Attributes['name']])
               &&
               (
               !empty($_REQUEST[$this->__Attributes['name']])
               ||
               $_REQUEST[$this->__Attributes['name']] === '0'
               )
               ){
               $this->__Attributes['value'] = $_REQUEST[$this->__Attributes['name']];
             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Validiert den Wert des Objekts. Wird der Erwartung-Wert nicht erfüllt, so wird das Feld rot umrandet.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 13.01.2007 (Fehlermeldung bei nicht gesetztem 'button'-Attribut verbessert)<br />
      *  Version 0.3, 13.01.2007 (Prüfung, ob validiert werden soll, in '__setValidateObject' ausgelagert)<br />
      *  Version 0.4, 11.02.2007 (Form wird nun als nicht gültig gekennzeichnet, wenn ein Element nicht valide ist)<br />
      *  Version 0.5, 03.03.2007 (Bug in Fehlermeldung behoben)<br />
      */
      function __validate(){

         // Prüfen, ob eine Validierung notwendig ist
         $this->__setValidateObject();

         // Validierung durchführen
         if($this->__ValidateObject == true){

            // Attribut "value" setzen, falls nicht vorhanden
            if(!isset($this->__Attributes['value'])){
               $this->__Attributes['value'] = (string)'';
             // end if
            }

            // Validierung durchführen
            $ValidatorMethode = 'validate'.$this->__Validator;

            if(in_array($ValidatorMethode,get_class_methods('myValidator'))){

               if(!myValidator::$ValidatorMethode($this->__Attributes['value']) || !isset($_REQUEST[$this->__Attributes['name']])){

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
               trigger_error('['.get_class($this).'::__validate()] Validation method "'.$ValidatorMethode.'" is not supported in class "myValidator"! Please consult the API documentation for further details!');
             // end else
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Prüft, ob ein Objekt validiert werden soll.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.01.2007<br />
      *  Version 0.2, 13.01.2007 (Vor Aktivierung der Validierung wird nun abgefragt, ob der Button gedrückt wurde)<br />
      */
      function __setValidateObject(){

         // Validator auf false setzen
         $this->__ValidateObject = false;

         // Validator und Button finden
         if(isset($this->__Attributes['validate']) && (trim($this->__Attributes['validate']) == 'true' || trim($this->__Attributes['validate']) == '1')){

            // Standard-Validator setzen
            if(!isset($this->__Attributes['validator'])){
               $this->__Validator = 'Text';
             // end if
            }
            else{
               $this->__Validator = $this->__Attributes['validator'];
             // end else
            }

            // Button finden
            if(!isset($this->__Attributes['button']) || empty($this->__Attributes['button'])){
               trigger_error('['.get_class($this).'::__setValidateObject()] Validation not possible for form object "'.get_class($this).'" with name "'.$this->__Attributes['name'].'"! Button is not specified!');
               $Button = (string)'';
             // end if
            }
            else{
               $Button = $this->__Attributes['button'];
             // end else
            }

            // Validator auf true setzen, falls Button gedrückt
            if(isset($_REQUEST[$Button])){
               $this->__ValidateObject = true;
             // end if
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>