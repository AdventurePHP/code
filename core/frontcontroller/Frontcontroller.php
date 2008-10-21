<?php
   /**
   *  @package core::frontcontroller
   *  @class AbstractFrontcontrollerAction
   *  @abstract
   *
   *  Implementiert das Action-Interface für eine Frontcontroller-Action.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.01.2007<br />
   *  Version 0.2, 24.02.2007 (Parameter "KeepInURL" hinzugefügt)<br />
   *  Version 0.3, 08.11.2007 (Standardwert von KeepInURL auf false gesetzt)<br />
   */
   class AbstractFrontcontrollerAction extends coreObject
   {

      /**
      *  @private
      *  Namespace der Action.
      */
      var $__ActionNamespace;


      /**
      *  @private
      *  Name der Action.
      */
      var $__ActionName;


      /**
      *  @private
      *  Input-Objekt der Action.
      */
      var $__Input;


      /**
      *  @private
      *  Speichert den Typ der Action. Mögliche Werte:<br />
      *  <ul>
      *    <li>prepagecreate: vor dem Erzeugen der PageController-Seite</li>
      *    <li>postpagecreate: nach dem Erzeugen der PageController-Seite</li>
      *    <li>pretransform: vor der Transformation der PageController-Seite</li>
      *    <li>posttransform: nach der Transformation der PageController-Seite</li>
      *  </ul>
      *  Standard ist prepagecreate.
      */
      var $__Type = 'prepagecreate';


      /**
      *  @private
      *  Speichert, ob die Action in der URL beibehalten werden soll. Werte: true | false.
      */
      var $__KeepInURL = false;


      function AbstractFrontcontrollerAction(){
      }


      /**
      *  @public
      *
      *  Liefert das Input-Objekt (Model) zurück.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.02.2007<br />
      */
      function &getInput(){
         return $this->__Input;
       // end function
      }


      /**
      *  @module run()
      *  @abstract
      *
      *  Abstrakte Methode, die vom FrontController zum Ausführen einer Action aufgerufen wird.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.01.2007<br />
      */
      function run(){
      }

    // end class
   }


   /**
   *  @package core::frontcontroller
   *  @class FrontcontrollerInput
   *
   *  Implementiert das Input-Interface für einen Frontcontroller-Input.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.01.2007<br />
   */
   class FrontcontrollerInput extends coreObject
   {

      function FrontcontrollerInput(){
      }


      /**
      *  @public
      *
      *  Gibt die Input-Attribute als URL-formatierten String zurück.
      *
      *  @return string $AttributesString; URL-formatierten Attribut-String
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2007<br />
      *  Version 0.2, 08.11.2007 (Fehler bei leerem Input-Objekt korrigiert)<br />
      *  Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
      */
      function getAttributesAsString($URLRewriting = null){

         // Parameter vom Frontcontroller auslesen
         $Action = &$this->__ParentObject;
         $fC = &$Action->getByReference('ParentObject');


         // set URLRewriting
         if($URLRewriting === null){
            $Reg = &Singleton::getInstance('Registry');
            $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');
          // end if
         }


         // URL-Trenner setzen
         if($URLRewriting == true){
            $InputDelimiter = $fC->get('URLRewritingInputDelimiter');
            $KeyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
          // end if
         }
         else{
            $InputDelimiter = $fC->get('InputDelimiter');
            $KeyValueDelimiter = $fC->get('KeyValueDelimiter');
          // end else
         }


         // Return-Array initialisieren
         $AttributesArray = array();


         // Array füllen
         if(count($this->__Attributes) > 0){

            foreach($this->__Attributes as $Key => $Value){
               $AttributesArray[] = $Key.$KeyValueDelimiter.$Value;
             // end if
            }

          // end if
         }


         // String zurückgeben
         return implode($InputDelimiter,$AttributesArray);

       // end function
      }

    // end class
   }


   /**
   *  @package core::frontcontroller
   *  @class Frontcontroller
   *
   *  Implementiert den Frontcontroller.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.01.2007<br />
   *  Version 0.2, 01.03.2007 (Input-Objekte werden nun vom Frontcontroller geladen!)<br />
   *  Version 0.3, 08.06.2007 (Größerer Umbau zu den PermanentActions und der URL-Filterung)<br />
   *  Version 0.4, 01.07.2007 (__createInputObject() entfernt)<br />
   */
   class Frontcontroller extends coreObject
   {

      /**
      *  @private
      *  Enthält die registrierten Actions.
      */
      var $__Actions = array();


      /**
      *  @private
      *  Action-Keyword.
      */
      var $__ActionKeyword = 'action';


      /**
      *  @private
      *  Trenner innerhalb des Namespaces einer Action.
      */
      var $__NamespaceURLDelimiter = '_';


      /**
      *  @private
      *  Trenner zwischen Namespace und Action-Keyword.
      */
      var $__NamespaceKeywordDelimiter = '-';


      /**
      *  @private
      *  Trenner zwischen Action-Keyword und Action-Klasse.
      */
      var $__KeywordClassDelimiter = ':';


      /**
      *  @private
      *  Trenner zwischen Action-Keyword und Action-Klasse bei aktiviertem URLRewriting.
      */
      var $__URLRewritingKeywordClassDelimiter = '/';


      /**
      *  @private
      *  Trenner zwischen Input-Werten.
      */
      var $__InputDelimiter = '|';


      /**
      *  @private
      *  Trenner zwischen Input-Werten bei aktiviertem URLRewriting.
      */
      var $__URLRewritingInputDelimiter = '/';


      /**
      *  @private
      *  Trenner zwischen Key zu Value eines Input-Wertes.
      */
      var $__KeyValueDelimiter = ':';


      /**
      *  @private
      *  Trenner zwischen Key zu Value eines Input-Wertes bei aktiviertem URLRewriting.
      */
      var $__URLRewritingKeyValueDelimiter = '/';


      /**
      *  @private
      *  Namespace des Frontcontrollers.
      */
      var $__Namespace = 'core::frontcontroller';


      function Frontcontroller(){
      }


      /**
      *  @public
      *
      *  Executes the desired actions and creates the page output.
      *
      *  @param string $Namespace namespace of the templates
      *  @param string $Template name of the templates
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.01.2007<br />
      *  Version 0.2, 27.01.2007<br />
      *  Version 0.3, 31.01.2007 (Context-Behandlung hinzugefügt)<br />
      *  Version 0.4, 03.02.2007 (Permanente Actions hinzugefügt)<br />
      *  Version 0.5, 08.06.2007 (URL-Filtering in generische Filter ausgelagert)<br />
      *  Version 0.6, 01.07.2007 (Ausführung von permanentpre und permanentpost gelöscht)<br />
      *  Version 0.7, 29.09.2007 (Aufrufzeiten der Actions erweitert / geändert)<br />
      *  Version 0.8, 21.06.2008 (Introduced Registry to retrieve URLRewrite configuration)<br />
      *  Version 0.9, 13.10.2008 (Removed $URLRewriting parameter, because URL rewriting must be configured in the registry)<br />
      */
      function start($Namespace,$Template){

         // set URLRewrite
         $Reg = &Singleton::getInstance('Registry');
         $URLRewriting = $Reg->retrieve('apf::core','URLRewriting');

         // check if the context is set. If not, use the current namespace
         if(empty($this->__Context)){
            $this->__Context = $Namespace;
          // end if
         }

         // initialize URI filter
         if($URLRewriting == true){
            $fCRF = filterFactory::getFilter('core::filter','frontcontrollerRewriteRequestFilter');
          // end if
         }
         else{
            $fCRF = filterFactory::getFilter('core::filter','frontcontrollerRequestFilter');
          // end if
         }

         // filter GET URIand parse action instructions
         $fCRF->filter();

         // execute pre page create actions (see timing model)
         $this->__runActions('prepagecreate');

         // create new page
         $Page = new Page('FrontControllerPage',$URLRewriting);

         // set context
         $Page->set('Context',$this->__Context);

         // set language
         $Page->set('Language',$this->__Language);

         // load desired design
         $Page->loadDesign($Namespace,$Template);

         // execute actions after page creation (see timing model)
         $this->__runActions('postpagecreate');

         // execute actions before transformation (see timing model)
         $this->__runActions('pretransform');

         // transform page
         $PageContent = $Page->transform();

         // execute actions after page transformation
         $this->__runActions('posttransform');

         // display page content
         echo $PageContent;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Referenz auf eine Actions zurück.<br />
      *
      *  @param string $ActionName; Name der Action
      *  @return object $Action | bool NULL; Die Action oder null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.02.2007<br />
      *  Version 0.2, 08.02.2007 (Noch nicht geladene Actions werden lazy nachgeladen und zurückgeliefert)<br />
      *  Version 0.3, 11.02.2007 (ActionName ist nun der Name der Section, bzw. des Alias der Action)<br />
      *  Version 0.4, 01.03.2007 (Input-Objekte werden nun vom Frontcontroller geladen!)<br />
      *  Version 0.5, 01.03.2007 (Prüfung ob Action-Klasse vorhanden ist hinzugefügt!)<br />
      *  Version 0.6, 08.03.2007 (Auf neuen configurationManager umgestellt)<br />
      *  Version 0.7, 08.06.2007 (Automatisches Neuerstellen einer Action entfernt)<br />
      *  Version 0.8, 08.11.2007 (Umstellung auf Hash-Offsets nachgezogen)<br />
      */
      function &getActionByName($ActionName){

         foreach($this->__Actions as $ActionHash => $DUMMY){

            // Prüfen, ob Action mit dem übergebenen Namen existiert
            if($this->__Actions[$ActionHash]->get('ActionName') == $ActionName){
               return $this->__Actions[$ActionHash];
             // end if
            }

          // end foreach
         }


         // Falls Action nicht vorhanden, NULL zurückgeben
         $null = null;
         return $null;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Referenz auf eine Actions zurück.<br />
      *
      *  @return array $Actions; Array mit Action-Objekten
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.02.2007<br />
      */
      function &getActions(){
         return $this->__Actions;
       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt aus einem URL-Namespace einen regulären Namespace.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      function __getActionNamespaceByURLString($NamespaceString){
         return str_replace($this->__NamespaceURLDelimiter,'::',$NamespaceString);
       // end function
      }


      /**
      *  @public
      *
      *  Registriert eine Action beim FC und läd die Parameter des Models aus einem Config-File.<br />
      *  Erwartet eine Konfigurationsdatei mit Namen {APPS__ENVIRONMENT}_actionsconfig.ini unter<br />
      *  dem Pfad {$ActionNamespace}::actions::{$this->__Context}.<br />
      *
      *  @param string $ActionNamespace; Namespace der Action
      *  @param string $ActionName; Name der Action
      *  @param array $ActionParams; (Input-)Parameter der Action
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 01.07.2007 (ActionNamespace wird nun zentral in addAction() übersetzt)<br />
      *  Version 0.3, 01.07.2007 (Parsen der Config-Parameter wird nun korrekt durchgeführt)<br />
      */
      function registerAction($ActionNamespace,$ActionName,$ActionParams = array()){

         // Config für Input laden
         $Config = &$this->__getConfiguration($ActionNamespace.'::actions','actionconfig');

         if($Config != null){

            // Parameter-Wert-Strings trennen
            if(strlen(trim($Config->getValue($ActionName,'FC.InputParams'))) > 0){

               // Parameter trennen
               $Params = explode($this->__InputDelimiter,$Config->getValue($ActionName,'FC.InputParams'));

               for($i = 0; $i < count($Params); $i++){

                  // Parameter und Wert trennen
                  if(substr_count($Params[$i],$this->__KeyValueDelimiter) > 0){

                     $ParamValuePair = explode($this->__KeyValueDelimiter,$Params[$i]);

                     // Paar zu den ActionParams hinzufügen
                     if(isset($ParamValuePair[0]) && isset($ParamValuePair[1])){
                        $ActionParams = array_merge($ActionParams,array($ParamValuePair[0] => $ParamValuePair[1]));
                      // end if
                     }

                   // end if
                  }

                // end for
               }

             // end if
            }

          // end if
         }

         // Action hinzufügen
         $this->addAction($ActionNamespace,$ActionName,$ActionParams);

       // end function
      }



      /**
      *  @public
      *
      *  Fügt eine Action hinzu. Erwartet eine Konfigurationsdatei mit Namen <br />
      *  {ENVIRONMENT}_actionsconfig.ini unter dem Pfad {$ActionNamespace}::actions::{$this->__Context}.<br />
      *
      *  @param string $ActionNamespace; Namespace der Action
      *  @param string $ActionName; Name der Action
      *  @param array $ActionParams; (Input-)Parameter der Action
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.06.2007<br />
      *  Version 0.2, 01.07.2007 (Action-Konfiguration wird nun auch unter "{Namespace}::actions::{Context}" erwartet)<br />
      *  Version 0.3, 02.09.2007 (Fehlermeldung erweitert)<br />
      *  Version 0.4, 08.09.2007 (Input-Parameter aus Config werden jetz beachtet)<br />
      *  Version 0.5, 08.11.2007 (Umstellung auf Hash-Offsets für Actions)<br />
      *  Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function addAction($ActionNamespace,$ActionName,$ActionParams = array()){

         // Namespace umwandeln
         $ActionNamespace = $this->__getActionNamespaceByURLString($ActionNamespace);
         $ActionNamespace .= '::actions';

         // Action-Config laden
         $CfgObj = &$this->__getConfiguration($ActionNamespace,'actionconfig');


         // Prüfen ob Konfiguration existent ist
         if($CfgObj == null){
            trigger_error('[Frontcontroller::__parseActions()] No configuration available for namespace "'.$ActionNamespace.'" and context "'.$this->__Context.'"!',E_USER_ERROR);
            exit;
          // end if
         }


         // Configuration auslesen
         $ActionSection = $CfgObj->getSection($ActionName);

         if($ActionSection == null){
            $Reg = &Singleton::getInstance('Registry');
            $Environment = $Reg->retrieve('apf::core','Environment');
            trigger_error('[Frontcontroller::__parseActions()] No config section for action key "'.$ActionName.'" available in configuration file "'.$Environment.'_actionconfig.ini" in namespace "'.$ActionNamespace.'" and context "'.$this->__Context.'"!',E_USER_ERROR);
            exit;
          // end if
         }


         // Action importieren
         import($ActionSection['FC.ActionNamespace'],$ActionSection['FC.ActionFile']);


         // Action importieren
         import($ActionSection['FC.ActionNamespace'],$ActionSection['FC.InputFile']);


         // Prüfen, ob Action-Klasse vorhanden ist
         if(!class_exists($ActionSection['FC.ActionClass'])){
            trigger_error('[Frontcontroller::__parseActions()] Action class with name "'.$ActionSection['FC.ActionClass'].'" could not be found. Please check your action configuration file!',E_USER_ERROR);
            exit;
          // end if
         }


         // Action initialisieren
         $Action = new $ActionSection['FC.ActionClass'];


         // ActionNamen bekannt machen
         $Action->set('ActionNamespace',$ActionNamespace);


         // ActionNamen bekannt machen
         $Action->set('ActionName',$ActionName);


         // Context übergeben
         $Action->set('Context',$this->__Context);


         // Sprache übergeben
         $Action->set('Language',$this->__Language);


         // Input Objekt erzeugen
         $Input = new $ActionSection['FC.InputClass'];


         // Input-Attribute setzen und mit den Standard-Werten aus der Config mergen (URL sticht!)
         $Input->setAttributes(array_merge($this->__generateParamsFromInputConfig($ActionSection['FC.InputParams']),$ActionParams));


         // Action bekannt machen
         $Input->setByReference('ParentObject',$Action);
         $Action->set('Input',$Input);


         // Frontcontroller bekannt machen
         $Action->setByReference('ParentObject',$this);


         // Action in Action-Array einsetzen
         $this->__Actions[md5($ActionNamespace.'~'.$ActionName)] = $Action;

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt ein Array aus dem Konfigurationswert für Standard-Input-Parameter.<br />
      *
      *  @param string $InputConfig; Konfigurations-String
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 08.09.2007<br />
      */
      function __generateParamsFromInputConfig($InputConfig = ''){

         // Rückgabe-Array initialisieren
         $InputParams = array();


         // Input-String bereinigen
         $InputConfig = trim($InputConfig);


         // Parameter-Array erzeugen
         if(strlen($InputConfig) > 0){

            // String an "|" zerlegen
            $ParamsArray = explode($this->__InputDelimiter,$InputConfig);

            for($i = 0; $i < count($ParamsArray); $i++){

               // String an ":" zerlegen
               $TmpAry = explode($this->__KeyValueDelimiter,$ParamsArray[$i]);

               if(isset($TmpAry[0]) && isset($TmpAry[1]) && !empty($TmpAry[0]) && !empty($TmpAry[1])){
                  $InputParams[$TmpAry[0]] = $TmpAry[1];
                // end if
               }

             // end foreach
            }

          // end if
         }


         // Input-Parameter zurückgeben
         return $InputParams;

       // end function
      }


      /**
      *  @private
      *
      *  Führt diejenigen Actions aus, die dem übergebenen Typ entsprechen.<br />
      *
      *  @param string $Type; Typ der Action (prepagecreate | postpagecreate | pretransform | posttransform)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 27.01.2007<br />
      *  Version 0.2, 31.01.2007 (Debug-Ausgaben konfigurierbar gemacht)<br />
      *  Version 0.3, 03.02.2007 (Actions werden nun gebenchmarked)<br />
      *  Version 0.4, 01.07.2007 (Debug-Ausgaben entfernt)<br />
      *  Version 0.5, 08.11.2007 (Umstellung auf Hash-Offsets nachgezogen)<br />
      *  Version 0.6, 28.03.2008 (Benchmark-Aufruf optimiert)<br />
      */
      function __runActions($Type = 'prepagecreate'){

         // BenchmarkTimer holen
         $T = &Singleton::getInstance('benchmarkTimer');

         // Actions ausführen
         foreach($this->__Actions as $ActionHash => $DUMMY){

            // Action ausführen, wenn Typ passt
            if($this->__Actions[$ActionHash]->get('Type') == $Type){

               // Timer starten
               $ID = get_class($this->__Actions[$ActionHash]).'::run()';
               $T->start($ID);


               // Action ausführen
               $this->__Actions[$ActionHash]->run();


               // Timer stoppen
               $T->stop($ID);

             // end if
            }

          // end for
         }

       // end function
      }

    // end class
   }
?>