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

   import('tools::form::taglib','valgroup_taglib_validate');
   import('tools::form::taglib','valgroup_taglib_placeholder');


   /**
   *  @namespace tools::form::taglib
   *  @class form_taglib_valgroup
   *
   *  Repräsentiert eine Validatoren-Gruppe (HTML-Form).<br />
   *  <br />
   *  Tag sollte einen Namen, bzw. eine ID besitzten und gruppiert Validatoren.<br />
   *  wird ausgegeben, wenn ein dort enthaltender Validator "anschlägt".<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 25.03.2007<br />
   */
   class form_taglib_valgroup extends ui_element
   {

      /**
      *  @protected
      *  Indikator, ob die ValGroup beim Transformieren der Form ausgegeben werden soll.
      */
      protected $__isValid = true;


      /**
      *  @public
      *
      *  Konstruktor der Klasse initialisiert die TagLibs.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 25.03.2007<br />
      *  Version 0.2, 28.03.2007<br />
      */
      function form_taglib_valgroup(){
         $this->__TagLibs[] = new TagLib('tools::form::taglib','valgroup','validate');
         $this->__TagLibs[] = new TagLib('tools::form::taglib','valgroup','placeholder');
       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "onParseTime". Extrahiert die enthaltenen Tags.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 25.03.2007<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }


      /**
      *  @public
      *
      *  Füllt einen Platzhalter innerhalb einer Validator-Gruppe.<br />
      *
      *  @param string $Name; Name des Platzhalters
      *  @param string $Value; Wert des Platzhalters
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.03.2007<br />
      */
      function setPlaceHolder($Name,$Value){

         // Deklariert das notwendige TagLib-Modul
         $TagLibModule = 'valgroup_taglib_placeholder';


         // Falls TagLib-Modul nicht vorhanden -> Fehler!
         if(!class_exists($TagLibModule)){
            trigger_error('[form_taglib_valgroup::setPlaceHolder()] TagLib module '.$TagLibModule.' is not loaded!',E_USER_ERROR);
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
            trigger_error('[form_taglib_valgroup::setPlaceHolder()] No placeholder object with name "'.$Name.'" composed in current validator group for document controller "'.($this->__ParentObject->__DocumentController).'"! Perhaps tag library valgroup:placeholder is not loaded in validator group "'.$this->__Attributes['name'].'"!',E_USER_ERROR);
            exit();

          // end else
         }

         // Warnen, falls kein Platzhalter gefunden wurde
         if($PlaceHolderCount < 1){
            trigger_error('[form_taglib_valgroup::setPlaceHolder()] There are no placeholders found for name "'.$Name.'" in validator group "'.($this->__Attributes['name']).'" in document controller "'.($this->__ParentObject->__DocumentController).'"!',E_USER_WARNING);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte Methode "transform".<br />
      *
      *  @return string $ValidatorResult; Rückgabe-Wert der Validator-Gruppe
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 25.03.2007<br />
      */
      function transform(){

         // Kinder transformieren und einsetzen
         if($this->__isValid == false){

            // Kinder transformieren und einsetzen
            foreach($this->__Children as $ObjectID => $Child){
               $this->__Content = str_replace('<'.$ObjectID.' />',$Child->transform(),$this->__Content);
             // end foreach
            }

            // Content zurückgeben
            return $this->__Content;

          // end if
         }
         else{
            return (string)'';
          // end else
         }

       // end function
      }

    // end class
   }
?>