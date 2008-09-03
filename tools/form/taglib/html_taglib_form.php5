<?php
   import('tools::form::taglib','ui_element');
   import('tools::form::taglib','form_taglib_button');
   import('tools::form::taglib','form_taglib_text');
   import('tools::form::taglib','form_taglib_select');
   import('tools::form::taglib','form_taglib_date');
   import('tools::form::taglib','form_taglib_placeholder');
   import('tools::form::taglib','form_taglib_password');
   import('tools::form::taglib','form_taglib_hidden');
   import('tools::form::taglib','form_taglib_checkbox');
   import('tools::form::taglib','form_taglib_radio');
   import('tools::form::taglib','form_taglib_file');
   import('tools::form::taglib','form_taglib_area');
   import('tools::form::taglib','form_taglib_multiselect');
   import('tools::form::taglib','form_taglib_validate');
   import('tools::form::taglib','form_taglib_valgroup');
   import('tools::form::taglib','form_taglib_genericval');
   import('tools::form::taglib','form_taglib_getstring');
   import('tools::form::taglib','form_taglib_addtaglib');
   import('tools::form::taglib','form_taglib_marker');


   /**
   *  @package tools::form::taglib
   *  @class html_taglib_form
   *
   *  Repräsentiert ein Form-Objekt (HTML-Form).<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 05.01.2007<br />
   *  Version 0.2, 12.01.2007 (Form wird jetzt wie ein Template behandelt)<br />
   *  Version 0.3, 13.01.2007 (Weitere TagLibs hinzugefügt)
   *  Version 0.4, 15.01.2007 (Weitere TagLib "form:multiselect" hinzugefügt)<br />
   *  Version 0.5, 11.02.2007 (Weitere TagLib "form:validate" hinzugefügt)<br />
   *  Version 0.6, 25.06.2007 (TagLib "form:validate" entfernt und durch "form:valgroup" ersetzt)<br />
   *  Version 0.7, 14.04.2007 (Attribut "isSent" hinzugefügt)<br />
   *  Version 0.8, 22.09.2007 (Generischen Vaidator hinzugefügt)<br />
   *  Version 0.9, 01.06.2008 (Methode getFormElementsByType() hinzugefügt)<br />
   *  Version 1.0, 16.06.2008 (API-Änderung: getFormElementsByTagName() hinzugefügt)<br />
   */
   class html_taglib_form extends ui_element
   {

      /**
      *  @private
      *  Speichert, ob das Formular mit gültigen Werten ausgefüllt wurde.
      */
      var $__isValid = true;


      /**
      *  @private
      *  @since 0.7
      *  Speichert, ob das Formular abgesendet wurde.
      */
      var $__isSent = false;


      /**
      *  @private
      *  Indiziert, ob das Formular an der Definitionsstelle transformiert und ausgegeben werden soll.
      */
      var $__TransformOnPlace = false;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Fügt die in einer Form enthaltenen Tags als TagLib hinzu.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      *  Version 0.2, 13.01.2007 (Weitere TagLibs hinzugefügt)<br />
      *  Version 0.3, 15.01.2007 (Weitere TagLib "form:multiselect" hinzugefügt)<br />
      *  Version 0.4, 11.02.2007 (Weitere TagLib form:validate" hinzugefügt)<br />
      *  Version 0.5, 03.03.2007 ("&" vor "new" entfernt)<br />
      *  Version 0.6, 25.03.2007 (Weitere Taglib "form:valgroup" eingeführt)<br />
      *  Version 0.7, 22.09.2007 (Generischen Vaidator hinzugefügt)<br />
      *  Version 0.8, 06.11.2007 (Tag form:getstring hinzugefügt)<br />
      *  Version 0.9, 11.07.2008 (Added the form:addtaglib tag)<br />
      *  Version 1.0, 03.09.2008 (Added the form:marker tag)<br />
      */
      function html_taglib_form(){

         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','button');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','text');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','select');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','date');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','placeholder');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','password');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','hidden');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','checkbox');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','radio');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','file');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','area');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','multiselect');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','valgroup');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','validate');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','genericval');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','getstring');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','marker');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','form','addtaglib');

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onParseTime". Extrahiert die enthaltenen Tags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  Fügt ein neues Form-Element zu einer Form hinzu.<br />
      *
      *  @param string $Type; Typ des Formelements (Name einer TagLib-Klasse)
      *  @return string $ObjectID | null; Objekt-ID des neu erzeugten Form-Objekts oder null im Fehlerfall
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function addFormElement($Type){

         // Prüfen, ob Klasse existiert
         if(class_exists($Type)){

            // Objekt-ID generieren
            $ObjectID = xmlParser::generateUniqID();

            // FormObjekt generieren und Attribute füllen
            $FormObject = new $Type;
            $FormObject->set('ObjectID',$ObjectID);
            $FormObject->setByReference('ParentObject',$this);

            // FormObjekt als Kind einhängen
            $this->__Children[$ObjectID] = $FormObject;

            // XML-Merker-Tag im Content hinzufügen
            $this->__Content .= '<'.$ObjectID.' />';

            // Referenz auf die Funktion zurückgeben
            return $ObjectID;

          // end if
         }
         else{
            return null;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Fügt weiteren Content (i.d.R. HTML-Code) zur aktuellen Form hinzu.<br />
      *
      *  @param string $Content; Content
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.01.2007<br />
      */
      function addFormContent($Content){
         $this->__Content .= $Content;
       // end function
      }


      /**
      *  @public
      *
      *  Adds content in front of a form marker. This method is intended to dynamically generate forms.
      *
      *  @param string $MarkerName the desired marker name
      *  @param string $Content the content to add
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function addFormContentBeforeMarker($MarkerName,$Content){

         // get desired marker
         $Marker = &$this->__getMarker($MarkerName);

         // check if marker exists
         if($Marker !== null){

            // get the object if
            $ObjectID = $Marker->get('ObjectID');

            // add the desired content before the marker
            $this->__Content = str_replace('<'.$ObjectID.' />',$Content.'<'.$ObjectID.' />',$this->__Content);

          // end if
         }
         else{

            // display an error
            trigger_error('[html_taglib_form::addFormContentBeforeMarker()] No marker object with name "'.$MarkerName.'" composed in current form for document controller "'.($this->__ParentObject->__DocumentController).'"! Please check the definition of the form with name "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

       // end function
      }

      function addFormContentAfterMarker($MarkerName,$Content){
      }

      function addFormElementBeforeMarker($MarkerName,$ElementType){
      }

      function addFormElementAfterMarker($MarkerName,$ElementType){
      }


      /**
      *  @private
      *
      *  Returns a reference on the desired marker or null.
      *
      *  @param string $MarkerName the desired marker name
      *  @return form_taglib_marker $Marker the marker or null
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.09.2008<br />
      */
      function &__getMarker($MarkerName){

         // check, weather the form has children
         if(count($this->__Children) > 0){

            // have a look at the children
            foreach($this->__Children as $ObjectID => $Child){

               // check, if current children is a marker
               if(get_class($Child) == 'form_taglib_marker'){

                  // check, if the name fits the method's argument
                  if($Child->getAttribute('name') == $MarkerName){
                     return $this->__Children[$ObjectID];
                   // end if
                  }

                // end if
               }

             // end foreach
            }

            // return null, if no child was found (with a quick hack)
            $null = null;
            return $null;

          // end if
         }
         else{

            // return null (with a quick hack)
            $null = null;
            return $null;

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Füllt einen Platzhalter innerhalb einer Form.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @param string $Value; Wert des Platzhalters
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      */
      function setPlaceHolder($Name,$Value){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'form_taglib_placeholder';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('[html_taglib_form::setPlaceHolder()] TagLib module '.$TagLibModule.' is not loaded!',E_USER_ERROR);
          // end if
         }


         // Anzahl der Platzhalter zählen
         $PlaceHolderCount = 0;


         // Prüfen, ob Kinder vorhanden
         if(count($this->__Children) > 0){

            // Nachsehen, ob es Kinder der Klasse 'template_taglib_placeholder' gibt
            foreach($this->__Children as $ObjectID => $Child){

               // Prüfen, ob Kind ein
               if(get_class($Child) == $TagLibModule){

                  // Prüfen, ob das Attribut 'name' dem angegebenen Namen entspricht
                  // und Content einsetzen
                  if($Child->getAttribute('name') == $Name){

                     $this->__Children[$ObjectID]->set('Content',$Value);
                     $PlaceHolderCount++;

                   // end if
                  }

                // end if
               }

             // end foreach
            }

          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            trigger_error('[html_taglib_form::setPlaceHolder()] No placeholder object with name "'.$Name.'" composed in current for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library form:placeholder is not loaded in form "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }


         // Warnen, falls kein Platzhalter gefunden wurde
         if($PlaceHolderCount < 1){
            trigger_error('[html_taglib_form::setPlaceHolder()] There are no placeholders found for name "'.$Name.'" in template "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }



      /**
      *  @public
      *
      *  Setzt das action-Attribut einer Form.<br />
      *
      *  @param string $Action; Action der Form
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function setAction($Action){
         $this->__Attributes['action'] = $Action;
       // end function
      }


      /**
      *  @public
      *
      *  Gibt ein Formular-Element, das mit $Name spezifiziert ist zurück.<br />
      *
      *  @param string $Name; Name des Form-Elements
      *  @return object $FormElement; Referenz auf ein Form-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      */
      function &getFormElementByName($Name){

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if($Child->getAttribute('name') == $Name){
                  return $this->__Children[$ObjectID];
                // end if
               }

             // end foreach
            }

          // end if
         }

         // Falls keine Kinder existieren -> Fehler!
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementByName()] No form element with name "'.$Name.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Gibt ein Formular-Element, das mit der ID $ID spezifiziert ist zurück.<br />
      *  Wird für den Zugriff auf Radio-Buttons benötigt, da diese gleich benamt sind.<br />
      *
      *  @param string $ID; ID des Form-Elements
      *  @return object $FormElement; Referenz auf ein Form-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.01.2007<br />
      */
      function &getFormElementByID($ID){

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){

               if($Child->getAttribute('id') == $ID){
                  return $this->__Children[$ObjectID];
                // end if
               }

             // end foreach
            }

          // end if
         }

         // Falls keine Kinder existieren -> Fehler!
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementByID()] No form element with id "'.$ID.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Gibt ein Formular-Element, das mit $ID spezifiziert ist zurück.<br />
      *
      *  @param string $ID; Objekt-ID des Form-Elements
      *  @return object $FormElement; Referenz auf ein Form-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 07.01.2007<br />
      *  Version 0.1, 12.01.2007 (Fehlerausgabe korrigiert)<br />
      */
      function &getFormElementByObjectID($ID){

         if(isset($this->__Children[$ID])){
            return $this->__Children[$ID];
          // end if
         }
         else{

            // Falls keine Kinder existieren -> Fehler!
            $Parent = $this->get('ParentObject');
            $GrandParent = $Parent->get('ParentObject');
            $DocumentController = $GrandParent->get('DocumentController');
            trigger_error('[html_taglib_form::getFormElementByObjectID()] No form element with id "'.$ID.'" composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
            exit();

          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Gibt Formular-Elemente des übergebenen Typs (z.B. "form:text") zurück.<br />
      *
      *  @param string $TagName; Type des Form-Elements
      *  @return array $FormElements; Liste von Referenzen auf die gewünschten Form-Objekte
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 14.06.2008 (API-Änderung. Statt getFormElementsByType() soll nur noch getFormElementsByTagName() verwendet werden, da intuitiver.)<br />
      */
      function &getFormElementsByTagName($TagName){

         // TagLib-Klasse zusammensetzen
         $Colon = strpos($TagName,':');
         $ClassName = trim(substr($TagName,0,$Colon)).'_taglib_'.trim(substr($TagName,$Colon + 1));

         // Eelemente suchen
         if(count($this->__Children) > 0){

            $FormElements = array();
            foreach($this->__Children as $ObjectID => $Child){

               if(get_class($this->__Children[$ObjectID]) == $ClassName){
                  $FormElements[] = &$this->__Children[$ObjectID];
                // end if
               }

             // end foreach
            }

            // Liste zurückgeben
            return $FormElements;

          // end if
         }

         // Falls keine Kinder existieren -> Fehler!
         $Parent = $this->get('ParentObject');
         $GrandParent = $Parent->get('ParentObject');
         $DocumentController = $GrandParent->get('DocumentController');
         trigger_error('[html_taglib_form::getFormElementsByType()] No form elements composed in current form "'.$this->__Attributes['name'].'" in document controller "'.$DocumentController.'"!',E_USER_ERROR);
         exit();

       // end function
      }


      /**
      *  @public
      *
      *  Transformiert eine Form und gibt den HTML-Quelltext zurück.<br />
      *
      *  @return string $Form; HTML-Quelltext der Form
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 20.01.2007 (Handling für Attribut "action" geändert)<br />
      */
      function transformForm(){

         // Verifizieren, ob action-Attribut gesetzt ist
         if(!isset($this->__Attributes['action']) || empty($this->__Attributes['action'])){
            $this->__Attributes['action'] = $_SERVER['REQUEST_URI'];
          // end if
         }


         // Form transformieren
         $HTML_Form = (string)'';
         $HTML_Form .= '<form '.$this->__getAttributesAsString($this->__Attributes).'>';

         $Content = $this->__Content;

         if(count($this->__Children) > 0){

            foreach($this->__Children as $ObjectID => $Child){
               $Content = str_replace('<'.$ObjectID.' />',$Child->transform(),$Content);
             // end foreach
            }

          // end if
         }

         $HTML_Form .= $Content;
         $HTML_Form .= '</form>';


         // Form zurückgeben
         return $HTML_Form;

       // end function
      }


      /**
      *  @public
      *
      *  Definiert, dass das Formular an der exakten Definitionsstelle transformiert und<br />
      *  ausgegeben werden soll.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function transformOnPlace(){
         $this->__TransformOnPlace = true;
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform" des "coreObject"s.<br />
      *
      *  @return string $Content; Leer-String oder Inhalt des Tags
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2007<br />
      *  Version 0.2, 01.06.2008 (transformOnPlace()-Feature implementiert)<br />
      */
      function transform(){

         // Prüfen, ob Template ausgegeben werden soll
         if($this->__TransformOnPlace === true){
            return $this->transformForm();
          // end if
         }

         // Leerstring zurückgeben
         return (string)'';

       // end function
      }

    // end class
   }
?>