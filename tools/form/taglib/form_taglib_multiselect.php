<?php
   import('tools::form::taglib','select_taglib_option');
   import('tools::form::taglib','form_taglib_select');


   /**
   *  @package tools::form::taglib
   *  @class form_taglib_multiselect
   *
   *  Repr�sentiert ein Multi-Select-Feld-Objekt (HTML-Form).<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 15.01.2007<br />
   *  Version 0.2, 07.06.2008 (Methode transform() konkret f�r das form:multiselect-Feld implementiert)<br />
   *  Version 0.3, 08.06.2008 (Methode __validate() konkret f�r das form:multiselect-Feld implementiert)<br />
   */
   class form_taglib_multiselect extends form_taglib_select
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert die Tag-Lib eines Select-Feldes.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.2, 03.03.2007 ("&" vor "new" entfernt)<br />
      *  Version 0.3, 26.08.2007 ("multiple"-Attribut wird nun automatisch gesetzt)<br />
      */
      function form_taglib_multiselect(){

         // Taglib f�r Optionen hinzuf�gen
         $this->__TagLibs[] = new TagLib('tools::form::taglib','select','option');

         // Validator-Style setzen
         $this->__ValidatorStyle = 'background-color: red;';

         // Multiselect-Attribut setzen
         $this->__Attributes['multiple'] = 'multiple';

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die Methode "onParseTime" f�r das Multi-Select-Feld neu.<br />
      *  Pr�ft, ob das
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.01.2007<br />
      *  Version 0.2, 07.06.2008 (Fehlermeldung erweitert)<br />
      */
      function onParseTime(){

         // Options exztrahieren
         $this->__extractTagLibTags();


         // Pr�fen, ob Name als Array initialisiert wurde
         if(!preg_match('/([A-Za-z0-9]+)\[\]$/',$this->__Attributes['name'])){

            $Form = & $this->__ParentObject;
            $Document = $Form->get('ParentObject');
            $DocumentController = $Document->get('DocumentController');
            trigger_error('[form_taglib_multiselect::onParseTime()] The attribute "name" of the &lt;form:multiselect /&gt; tag in form "'.$Form->getAttribute('name').'" in document controller "'.$DocumentController.'" must not contain whitespace characters before or between "[" or "]"! Otherwise you maybe forgot the "[]"!',E_USER_ERROR);
            exit();

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert ein Select-Feld-Objekt.<br />
      *
      *  @return string $SelectField; HTML-Code des Select-Felds
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.06.2008 (Wegen Presetting-Fehler als eigene Methode implementiert, da die Klammern "[]" beim Attribut "name" nicht ersetzt wurden)<br />
      */
      function transform(){

         // Validierung nachbehandeln
         unset($this->__Attributes['value']);


         // Form transformieren
         $HTML_Select = (string)'';
         $HTML_Select .= '<select '.$this->__getAttributesAsString($this->__Attributes,$this->__ExclusionArray).'>';

         $Content = $this->__Content;

         if(count($this->__Children) > 0){

            // Attribut "name" auslesen und Klammern ersetzen, da sonst presetting
            // nicht sauber funktioniert!
            $TagName = str_replace('[]','',$this->getAttribute('name'));

            foreach($this->__Children as $ObjectID => $Child){

               // Es wird nun abgefragt, ob $_REQUEST[$TagName] ein Array ist
               // und ob der Wert des select-Feldes dort enthalten ist
               if(isset($_REQUEST[$TagName]) && is_array($_REQUEST[$TagName])){
                  if(in_array($Child->getAttribute('value'),$_REQUEST[$TagName])){
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


         // Form zur�ckgeben
         return $HTML_Select;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die selektierten Optionen zur�ck.
      *
      *  @return select_taglib_option[] $Options; Liste von "select:option"-Elementen
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008<br />
      */
      function &getSelectedOptions(){

         // Presetting lazy ausf�hren
         $this->__presetValue();

         // Liste initialisieren
         $Options = array();

         foreach($this->__Children as $ObjectID => $Child){

            if($this->__Children[$ObjectID]->getAttribute('selected') == 'selected'){
               $Options[] = &$this->__Children[$ObjectID];
             // end if
            }

          // end foreach
         }

         // Liste zur�ckgeben
         return $Options;

       // end function
      }


      /**
      *  @private
      *
      *  Implementiert die Methode "__presetValue" aus der Klasse "ui_element" neu.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 15.01.2007<br />
      *  Version 0.2, 16.01.2007 (Pr�fung ob Request-Parameter gesetzt ist)<br />
      */
      function __presetValue(){

         // Offset des Request-Arrays aus Attribut generieren
         $RequestOffset = trim(str_replace('[','',str_replace(']','',$this->__Attributes['name'])));


         // Werte aus REQUEST auslesen
         if(isset($_REQUEST[$RequestOffset])){
            $Values = $_REQUEST[$RequestOffset];
          // end if
         }
         else{
            $Values = array();
          // end else
         }


         // Optionen mit entsprechenden Werten vorselektieren
         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if(in_array($this->__Children[$ObjectID]->getAttribute('value'),$Values)){
                  $this->__Children[$ObjectID]->setAttribute('selected','selected');
                // end if
               }

             // end foreach
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Validiert den Wert des Objekts. Wird der Erwartung-Wert nicht erf�llt, so wird das Feld rot hinterlegt.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 08.06.2008 (Methode __validate() f�r form_taglib_multiselect neu implementiert, da Validierung fehlerhaft)<br />
      */
      function __validate(){

         // Pr�fen, ob eine Validierung notwendig ist
         $this->__setValidateObject();


         // Validierung durchf�hren
         if($this->__ValidateObject == true){

            // Offset des Request-Arrays aus Attribut generieren
            $RequestOffset = trim(str_replace('[','',str_replace(']','',$this->__Attributes['name'])));

            // Validierung durchf�hren
            if(!isset($_REQUEST[$RequestOffset])){

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

    // end class
   }
?>