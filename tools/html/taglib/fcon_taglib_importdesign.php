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

   /**
   *  @namespace tools::html::taglib
   *  @class fcon_taglib_importdesign
   *
   *  Implementiert ein core::importdesign-Tag, das den aktuellen View aus dem Model der <br />
   *  Anwendung ausliest. Der Tag kann per Attributen für jede Anwendung generisch konfiguriert <br />
   *  werden. Erwartet die Attribute
   *  <ul>
   *    <li>templatenamespace: Namespace des Templates (Wert: gültiger Namespace)</li>
   *    <li>modelnamespace: Namespace des Applikationsmodels (Wert: gültiger Namespace)</li>
   *    <li>modelfile: Name der Datei des Models (Wert: Dateiname)</li>
   *    <li>modelclass: Name der Model-Klasse (Wert: Klassenname)</li>
   *    <li>modelparam: Name des Attributs des Models, das als Templatename verwendet werden soll</li>
   *    <li>context: Setzt den Context des Knotens (Wert: Gültiger Context)</li>
   *    <li>sessionsingleton: defines, if the model is retrieved sessionsingleton or just singleton (values: true|false)</li>
   *  </ul>
   *  Alle Parameter ausser "context" sind Pflichtparameter.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 13.11.2007<br />
   */
   class fcon_taglib_importdesign extends core_taglib_importdesign
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.11.2007<br />
      */
      function fcon_taglib_importdesign(){
         parent::core_taglib_importdesign();
       // end function
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet das Template, das in den
      *  Attributen beschreiben ist als neuen Objekt-Baum-Knoten ein.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 13.11.2007<br />
      *  Version 0.2, 30.08.2008 (Fixed bug, that model class was not included)<br />
      *  Version 0.3, 23.10.2008 (Added the sessionsingleton param to be able to handle session models)<br />
      */
      function onParseTime(){

         // Timer starten
         $T = &Singleton::getInstance('BenchmarkTimer');
         $T->start('(fcon_taglib_importdesign) '.$this->__ObjectID.'::onParseTime()');

         // Template-Namespace auslesen
         if(!isset($this->__Attributes['templatenamespace'])){
            trigger_error('[fcon_taglib_importdesign::onParseTime()] Attribute "templatenamespace" is not given!');
            return null;
          // end if
         }
         else{
            $TemplateNamespace = $this->__Attributes['templatenamespace'];
          // end else
         }

         // Template-Namespace auslesen
         if(!isset($this->__Attributes['modelnamespace'])){
            trigger_error('[fcon_taglib_importdesign::onParseTime()] Attribute "modelnamespace" is not given!');
            return null;
          // end if
         }
         else{
            $ModelNamespace = $this->__Attributes['modelnamespace'];
          // end else
         }

         // Model-Datei auslesen
         if(!isset($this->__Attributes['modelfile'])){
            trigger_error('[fcon_taglib_importdesign::onParseTime()] Attribute "modelfile" is not given!');
            return null;
          // end if
         }
         else{
            $ModelFile = $this->__Attributes['modelfile'];
          // end else
         }

         // Model-Klasse auslesen
         if(!isset($this->__Attributes['modelclass'])){
            trigger_error('[fcon_taglib_importdesign::onParseTime()] Attribute "modelclass" is not given!');
            return null;
          // end if
         }
         else{
            $ModelClass = $this->__Attributes['modelclass'];
          // end else
         }

         // Model-Klasse auslesen
         if(!isset($this->__Attributes['modelparam'])){
            trigger_error('[fcon_taglib_importdesign::onParseTime()] Attribute "modelparam" is not given!');
            return null;
          // end if
         }
         else{
            $ModelParam = $this->__Attributes['modelparam'];
          // end else
         }

         // Prüfen, ob Model-Klasse bereits eingebunden wurde und ggf. nachholen
         if(!class_exists($ModelClass)){
            import($ModelNamespace,$ModelFile);
          // end if
         }

         // get initialization mode
         if(!isset($this->__Attributes['sessionsingleton']) || $this->__Attributes['sessionsingleton'] == 'false'){
            $initMode = 'SINGLETON';
          // end if
         }
         else{
            $initMode = 'SESSIONSINGLETON';
          // end else
         }

         // read the name of the template from the model
         $Model = &$this->__getServiceObject($ModelNamespace,$ModelClass,$initMode);
         $TemplateName = $Model->getAttribute($ModelParam);

         // Context-Parameter einlesen, falls gesetzt
         if(isset($this->__Attributes['context'])){
            $this->__Context = trim($this->__Attributes['context']);
          // end if
         }

         // Content einlesen
         $this->__loadContentFromFile($TemplateNamespace,$TemplateName);

         // Nach einem DocumentController suchen
         $this->__extractDocumentController();

         // XML-Tags im Content parsen
         $this->__extractTagLibTags();

         // Timer stoppen
         $T->stop('(fcon_taglib_importdesign) '.$this->__ObjectID.'::onParseTime()');

       // end function
      }

    // end class
   }
?>