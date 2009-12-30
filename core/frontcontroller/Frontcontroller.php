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

   // setup the front controller input filter and disable the page controller filter
   $reg = &Singleton::getInstance('Registry');
   $reg->register('apf::core::filter','FrontControllerInputFilter',new FilterDefinition('core::filter','FrontControllerInputFilter'));
   $reg->register('apf::core::filter','PageControllerInputFilter',null);


   /**
   *  @package core::frontcontroller
   *  @class AbstractFrontcontrollerAction
   *  @abstract
   *
   *  Implements an action interface for a front controller action.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 27.01.2007<br />
   *  Version 0.2, 24.02.2007 (Added param "KeepInURL")<br />
   *  Version 0.3, 08.11.2007 (Standardwert von KeepInURL auf false gesetzt)<br />
   */
   abstract class AbstractFrontcontrollerAction extends coreObject
   {

      /**
      *  @private
      *  Namespace of the action.
      */
      protected $__ActionNamespace;


      /**
      *  @private
      *  Name of the action.
      */
      protected $__ActionName;


      /**
      *  @private
      *  Input object of the action.
      */
      protected $__Input;


      /**
      *  @private
      *  Defines the type of the action. Allowed values
      *  <ul>
      *    <li>prepagecreate: executed before the page controller page is created</li>
      *    <li>postpagecreate: executed after the page controller page is created</li>
      *    <li>pretransform: executed before transformation of the page</li>
      *    <li>posttransform: executed after transformation of the page</li>
      *  </ul>
      *  The default value is "prepagecreate".
      */
      protected $__Type = 'prepagecreate';


      /**
      *  @private
      *  Indicates, if the action should be included in the URL. Values: true | false.
      */
      protected $__KeepInURL = false;


      function AbstractFrontcontrollerAction(){
      }


      /**
      *  @public
      *
      *  Returns the input object of the action.
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
      *  Defines the interface method, that must be implemented by each concrete action. The method
      *  is called by the front controller, when the action is executed.
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
   *  Implements a base class for input parameters for front controller actions.
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
      *  Returns all input parameters as a URL formatted string.
      *
      *  @param boolean $urlRewriting True for activated url rewriting, false instead
      *  @return string $attributesString URL formatted attributes string
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.02.2007<br />
      *  Version 0.2, 08.11.2007 (Fehler bei leerem Input-Objekt korrigiert)<br />
      *  Version 0.3, 21.06.2008 (Removed APPS__URL_REWRITING and introduced the Registry instead)<br />
      */
      function getAttributesAsString($urlRewriting = null){

         // get the current front controller
         $action = &$this->__ParentObject;
         $fC = &$action->getByReference('ParentObject');

         // set URLRewriting manually
         if($urlRewriting === null){
            $reg = &Singleton::getInstance('Registry');
            $urlRewriting = $reg->retrieve('apf::core','URLRewriting');
          // end if
         }

         // define url delimiter
         if($urlRewriting == true){
            $InputDelimiter = $fC->get('URLRewritingInputDelimiter');
            $KeyValueDelimiter = $fC->get('URLRewritingKeyValueDelimiter');
          // end if
         }
         else{
            $InputDelimiter = $fC->get('InputDelimiter');
            $KeyValueDelimiter = $fC->get('KeyValueDelimiter');
          // end else
         }

         // fill consolidated attributes array
         $AttributesArray = array();
         if(count($this->__Attributes) > 0){

            foreach($this->__Attributes as $Key => $Value){
               $AttributesArray[] = $Key.$KeyValueDelimiter.$Value;
             // end if
            }

          // end if
         }

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
   *  Version 0.3, 08.06.2007 (Gr��erer Umbau zu den PermanentActions und der URL-Filterung)<br />
   *  Version 0.4, 01.07.2007 (__createInputObject() entfernt)<br />
   */
   class Frontcontroller extends coreObject
   {

      /**
      *  @protected
      *  Enth�lt die registrierten Actions.
      */
      protected $__Actions = array();


      /**
      *  @protected
      *  Action-Keyword.
      */
      protected $__ActionKeyword = 'action';


      /**
      *  @protected
      *  Trenner innerhalb des Namespaces einer Action.
      */
      protected $__NamespaceURLDelimiter = '_';


      /**
      *  @protected
      *  Trenner zwischen Namespace und Action-Keyword.
      */
      protected $__NamespaceKeywordDelimiter = '-';


      /**
      *  @protected
      *  Trenner zwischen Action-Keyword und Action-Klasse.
      */
      protected $__KeywordClassDelimiter = ':';


      /**
      *  @protected
      *  Trenner zwischen Action-Keyword und Action-Klasse bei aktiviertem URLRewriting.
      */
      protected $__URLRewritingKeywordClassDelimiter = '/';


      /**
      *  @protected
      *  Trenner zwischen Input-Werten.
      */
      protected $__InputDelimiter = '|';


      /**
      *  @protected
      *  Trenner zwischen Input-Werten bei aktiviertem URLRewriting.
      */
      protected $__URLRewritingInputDelimiter = '/';


      /**
      *  @protected
      *  Trenner zwischen Key zu Value eines Input-Wertes.
      */
      protected $__KeyValueDelimiter = ':';


      /**
      *  @protected
      *  Trenner zwischen Key zu Value eines Input-Wertes bei aktiviertem URLRewriting.
      */
      protected $__URLRewritingKeyValueDelimiter = '/';


      /**
      *  @protected
      *  Namespace des Frontcontrollers.
      */
      protected $__Namespace = 'core::frontcontroller';


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
      *  Version 0.3, 31.01.2007 (Context-Behandlung hinzugef�gt)<br />
      *  Version 0.4, 03.02.2007 (Permanente Actions hinzugef�gt)<br />
      *  Version 0.5, 08.06.2007 (URL-Filtering in generische Filter ausgelagert)<br />
      *  Version 0.6, 01.07.2007 (Ausf�hrung von permanentpre und permanentpost gel�scht)<br />
      *  Version 0.7, 29.09.2007 (Aufrufzeiten der Actions erweitert / ge�ndert)<br />
      *  Version 0.8, 21.06.2008 (Introduced Registry to retrieve URLRewrite configuration)<br />
      *  Version 0.9, 13.10.2008 (Removed $URLRewriting parameter, because URL rewriting must be configured in the registry)<br />
      *  Version 1.0, 11.12.2008 (Switched to the new input filter concept)<br />
      */
      function start($namespace,$template){

         // set URLRewrite
         $reg = &Singleton::getInstance('Registry');
         $urlRewriting = $reg->retrieve('apf::core','URLRewriting');

         // check if the context is set. If not, use the current namespace
         if(empty($this->__Context)){
            $this->__Context = $namespace;
          // end if
         }

         // apply front controller input filter
         $filterDef = $reg->retrieve('apf::core::filter','FrontControllerInputFilter');

         if($filterDef !== null){
            $inputFilter = FilterFactory::getFilter($filterDef);
            $inputFilter->filter(null);
          // end if
         }

         // execute pre page create actions (see timing model)
         $this->__runActions('prepagecreate');

         // create new page
         $Page = new Page('FrontControllerPage',$urlRewriting);

         // set context
         $Page->set('Context',$this->__Context);

         // set language
         $Page->set('Language',$this->__Language);

         // load desired design
         $Page->loadDesign($namespace,$template);

         // execute actions after page creation (see timing model)
         $this->__runActions('postpagecreate');

         // execute actions before transformation (see timing model)
         $this->__runActions('pretransform');

         // transform page
         $pageContent = $Page->transform();

         // execute actions after page transformation (see timing model)
         $this->__runActions('posttransform');

         // display page content
         echo $pageContent;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Referenz auf eine Actions zur�ck.<br />
      *
      *  @param string $ActionName; Name der Action
      *  @return object $Action | bool NULL; Die Action oder null
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.02.2007<br />
      *  Version 0.2, 08.02.2007 (Noch nicht geladene Actions werden lazy nachgeladen und zur�ckgeliefert)<br />
      *  Version 0.3, 11.02.2007 (ActionName ist nun der Name der Section, bzw. des Alias der Action)<br />
      *  Version 0.4, 01.03.2007 (Input-Objekte werden nun vom Frontcontroller geladen!)<br />
      *  Version 0.5, 01.03.2007 (Pr�fung ob Action-Klasse vorhanden ist hinzugef�gt!)<br />
      *  Version 0.6, 08.03.2007 (Auf neuen configurationManager umgestellt)<br />
      *  Version 0.7, 08.06.2007 (Automatisches Neuerstellen einer Action entfernt)<br />
      *  Version 0.8, 08.11.2007 (Umstellung auf Hash-Offsets nachgezogen)<br />
      */
      function &getActionByName($ActionName){

         foreach($this->__Actions as $ActionHash => $DUMMY){

            // Pr�fen, ob Action mit dem �bergebenen Namen existiert
            if($this->__Actions[$ActionHash]->get('ActionName') == $ActionName){
               return $this->__Actions[$ActionHash];
             // end if
            }

          // end foreach
         }


         // return null, if action could not be found
         $null = null;
         return $null;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Referenz auf eine Actions zur�ck.<br />
      *
      *  @return array $Actions; Array mit Action-Objekten
      *
      *  @author Christian Sch�fer
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
      *  Erzeugt aus einem URL-Namespace einen regul�ren Namespace.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 03.06.2007<br />
      */
      protected function __getActionNamespaceByURLString($NamespaceString){
         return str_replace($this->__NamespaceURLDelimiter,'::',$NamespaceString);
       // end function
      }


      /**
      *  @public
      *
      *  Registriert eine Action beim FC und l�d die Parameter des Models aus einem Config-File.<br />
      *  Erwartet eine Konfigurationsdatei mit Namen {APPS__ENVIRONMENT}_actionsconfig.ini unter<br />
      *  dem Pfad {$ActionNamespace}::actions::{$this->__Context}.<br />
      *
      *  @param string $ActionNamespace; Namespace der Action
      *  @param string $ActionName; Name der Action
      *  @param array $ActionParams; (Input-)Parameter der Action
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.06.2007<br />
      *  Version 0.2, 01.07.2007 (ActionNamespace wird nun zentral in addAction() �bersetzt)<br />
      *  Version 0.3, 01.07.2007 (Parsen der Config-Parameter wird nun korrekt durchgef�hrt)<br />
      */
      function registerAction($ActionNamespace,$ActionName,$ActionParams = array()){

         // Config f�r Input laden
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

                     // Paar zu den ActionParams hinzuf�gen
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

         // Action hinzuf�gen
         $this->addAction($ActionNamespace,$ActionName,$ActionParams);

       // end function
      }



      /**
      *  @public
      *
      *  F�gt eine Action hinzu. Erwartet eine Konfigurationsdatei mit Namen <br />
      *  {ENVIRONMENT}_actionsconfig.ini unter dem Pfad {$ActionNamespace}::actions::{$this->__Context}.<br />
      *
      *  @param string $ActionNamespace; Namespace der Action
      *  @param string $ActionName; Name der Action
      *  @param array $ActionParams; (Input-)Parameter der Action
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 05.06.2007<br />
      *  Version 0.2, 01.07.2007 (Action-Konfiguration wird nun auch unter "{Namespace}::actions::{Context}" erwartet)<br />
      *  Version 0.3, 02.09.2007 (Fehlermeldung erweitert)<br />
      *  Version 0.4, 08.09.2007 (Input-Parameter aus Config werden jetz beachtet)<br />
      *  Version 0.5, 08.11.2007 (Umstellung auf Hash-Offsets f�r Actions)<br />
      *  Version 0.2, 21.06.2008 (Replaced APPS__ENVIRONMENT with a value from the Registry)<br />
      */
      function addAction($ActionNamespace,$ActionName,$ActionParams = array()){

         // Namespace umwandeln
         $ActionNamespace = $this->__getActionNamespaceByURLString($ActionNamespace);
         $ActionNamespace .= '::actions';

         // Action-Config laden
         $CfgObj = &$this->__getConfiguration($ActionNamespace,'actionconfig');


         // Pr�fen ob Konfiguration existent ist
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


         // Pr�fen, ob Action-Klasse vorhanden ist
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


         // Context �bergeben
         $Action->set('Context',$this->__Context);


         // Sprache �bergeben
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
      *  Erzeugt ein Array aus dem Konfigurationswert f�r Standard-Input-Parameter.<br />
      *
      *  @param string $InputConfig; Konfigurations-String
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 08.09.2007<br />
      */
      protected function __generateParamsFromInputConfig($InputConfig = ''){

         // R�ckgabe-Array initialisieren
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


         // Input-Parameter zur�ckgeben
         return $InputParams;

       // end function
      }


      /**
      *  @private
      *
      *  F�hrt diejenigen Actions aus, die dem �bergebenen Typ entsprechen.<br />
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
      protected function __runActions($Type = 'prepagecreate'){

         // BenchmarkTimer holen
         $T = &Singleton::getInstance('BenchmarkTimer');

         // Actions ausf�hren
         foreach($this->__Actions as $ActionHash => $DUMMY){

            // Action ausf�hren, wenn Typ passt
            if($this->__Actions[$ActionHash]->get('Type') == $Type){

               // Timer starten
               $ID = get_class($this->__Actions[$ActionHash]).'::run()';
               $T->start($ID);


               // Action ausf�hren
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