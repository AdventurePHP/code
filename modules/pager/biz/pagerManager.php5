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

   import('tools::link','frontcontrollerLinkHandler');
   import('tools::variablen','variablenHandler');
   import('modules::pager::biz','pageObject');
   import('modules::pager::data','pagerMapper');


   /**
   *  @namespace modules::pager::biz
   *  @class pagerManagerFabric
   *
   *  Implementierung der Pager-Fabric.<br />
   *
   *  <strong>Anwendungsbeispiel:<strong>
   *  <br />
   *  $pMF = &$this->__getServiceObject('modules::pager::biz','pagerManagerFabric');
   *  $pM = &$pMF->getPagerManager('{ConfigSection}',{AdditionlParamArray});
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 13.04.2007<br />
   */
   class pagerManagerFabric extends coreObject
   {

      /**
      *  @private
      *  Array mit allen aktuell erhältlichen Pagern
      */
      var $__Pager = array();


      function pagerManagerFabric(){
      }


      /**
      *  @public
      *
      *  Gibt eine Referenz auf einen pagerManager zurück.<br />
      *
      *  @param string $configString; Konfigurations-String
      *  @param array $AddParams; Zusätzliche Anwendungs-Parameter der Anwendung
      *  @return pagerManager $pagerManager; Referenz auf den gewünschten pagerManager
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 13.04.2007<br />
      */
      function &getPagerManager($configString,$AddParams = array()){

         // Hash errechnen
         $PagerHash = md5($configString.'_'.implode('_',$AddParams));

         if(!isset($this->__Pager[$PagerHash])){

             // pagerManager erzeugen (ServiceObject-Modell)
             $pgrMgr = new pagerManager();

             // Mit zusätzlichen Parametern initialisieren
             $pgrMgr->set('Context',$this->__Context);
             $pgrMgr->set('Language',$this->__Language);
             $pgrMgr->init($configString,$AddParams);

             // Lokal cachen
             $this->__Pager[$PagerHash] = $pgrMgr;

          // end if
         }


         // Pager zurückgeben
         return $this->__Pager[$PagerHash];

       // end function
      }

    // end class
   }


   /**
   *  @namespace modules::pager::biz
   *  @class pagerManager
   *
   *  Repräsentiert die Business-Schicht der Pagers. Implementiert den Pager im Frontcontroller-Stil.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   *  Version 0.2, 14.08.2006 (Klassen-Variablen sauber deklariert)<br />
   *  Version 0.3, 16.08.2006 (Erweiterte Konfiguration für Statements hinzugefügt)<br />
   *  Version 0.4, 13.04.2007 (Erweitert, damit auch Parameter aus der Anwendung an den pagerManager gegeben werden können)<br />
   */
   class pagerManager extends coreObject
   {

      /**
      *  @private
      *  Hält lokal verwendete Variablen.
      */
      var $_LOCALS;


      /**
      *  @private
      *  Konfigurations-Name des Pagers.
      */
      var $__config_Name = 'pager';


      /**
      *  @private
      *  Konfigurations-Abschnitt.
      */
      var $__config_Section = 'Standard';


      /**
      *  @private
      *  Standard für Einträge pro Seite.
      */
      var $__config_EntriesPerPage;


      /**
      *  @private
      *  URL-Name für Start-Anzahl.
      */
      var $__config_ParameterStartName;


      /**
      *  @private
      *  URL-Name für die Anzahl.
      */
      var $__config_ParameterCountName;


      /**
      *  @private
      *  Statement-Namespace.
      */
      var $__config_StatementNamespace;


      /**
      *  @private
      *  Name des Count-Stataments.
      */
      var $__config_CountStatement;


      /**
      *  @private
      *  Parameter des Count-Statements.
      */
      var $__config_CountStatement_Params;


      /**
      *  @private
      *  Statement für die IDs der Datensätze.
      */
      var $__config_EntriesStatement;


      /**
      *  @private
      *  Parameter für das ID-Statement.
      */
      var $__config_EntriesStatement_Params;


      /**
      *  @private
      *  Namespace des Templates.
      */
      var $__config_DesignNamespace;


      /**
      *  @private
      *  Name des Templates.
      */
      var $__config_DesignTemplate;


      /**
      *  @private
      *  @since 0.4
      *  Statement-Parameter.
      */
      var $__pager_StatamentParams;


      /**
      *  @private
      *  Start-Nummer.
      */
      var $__pager_Start;


      /**
      *  @private
      *  Anzahl der Einträge.
      */
      var $__pager_EntriesCount;


      /**
      *  @private
      *  Aktuelle Seite (ULR).
      */
      var $__pager_Site;


      /**
      *  @private
      *  Prüfzahl, on Pager initialisiert wurde
      */
      var $__pager_IsInitialized = false;


      /**
      *  @private
      *  Optionaler Anker-Name.
      */
      var $__AnchorName = null;


      function pagerManager(){
      }


      /**
      *  @public
      *
      *  Setzt die verwendete Konfigurations-Sektion und initialisiert den Pager.<br />
      *
      *  @param string | array $initParam; Konfigurations-Abschnitt oder Array mit Konfigurations-Abschnitt und zusätzlichen Parametern
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 06.08.2006<br />
      *  Version 0.2, 16.08.2006 (Erweiterte Parameter-Konfigurations-Möglichkeit für Anzahl-Statement implementiert)<br />
      *  Version 0.3, 29.03.2007 (Umbenannt in "init()" um serviceManager zu unterstützen)<br />
      *  Version 0.4, 30.03.2007 (iniHandler bereinigt)<br />
      *  Version 0.5, 13.04.2007 (Methode erweitert, damit der pagerManager auch über die Fabric instanziiert werden kann)<br />
      *  Version 0.6, 26.04.2008 (Statement-Parameter "Start" und "EntriesCount" werden nun auf int gecastet)<br />
      */
      function init($configSection,$AddParams = array()){

         if($this->__pager_IsInitialized == false){

            // Config einlesen
            $this->__config_Section = $configSection;
            $Config = &$this->__getConfiguration('modules::pager',$this->__config_Name);

            $this->__config_EntriesPerPage = $Config->getValue($this->__config_Section,'Pager.EntriesPerPage');

            $this->__config_ParameterStartName = $Config->getValue($this->__config_Section,'Pager.ParameterStartName');
            $this->__config_ParameterCountName = $Config->getValue($this->__config_Section,'Pager.ParameterCountName');

            $this->__config_StatementNamespace = $Config->getValue($this->__config_Section,'Pager.StatementNamespace');

            $this->__config_CountStatement = $Config->getValue($this->__config_Section,'Pager.CountStatement');
            $this->__config_CountStatement_Params = $Config->getValue($this->__config_Section,'Pager.CountStatement.Params');

            $this->__config_EntriesStatement = $Config->getValue($this->__config_Section,'Pager.EntriesStatement');
            $this->__config_EntriesStatement_Params = $Config->getValue($this->__config_Section,'Pager.EntriesStatement.Params');

            $this->__config_DesignNamespace = $Config->getValue($this->__config_Section,'Pager.DesignNamespace');
            $this->__config_DesignTemplate = $Config->getValue($this->__config_Section,'Pager.DesignTemplate');


            // Config für URI-Parameter ziehen
            $this->_LOCALS = variablenHandler::registerLocal(array(
                                                                   $this->__config_ParameterStartName => 0,
                                                                   $this->__config_ParameterCountName => $this->__config_EntriesPerPage
                                                                  )
                                                            );


            // Statement-Parameter initialisieren (Cast erzwingen!)
            $params = array(
                            'Start' => (int)$this->_LOCALS[$this->__config_ParameterStartName],
                            'EntriesCount' => (int)$this->_LOCALS[$this->__config_ParameterCountName]
                           );


            // Erweiterte Parameter setzen ($AddParams sticht!)
            $this->__pager_StatamentParams = array_merge($params,$this->__generateStatementParams($this->__config_EntriesStatement_Params),$AddParams);


            // Mapper holen
            $pM = &$this->__getServiceObject('modules::pager::data','pagerMapper');


            // Start, Count und Site initialisieren
            $this->__pager_Start = $this->_LOCALS[$this->__config_ParameterStartName];
            $this->__pager_EntriesCount = $pM->getEntriesCountValue($this->__config_StatementNamespace,$this->__config_CountStatement,$this->__pager_StatamentParams);
            $this->__pager_Site = $_SERVER['REQUEST_URI'];


            // Pager als initialisiert kennzeichnen
            $this->__pager_IsInitialized = true;

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Setzt einen Anker-Link-Namen, der bei der Ausgabe des Pagers mit eingebunden wird,<br />
      *  falls dieser > 3 Zeichen ist.<br />
      *
      *  @param string $AnchorName; Anker-Name
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 29.08.2007<br />
      */
      function setAnchorName($AnchorName = ''){

         if(strlen($AnchorName) >= 3){
            $this->__AnchorName = $AnchorName;
          // end if
         }
         else{
            trigger_error('[pagerManager::setAnchor()] Given anchor name is too short. It must have a minimum length of tree or more letters!',E_USER_WARNING);
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Läd die aktuellen IDs, die auf der aktuellen Seite angezeigt werden sollen.<br />
      *
      *  @return array $Entries | array(); Array mit den IDs, für die aktuelle Seite oder leeres Array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 06.08.2006<br />
      *  Version 0.3, 16.08.2006 (Erweiterte Parameter-Konfigurations-Möglichkeit für Lade-Statement implementiert)<br />
      */
      function loadEntries(){

         if($this->__pager_IsInitialized){

            // ID's der Einträge zurückliefern
            $M = &$this->__getServiceObject('modules::pager::data','pagerMapper');
            return $M->loadEntries($this->__config_StatementNamespace,$this->__config_EntriesStatement,$this->__pager_StatamentParams);

          // end if
         }
         else{
            trigger_error('[pagerManager->loadEntries()] Pager is not initialized!');
            return array();
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Läd die aktuell geforderten Domain-Objekte, die auf der aktuellen Seite angezeigt werden sollen.<br />
      *
      *  @param object $DataComponent Instanz der Datenkomponente der Anwendung
      *  @param string $LoadMethod Name der Objekt-Lade-Methode der Datenkomponente
      *  @return array object $Entries | array(); Array mit den Domain-Objekten der Anwenung oder leeres Array
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 01.09.2007<br />
      *  Version 0.2, 18.09.2007 (Überprüfung der Methode für PHP 4 kompatibel gemacht)<br />
      */
      function loadEntriesByAppDataComponent(&$DataComponent,$LoadMethod){

         if($this->__pager_IsInitialized){

            // ID's der Einträge selektieren
            $M = &$this->__getServiceObject('modules::pager::data','pagerMapper');
            $EntryIDs = $M->loadEntries($this->__config_StatementNamespace,$this->__config_EntriesStatement,$this->__pager_StatamentParams);

            // Prüfen, ob gegebene Daten-Komponente korrekt übergeben wurde
            if(in_array($LoadMethod,get_class_methods($DataComponent))){

               // Einträge mit Hilfe der Instanz der Daten-Komponente laden
               $Entries = array();
               for($i = 0; $i < count($EntryIDs); $i++){
                  $Entries[] = $DataComponent->{$LoadMethod}($EntryIDs[$i]);
                // end for
               }

               // Ergebnis-Liste zurückgenen
               return $Entries;

             // end if
            }
            else{
               trigger_error('[pagerManager->loadEntriesByAppDataComponent()] Given data component ('.get_class($DataComponent).') has no method "'.$LoadMethod.'"! No entries can be loaded!',E_USER_WARNING);
               return array();
             // end else
            }

          // end if
         }
         else{
            trigger_error('[pagerManager->loadEntriesByAppDataComponent()] Pager is not initialized!',E_USER_WARNING);
            return array();
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt einen Pager (grafische Ausgabe) und gibt den HTML-Code zurück.<br />
      *
      *  @return string $Pager; HTML-Ausgabe des Pagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 11.03.2007 (Auf neuen PageController umgestellt, MessageQueue entfernt)<br />
      *  Version 0.3, 29.08.2007 (Anker-Name wird nun als Attribut der Seite gesetzt)<br />
      *  Version 0.4, 02.03.2008 (Page wird nun mit der aktuellen Sprache und Context ausgestattet)<br />
      */
      function getPager(){

         if($this->__pager_IsInitialized){

            // Neue Pager-Ausgabe erzeugen
            $Pager = new Page('Pager',false);

            // Spache und Context setzen
            $Pager->set('Language',$this->__Language);
            $Pager->set('Context',$this->__Context);

            // Design laden
            $Pager->loadDesign($this->__config_DesignNamespace,$this->__config_DesignTemplate);

            // Seiten an Document weitergeben
            $Document = &$Pager->getByReference('Document');
            $Document->setAttribute('Pages',$this->__createPages4PagerDisplay());
            $Document->setAttribute('Config',array('ParameterStartName' => $this->__config_ParameterStartName,
                                                   'ParameterCountName' => $this->__config_ParameterCountName,
                                                   'EntriesPerPage' => $this->__config_EntriesPerPage
                                                  )
                                   );
            if($this->__AnchorName != null){
               $Document->setAttribute('AnchorName',$this->__AnchorName);
             // end if
            }

            // Pager transformieren
            return $Pager->transform();

          // end if
         }
         else{
            trigger_error('[pagerManager->getPager()] Pager is not initialized!',E_USER_WARNING);
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die URL-Parameter des Pagers zurück.<br />
      *
      *  @return array $URLParams; Array der URL-Parameter des Pagers
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 17.03.2007<br />
      */
      function getPagerURLParameters(){

         return array(
                      'StartName' => $this->__config_ParameterStartName,
                      'CountName' => $this->__config_ParameterCountName
                      );

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt Seitenobjekte für die Anzeige.<br />
      *
      *  @return array $PagerObjects; Array von pageObject's
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 06.08.2006<br />
      *  Version 0.3, 14.08.2006 (Globale Konfiguration für URL-Rewriting hinzugefügt)<br />
      *  Version 0.4, 16.11.2007 (Auf frontcontrollerLinkHandler umgestellt)<br />
      *  Version 0.5, 26.04.2008 (Division by zero vermeiden)<br />
      */
      function __createPages4PagerDisplay(){

         $PagerObjekte = array();
         $Start = 0;

         // Division by zero vermeiden
         if((int)$this->_LOCALS[$this->__config_ParameterCountName] == 0){
            $this->_LOCALS[$this->__config_ParameterCountName] = 1;
          // end if
         }
         $PagerAnzahlSeiten = ceil($this->__pager_EntriesCount / $this->_LOCALS[$this->__config_ParameterCountName]);

         for($i = 0; $i < $PagerAnzahlSeiten; $i++){

            // Seiten-Objekt erzeugen
            $PagerObjekte[$i] = new pageObject();

            // Link generieren
            $Link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__config_ParameterStartName => $Start));
            $PagerObjekte[$i]->set('Link',$Link);

            // Name der Seite einsetzen
            $PagerObjekte[$i]->set('Page',$i+1);

            // Selektierte Seite markieren
            if($Start == $this->_LOCALS[$this->__config_ParameterStartName]){
               $PagerObjekte[$i]->set('isSelected',true);
             // end if
            }

            // Anzahl der Einträge einsetzen
            $PagerObjekte[$i]->set('entriesCount',$this->__pager_EntriesCount);

            // Anzahl der Seiten einsetzen
            $PagerObjekte[$i]->set('pageCount',$PagerAnzahlSeiten);


            // Startpunkt inkrementieren
            $Start = $Start + $this->_LOCALS[$this->__config_ParameterCountName];

          // end for
         }

         return $PagerObjekte;

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt ein Parameter-Array für das Ausführen eines SQL-Statements aus einem Konfigurations-String.<br />
      *  Gibt ein leeres Array zurück, dass der Config-String leer ist, oder das Parameter-Array, das sowohl<br />
      *  aus dem Request als auch aus Standard-Werten zusammengesetzt ist zurück.<br />
      *
      *  @param string $configString; Konfigurations-Zeichenkette
      *  @return array $StmtParams; Array der Statement-Parameter
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.08.2006<br />
      */
      function __generateStatementParams($configString){

         if(!empty($configString)){

            $params = explode('|',$configString);

            $stmtparams = array();

            for($i = 0; $i < count($params); $i++){

               if(substr_count($params[$i],':')){

                  // Offset in Name und Standardwert trennen
                  $temp = explode(':',$params[$i]);

                  // Variable lokal registrieren
                  $locals = variablenHandler::registerLocal(array(trim($temp[0]) => trim($temp[1])));

                  // Lokale Variablen mergen
                  $stmtparams = array_merge($stmtparams,$locals);

                // end if
               }
               else{

                  // Variable lokal registrieren
                  $locals = variablenHandler::registerLocal(array(trim($params[$i])));

                  // Lokale Variablen mergen
                  $stmtparams = array_merge($stmtparams,$locals);

                // end else
               }

             // end for
            }

          // end if
         }
         else{
            $stmtparams = array();
          // end else
         }

         // Statement-Parameter-Array zurückgeben
         return $stmtparams;

       // end function
      }

    // end class
   }
?>