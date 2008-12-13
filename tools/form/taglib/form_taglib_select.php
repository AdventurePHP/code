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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::form::taglib','select_taglib_option');


   /**
   *  @namespace tools::form::taglib
   *  @class form_taglib_select
   *
   *  Represents an APF select field.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 07.01.2007<br />
   *  Version 0.2, 12.01.2007 (Renamed to "form_taglib_select")<br />
   */
   class form_taglib_select extends ui_element
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert die Tag-Lib eines Select-Feldes.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 03.03.2007 ("&" vor "new" entfernt)<br />
      */
      function form_taglib_select(){
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','option');
         $this->__ValidatorStyle = 'background-color: red;';
       // end function
      }


      /**
      *  @public
      *
      *  Extrahiert die Option-Tags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
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
      *  Setzte den Status einer mit $DisplayNameOrValue deklarierten Select-Feld-Option auf 'selected'.<br />
      *  Der Parameter kann sowohl ein Wert einer Option als auch ein Anzeige-Name sein.<br />
      *
      *  @param string $DisplayNameOrValue; Wert oder Anzeige-Name einer Option.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 18.11.2007 (Fehlermeldung korrigiert und Möglichkeit eingeräumt per Value oder DisplayName selected zu setzen)<br />
      */
      function setOption2Selected($DisplayNameOrValue){

         // Option-Counter initialisieren
         $OptionCount = 0;

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if(trim($Child->get('Content')) == $DisplayNameOrValue || $Child->getAttribute('value') == $DisplayNameOrValue){
                  $this->__Children[$ObjectID]->setAttribute('selected','selected');
                  $OptionCount++;
                // end if
               }

             // end foreach
            }

          // end if
         }

         // Warnung ausgeben, falls kein Element gefunden wurde
         if($OptionCount < 1){
            trigger_error('[form_taglib_select::setOption2Selected()] No option with name or value "'.$DisplayNameOrValue.'" found in select field "'.$this->__Attributes['name'].'" in form "'.$this->__ParentObject->getAttribute('name').'"!');
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Fügt zum Select-Feld eine Option hinzu.<br />
      *
      *  @param string $DisplayName; Dieplay-Name der Option
      *  @param string $Value; Wert der Option
      *  @param bool $PreSelected; true | false (optional)
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 07.06.2008 (ObjectID der eingefügten Option wird nun gesetzt)<br />
      */
      function addOption($DisplayName,$Value,$PreSelected = false){

         // Objekt-ID generieren
         $ObjectID = xmlParser::generateUniqID();


         // Neues Options-Objekt erzeugen
         $this->__Children[$ObjectID] = new select_taglib_option();


         // Attribute setzen
         $this->__Children[$ObjectID]->set('ObjectID',$ObjectID);
         $this->__Children[$ObjectID]->set('Content',$DisplayName);
         $this->__Children[$ObjectID]->setAttribute('value',$Value);

         if($PreSelected == true){
            $this->__Children[$ObjectID]->setAttribute('selected','selected');
          // end if
         }


         // Vater bekannt machen
         $this->__Children[$ObjectID]->setByReference('ParentObject',$this);


         // XML-Merker-Tag in den Content einpflegen
         $this->__Content .= '<'.$ObjectID.' />';

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert ein Select-Feld-Objekt.<br />
      *
      *  @return string $SelectField; HTML-Code des Select-Felds
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 12.01.2007 (Schönheitskorrekturen)<br />
      *  Version 0.3, 11.02.2007 (Presetting und Validierung nach onAfterAppend() verschoben)<br />
      */
      function transform(){

         // Validierung nachbehandeln
         unset($this->__Attributes['value']);


         // Form transformieren
         $HTML_Select = (string)'';
         $HTML_Select .= '<select '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).'>';

         $Content = $this->__Content;

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if(isset($_REQUEST[$this->getAttribute('name')]) && !empty($_REQUEST[$this->getAttribute('name')])){

                  if($Child->getAttribute('value') == $_REQUEST[$this->getAttribute('name')]){
                     $this->__Children[$ObjectID]->setAttribute('selected','selected');
                   // end if
                  }
                  else{
                     $this->__Children[$ObjectID]->deleteAttribute('selected');
                   // end else
                  }

                // end if
               }

               $Content = str_replace('<'.$ObjectID.' />',$this->__Children[$ObjectID]->transform(),$Content);

             // end foreach
            }

          // end if
         }

         $HTML_Select .= $Content;
         $HTML_Select .= '</select>';


         // Form zurückgeben
         return $HTML_Select;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die selektierten Optionen zurück.<br />
      *
      *  @return select_taglib_option $Option; Das selektierte "select:option"-Element
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008<br />
      */
      function &getSelectedOption(){

         // Presetting lazy ausführen
         $this->__presetValue();

         // Liste initialisieren
         $Option = null;

         foreach($this->__Children as $ObjectID => $Child){
            if($this->__Children[$ObjectID]->getAttribute('selected') == 'selected'){
               $Option = &$this->__Children[$ObjectID];
             // end if
            }

          // end foreach
         }

         // Liste zurückgeben
         return $Option;

       // end function
      }


      /**
      *  @private
      *
      *  Validiert den Wert des Objekts. Wird der Erwartung-Wert nicht erfüllt, so wird das Feld rot hinterlegt.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008 (Methode __validate() für form_taglib_select neu implementiert, da Validierung fehlerhaft)<br />
      */
      function __validate(){

         // Prüfen, ob eine Validierung notwendig ist
         $this->__setValidateObject();


         // Validierung durchführen
         if($this->__ValidateObject == true){

            // Offset des Request-Arrays aus Attribut generieren
            $RequestOffset = $this->__Attributes['name'];

            // Validierung durchführen
            if(!isset($_REQUEST[$RequestOffset]) || empty($_REQUEST[$RequestOffset])){

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

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die Methode "__presetValue" aus der Klasse "ui_element" neu.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008 (Neuimplementierung wegen Validierungsfehlern!)<br />
      */
      function __presetValue(){

         // Werte aus REQUEST auslesen
         if(isset($_REQUEST[$this->__Attributes['name']])){
            $Value = $_REQUEST[$this->__Attributes['name']];
          // end if
         }
         else{
            $Value = (string)'NOVALUEINREQUEST';
          // end else
         }


         // Optionen mit entsprechenden Werten vorselektieren
         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if($this->__Children[$ObjectID]->getAttribute('value') == $Value){
                  $this->__Children[$ObjectID]->setAttribute('selected','selected');
                // end if
               }

             // end foreach
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>