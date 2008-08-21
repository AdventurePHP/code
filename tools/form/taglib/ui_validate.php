<?php
   import('tools::validator','myValidator');
   import('core::configuration','configurationManager');
   import('core::singleton','Singleton');


   /**
   *  @package tools::form::taglib
   *  @class ui_validate
   *
   *  Repräsentiert ein abstraktes Validate-Objekt (HTML-Form).<br />
   *  <br />
   *  Tag erwartet folgende Attribute:<br />
   *   - type: Validierungstyp. Werte: text | css. Standard: css<br />
   *   - field: Name des Feldes, das validiert werden soll<br />
   *   - validator: Name der Validator-Methode. Standard: Text<br />
   *   - button: Name des Buttons der Form<br />
   *   - style: CSS-Style, falls type="css"<br />
   *   - lang: Sprachkürzel, falls tyle=text. Standard: de<br />
   *   - msginputreq: Konfigurations-Schlüssel für eine angepasste Fehlermeldung (Eingabe erforderlich)<br />
   *   - msginputwrg: onfigurations-Schlüssel für eine angepasste Fehlermeldung (Eingabe fehlerhaft)<br />
   *  <br />
   *  Setzt eine Konfigurations-Datei 'formconfig' im Namespace "tools::form::taglib"<br />
   *  und dem aktuellen Context der Applikation in der Form<br />
   *  <br />
   *  [de]<br />
   *  InputRequired = "[..]"<br />
   *  InputWrong = "[..]"<br />
   *  [..]<br />
   *  <br />
   *  [en]<br />
   *  [..]
   *  <br />
   *  voraus.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 11.02.2007<br />
   *  Version 0.2, 25.03.2007 (Weitere Attribute "msginputreq" und "msginputwrg" hinzugefügt)<br />
   */
   class ui_validate extends ui_element
   {

      function ui_validate(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onAfterAppend".<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      *  Version 0.2, 25.03.2007 (Weitere Attribute "msginputreq" und "msginputwrg" hinzugefügt)<br />
      *  Version 0.3, 29.03.2007 (isValid der Form wird nun auch auf false gesetzt)<br />
      */
      function onAfterAppend(){

         // Typ auslesen
         if(isset($this->__Attributes['type']) && !empty($this->__Attributes['type'])){
            $Type = $this->__Attributes['type'];
          // end if
         }
         else{
            $Type = 'css';
          // end else
         }


         // ValidatorMethode auslesen
         if(isset($this->__Attributes['validator']) && !empty($this->__Attributes['validator'])){
            $Validator = $this->__Attributes['validator'];
          // end if
         }
         else{
            $Validator = 'Text';
          // end else
         }

         $ValidatorMethode = 'validate'.$Validator;


         // Validierungs-Methode auslesen
         if(!isset($this->__Attributes['field']) || !isset($this->__Attributes['button']) || empty($this->__Attributes['field']) || empty($this->__Attributes['button'])){
            $Name = $this->__ParentObject->getAttribute('name');
            trigger_error('['.get_class($this).'::onAfterAppend()] Validator tag in form or validator group "'.$Name.'" has no or an empty button- or field- attributes!',E_USER_ERROR);
            exit;
          // end if
         }
         else{
            $Field = $this->__Attributes['field'];
            $Button = $this->__Attributes['button'];
          // end else
         }


         // String, der zu validieren ist, auslesen
         if(isset($_REQUEST[trim($this->__Attributes['field'])])){
            $String = $_REQUEST[trim($this->__Attributes['field'])];
          // end if
         }
         else{
            $String = (string)'';
          // end else
         }


         // Markierungsfarbe auslesen
         if($Type == 'css'){

            if(isset($this->__Attributes['style'])){
               $Style = $this->__Attributes['style'];
             // end if
            }
            else{
               $Style = $this->__ValidatorStyle;
             // end else
            }

          // end if
         }
         else{
            $Style = $this->__ValidatorStyle;
          // end else
         }


         // Markierungstext-Sprache auslesen (Standard: Sprache des Objekts)
         if($Type == 'text'){

            if(isset($this->__Attributes['lang'])){
               $Lang = $this->__Attributes['lang'];
             // end if
            }
            else{
               $Lang = $this->__Language;
             // end else
            }

          // end if
         }


         // Validiere Wert, falls Button geklickt wurde
         if(isset($_REQUEST[$Button])){

            if(myValidator::$ValidatorMethode($String) == true){
               $this->__Content = (string)'';
             // end if
            }
            else{

               // Validierung für Text vornehmen
               if($Type == 'text'){

                  // Konfiguration für die Ausgabe des Validate-Tags einlesen
                  $Config = $this->__getConfiguration('tools::form::taglib','formconfig');
                  $LangSection = $Config->getSection($Lang);


                  // Speziellen Offset für die Fehlerausgabe (falscher Input) holen, falls vorhanden
                  if(isset($this->__Attributes['msginputwrg']) && !empty($this->__Attributes['msginputwrg'])){
                     $MessageInputWrong = $this->__Attributes['msginputwrg'];
                   // end if
                  }
                  else{
                     $MessageInputWrong = 'InputWrong';
                   // end else
                  }


                  // Speziellen Offset für die Fehlerausgabe (kein Input) holen, falls vorhanden
                  if(isset($this->__Attributes['msginputreq']) && !empty($this->__Attributes['msginputreq'])){
                     $MessageInputRequired = $this->__Attributes['msginputreq'];
                   // end if
                  }
                  else{
                     $MessageInputRequired = 'InputRequired';
                   // end else
                  }


                  // Content setzen
                  if(myValidator::validateText($String) == true){

                     if(isset($LangSection[$MessageInputWrong])){
                        $this->__Content = $LangSection[$MessageInputWrong];
                      // end if
                     }
                     else{
                        $this->__Content = $LangSection['InputWrong'];
                      // end else
                     }

                   // end if
                  }
                  else{

                     if(isset($LangSection[$MessageInputRequired])){
                        $this->__Content = $LangSection[$MessageInputRequired];
                      // end if
                     }
                     else{
                        $this->__Content = $LangSection['InputRequired'];
                      // end else
                     }

                   // end else
                  }

                // end if
               }


               // Validierung für Farbe vornehmen
               if($Type == 'color'){
                  $this->__Content = $Style;
                // end if
               }


               // html:form oder form:valgroup als invalid kennzeichnen
               $this->__ParentObject->set('isValid',false);

               // Falls Vater eine ValidatorGruppe ist auch die Form als invalid kennzeichnen
               if(get_class($this->__ParentObject) == 'form_taglib_valgroup'){
                  $Form = &$this->__ParentObject->getByReference('ParentObject');
                  $Form->set('isValid',false);
                // end if
               }

             // end else
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
      *  @return string $ValidatorResult; Rückgabe-Wert des Validators
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 11.02.2007<br />
      */
      function transform(){
         return $this->__Content;
       // end function
      }

    // end class
   }
?>