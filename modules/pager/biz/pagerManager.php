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
   import('tools::request','RequestHandler');
   import('modules::pager::biz','PagerPage');
   import('modules::pager::data','PagerMapper');


   /**
   *  @namespace modules::pager::biz
   *  @class PagerManagerFabric
   *
   *  Implements the factory of the pager manager.
   *
   *  Application sample:
   *  <pre>$pMF = &$this->__getServiceObject('modules::pager::biz','PagerManagerFabric');
   *  $pM = &$pMF->getPagerManager('{ConfigSection}',{AdditionlParamArray});</pre>
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 13.04.2007<br />
   */
   class PagerManagerFabric extends coreObject
   {

      /**
      *  @private
      *  Cache list if concrete pager manager instances.
      */
      var $__Pager = array();


      function PagerManagerFabric(){
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

         // create hash key
         $pagerHash = md5($configString.'_'.implode('_',$AddParams));

         if(!isset($this->__Pager[$pagerHash])){

             // pagerManager erzeugen (ServiceObject-Modell)
             $this->__Pager[$pagerHash] = &$this->__getServiceObject('modules::pager::biz','PagerManager','NORMAL');

             // initialize with special parame
             $this->__Pager[$pagerHash]->init($configString,$AddParams);

          // end if
         }


         // return desired pager reference
         return $this->__Pager[$pagerHash];

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
   class PagerManager extends coreObject
   {

      /**
      *  @private
      *  Configuration file name of the pager.
      */
      var $__Name = 'pager';


      /**
      *  @private
      *  @since 0.5
      *  Contains the current configuration.
      */
      var $__PagerConfig = null;


      /**
      *  @private
      *  Contains the statement params.
      */
      var $__StatementParams = null;


      /**
      *  @private
      *  Start page number.
      */
      var $__Start;


      /**
      *  @private
      *  Number of total entries.
      */
      var $__EntriesCount;


      /**
      *  @private
      *  Indicates, if the pager was already initialized.
      */
      var $__IsInitialized = false;


      /**
      *  @private
      *  Contains the name of the oprional anchor.
      */
      var $__AnchorName = null;


      function PagerManager(){
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
      function init($configSection,$addParams = array()){

         if($this->__IsInitialized == false){

            // read the config
            $Config = &$this->__getConfiguration('modules::pager',$this->__Name);
            $this->__PagerConfig = $Config->getSection($configSection);

            // initialize the statement params (for cast!)
            $params = array(
                            'Start' => (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterStartName'],0),
                            'EntriesCount' => (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterCountName'],$this->__PagerConfig['Pager.EntriesPerPage'])
                           );

             // create the mapper instance
            $pM = &$this->__getAndInitServiceObject('modules::pager::data','pagerMapper',$this->__PagerConfig['Pager.DatabaseConnection']);

            // enhance the statement params ($AddParams overwrites given values!)
            $this->__StatementParams = array_merge($params,$this->__generateStatementParams($this->__PagerConfig['Pager.EntriesStatement.Params']),$addParams);

            // initialize start, count and site
            $this->__Start = RequestHandler::getValue($this->__PagerConfig['Pager.ParameterStartName']);
            $this->__EntriesCount = $pM->getEntriesCountValue($this->__PagerConfig['Pager.StatementNamespace'],$this->__PagerConfig['Pager.CountStatement'],$this->__StatementParams);

            // mark pager as initialized
            $this->__IsInitialized = true;

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Sets the anchor name to add to the links. The string has to be greater than three signs.
      *
      *  @param string $anchorName name of the anchor to add to the links
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.08.2007<br />
      */
      function setAnchorName($anchorName = ''){

         if(strlen($anchorName) >= 3){
            $this->__AnchorName = $anchorName;
          // end if
         }
         else{
            trigger_error('[pagerManager::setAnchorName()] Given anchor name is too short. It must have a minimum length of tree or more letters!',E_USER_WARNING);
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

         if($this->__IsInitialized){

            // load the ids of the relevant entries
            $M = &$this->__getAndInitServiceObject('modules::pager::data','PagerMapper',$this->__PagerConfig['Pager.DatabaseConnection']);
            return $M->loadEntries($this->__PagerConfig['Pager.StatementNamespace'],$this->__PagerConfig['Pager.EntriesStatement'],$this->__StatementParams);

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

         if($this->__IsInitialized){

            // select the ids of the desired entries
            $M = &$this->__getAndInitServiceObject('modules::pager::data','PagerMapper',$this->__PagerConfig['Pager.DatabaseConnection']);
            $EntryIDs = $M->loadEntries($this->__PagerConfig['Pager.StatementNamespace'],$this->__PagerConfig['Pager.EntriesStatement'],$this->__StatementParams);

            // Prüfen, ob gegebene Daten-Komponente korrekt übergeben wurde
            if(in_array(strtolower($LoadMethod),get_class_methods($DataComponent))){

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

         if($this->__IsInitialized){

            // Neue Pager-Ausgabe erzeugen
            $Pager = new Page('Pager',false);

            // Spache und Context setzen
            $Pager->set('Language',$this->__Language);
            $Pager->set('Context',$this->__Context);

            // Design laden
            $Pager->loadDesign($this->__PagerConfig['Pager.DesignNamespace'],$this->__PagerConfig['Pager.DesignTemplate']);

            // Seiten an Document weitergeben
            $Document = &$Pager->getByReference('Document');
            $Document->setAttribute('Pages',$this->__createPages4PagerDisplay());
            $Document->setAttribute('Config',array('ParameterStartName' => $this->__PagerConfig['Pager.ParameterStartName'],
                                                   'ParameterCountName' => $this->__PagerConfig['Pager.ParameterCountName'],
                                                   'EntriesPerPage' => $this->__PagerConfig['Pager.EntriesPerPage']
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
                      'StartName' => $this->__PagerConfig['Pager.ParameterStartName'],
                      'CountName' => $this->__PagerConfig['Pager.ParameterCountName']
                      );

       // end function
      }


      /**
      *  @private
      *
      *  Creates a list of pager pages and returns it.
      *
      *  @return array $pages list of pages
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 05.08.2006<br />
      *  Version 0.2, 06.08.2006<br />
      *  Version 0.3, 14.08.2006 (Added a global configuration for url rewriting)<br />
      *  Version 0.4, 16.11.2007 (Switched to the frontcontrollerLinkHandler)<br />
      *  Version 0.5, 26.04.2008 (Avoid division by zero)<br />
      *  Version 0.6, 19.01.2009 (Changed the implementation due to refactoring)<br />
      */
      function __createPages4PagerDisplay(){

         // initialize some params
         $pages = array();
         $start = 0;

         // avoid division by zero
         $countPerPage = (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterCountName'],0);
         if($countPerPage === 0){
            $countPerPage = $this->__PagerConfig['Pager.EntriesPerPage'];
          // end if
         }
         $pageCount = ceil($this->__EntriesCount / $countPerPage);

         // create the page representation objects
         for($i = 0; $i < $pageCount; $i++){

            // create a new pager page object
            $pages[$i] = new PagerPage();

            // generate the link
            $link = frontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'],array($this->__PagerConfig['Pager.ParameterStartName'] => $start));
            $pages[$i]->set('Link',$link);

            // set the number of the page
            $pages[$i]->set('Page',$i + 1);

            // mark as selected
            $currentStart = (int)RequestHandler::getValue($this->__PagerConfig['Pager.ParameterStartName']);
            if($start === $currentStart){
               $pages[$i]->set('isSelected',true);
             // end if
            }

            // add the entries count
            $pages[$i]->set('entriesCount',$this->__EntriesCount);

            // add eth page count
            $pages[$i]->set('pageCount',$pageCount);

            // increment the start point
            $start = $start + $countPerPage;

          // end for
         }

         // return the list of pager pages
         return $pages;

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
                  $locals = RequestHandler::getValues(array(trim($temp[0]) => trim($temp[1])));

                  // Lokale Variablen mergen
                  $stmtparams = array_merge($stmtparams,$locals);

                // end if
               }
               else{

                  // Variable lokal registrieren
                  $locals = RequestHandler::getValues(array(trim($params[$i])));

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