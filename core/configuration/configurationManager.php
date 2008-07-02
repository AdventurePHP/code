<?php
   /**
   *  @package core::configuration
   *  @class Configuration
   *
   *  Represents a configuration object, that is loaded by the configurationManager. It stores
   *  section or subsections and their corresponding values.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.01.2007<br />
   */
   class Configuration
   {

      /**
      *  @private
      *  Container for configuration entries.
      */
      var $__Configuration = array();


      function Configuration(){
      }


      /**
      *  @public
      *
      *  Returns a configuration section as an associative array.
      *
      *  @param string $Name; Name of the cection
      *  @return array $Section | null; Section or null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function getSection($Name){

         if(isset($this->__Configuration[$Name])){
            return $this->__Configuration[$Name];
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
      *  Returns a configuration sub section as an associative array.
      *
      *  @param string $Section; Name der Section
      *  @param string $Name; Name der Subsection
      *  @return array $Value | null; Wert (Array) der Konfigurations-Variable oder null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 30.01.2007<br />
      *  Version 0.2, 31.01.2007 (Subsection wird auf array geprüft)<br />
      *  Version 0.3, 16.11.2007 (trim() bei return entfernt, da es keinen Sinn macht)<br />
      */
      function getSubSection($Section,$Name){

         if(is_array($this->__Configuration[$Section][$Name])){
            return $this->__Configuration[$Section][$Name];
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
      *  Returns a configuration value by section and attribute name.
      *
      *  @param string $Section; Name der Section
      *  @param string $Name; Name der Konfigurations-Variable
      *  @return string $Value | null; Wert der Konfigurations-Variable
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function getValue($Section,$Name){

         if(isset($this->__Configuration[$Section][$Name])){
            return $this->__Configuration[$Section][$Name];
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
      *  Fills the inetrnal configuratin container with the configuration content.
      *
      *  @param array $Array; Konfigurations-Array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function setConfiguration($Array){
         $this->__Configuration = $Array;
       // end function
      }


      /**
      *  @public
      *
      *  Returns the entire configuration as an associative array.
      *
      *  @return array $Array; Konfigurations-Array
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.02.2007<br />
      */
      function getConfiguration(){
         return $this->__Configuration;
       // end function
      }

    // end class
   }


   /**
   *  @package core::configuration
   *  @class configurationManager
   *
   *  The configurationManager represents a configuration utility, that loads and handles configurations
   *  that depend on the context and the environment tzhe application or module is executed in. The
   *  manager must be instanciated singleton!
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.01.2007<br />
   *  Version 0.2, 07.03.2007 (Kompletter Umbau wegen Strukturänderung (Context!!!)<br />
   *  Version 0.3, 02.04.2007 (Abschließend getestet und Feinheiten optimiert)<br />
   *  Version 0.4, 16.11.2007 (Definition von $__NamespaceDelimiter wiederhergestellt)<br />
   *  Version 0.5, 21.06.2008 (Introduced Registry to get the current Environment string)<br />
   */
   class configurationManager extends coreObject
   {

      /**
      *  @private
      *  Hält die geladenen Konfigurationen vor.
      */
      var $__Configurations = array();


      /**
      *  @private
      *  Trennzeichen für SubConfigurationen.
      */
      var $__NamespaceDelimiter = '.';


      function configurationManager(){
      }


      /**
      *  @public
      *
      *  Läd eine Konfiguration, die durch die angegebenen Parameter beschrieben ist. Konfigurationen<br />
      *  werden immer unter den Namespace config::* abgelegt.<br />
      *  <br />
      *  $oCfgMgr = &Singleton::getInstance('configurationManager');<br />
      *  $oCfg = &$oCfgMgr->getConfiguration('sites::weiterbildungsveranstaltung','actions','permanentactions');<br />
      *
      *  @param string $Namespace; Namespace, unter dem die Konfiguration liegt (wird intern mit config::* gepräfixt)
      *  @param string $Context; Context der Konfigurations-Datei
      *  @param string $ConfigName; Name der Konfiguratons-Datei
      *  @param bool $ParseSubsections; true | false. Bestimmt ob Subsections in eigene Array-Offsets abgelegt werden
      *  @return object $CfgObj | bool NULL; Konfigurations-Objekt oder NULL
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 07.03.2007<br />
      *  Version 0.2, 02.04.2007 (Wenn Config nicht geladen werden kann, wird ein ERROR ausgegeben)<br />
      *  Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
      */
      function &getConfiguration($Namespace,$Context,$ConfigName,$ParseSubsections = false){

         // ConfigHash errechnen
         $ConfigHash = md5($Namespace.$Context.$ConfigName);

         // Prüfen, ob Config vorhanden ist
         if($this->configurationExists($Namespace,$Context,$ConfigName) == true){

            // Prüfen, ob Config schon geladen wurde
            if(!isset($this->__Configurations[$ConfigHash])){

               // Konfiguration laden
               $Configuration = $this->__loadConfiguration($Namespace,$Context,$ConfigName);


               // Konfiguration parsen
               $CfgObj = new Configuration();

               if($ParseSubsections == true){
                  $CfgObj->setConfiguration($this->__parseConfiguration($Configuration));
                // end if
               }
               else{
                  $CfgObj->setConfiguration($Configuration);
                // end else
               }

               // Konfiguration in HashTable einsetzen
               $this->__Configurations[$ConfigHash] = $CfgObj;

             // end if
            }

          // end if
         }
         else{

            // retrieve environment configuration from Registry
            $Reg = &Singleton::getInstance('Registry');
            $Environment = $Reg->retrieve('apf::core','Environment');

            // trigger error
            trigger_error('[configurationManager->getConfiguration()] Requested configuration with name "'.$Environment.'_'.$ConfigName.'.ini" cannot be loaded from namespace "'.$Namespace.'" with context "'.$Context.'"!',E_USER_ERROR);
            exit();

          // end else
         }


         // Konfiguration zurückgeben
         return $this->__Configurations[$ConfigHash];

       // end function
      }


      /**
      *  @public
      *
      *  Prüft, ob eine Konfigurations-Datei angelegt wurde<br />
      *
      *  @param string $Namespace; Namespace der Konfigurations-Datei
      *  @param string $Context; Context der Applikation
      *  @param string $ConfigName; Name der Konfigurations-Datei
      *  @return bool $ConfigurationExistent; true | false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 03.02.2007<br />
      *  Version 0.2, 07.03.2007 (In "configurationExists" umgenannt)<br />
      */
      function configurationExists($Namespace,$Context,$ConfigName){

         // Prüfen, ob Config-File vorhanden ist
         if(file_exists($this->__getConfigurationFileName($Namespace,$Context,$ConfigName))){
            return true;
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @private
      *
      *  Läd eine Konfigurations-Datei. Diese muss unterhalb des Ordners <em>config</em> liegen.<br />
      *
      *  @param string $Namespace; Namespace der Konfigurations-Datei
      *  @param string $Context; Context der Applikation
      *  @param string $ConfigName; Name der Konfigurations-Datei
      *  @return array $Configuration | null; Konfigurations-Array, oder null
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      *  Version 0.2, 03.02.2007 (Generierung des Dateinames ausgelagert)<br />
      *  Version 0.3, 07.03.2007<br />
      *  Version 0.4, 02.04.2007 (else-Zweig entfernt, da Config immer existiert, wenn Methode aufgerufen wird)<br />
      */
      function __loadConfiguration($Namespace,$Context,$ConfigName){

         // Name des ConfigFiles ziehen
         $ConfigFile = $this->__getConfigurationFileName($Namespace,$Context,$ConfigName);

         // Konfiguration parsen
         return parse_ini_file($ConfigFile,true);

       // end function
      }


      /**
      *  @private
      *
      *  Setzt den ConfigFileName aus Namespace und ConfigName zusammen.<br />
      *
      *  @param string $Namespace; Namespace der Konfigurations-Datei
      *  @param string $Context; Context der Applikation
      *  @param string $ConfigName; Name der Konfigurations-Datei
      *  @return string $ConfigurationFileName; Name der Konfigurations-Datei
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 03.02.2007<br />
      *  Version 0.2, 07.03.2007<br />
      *  Version 0.3, 21.06.2008 (Introduced the Registry component)<br />
      */
      function __getConfigurationFileName($Namespace,$Context,$ConfigName){

         // generate namespace and context path
         if(strlen($Context) > 0){
            $Path = str_replace('::','/',$Namespace).'/'.str_replace('::','/',$Context);
          // end if
         }
         else{
            $Path = str_replace('::','/',$Namespace);
          // end else
         }

         // retrieve environment configuration from Registry
         $Reg = &Singleton::getInstance('Registry');
         $Environment = $Reg->retrieve('apf::core','Environment');

         // return configuration file name
         return APPS__PATH.'/config/'.$Path.'/'.$Environment.'_'.$ConfigName.'.ini';

       // end function
      }


      /**
      *  @private
      *
      *  Läd eine Konfigurations-Datei. Diese muss unterhalb des Ordners <em>config</em> liegen.<br />
      *
      *  @param array $Configuration; Array, das mit Hilfe von "parse_ini_file" aus einer ini-Datei erstellt wurde.<br />
      *  @return object $ConfigurationObject; Konfigurations-Objekt vom Typ "Configuration"
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function __parseConfiguration($Configuration = array()){

         // Rückgabe-Array initialisieren
         $ConfigurationArray = array();


         // Offsets des Arrays durchgehen
         foreach($Configuration as $Key => $Value){

            // Falls Value ein Array ist, dann dieses Parsen
            if(is_array($Value)){
               $ConfigurationArray[$Key] = $this->__parseSubsections($Value);
             // end if
            }
            else{
               $ConfigurationArray[$Key] = $Value;
             // end else
            }

          // end foreach
         }

         // Config-Array zurückgeben
         return $ConfigurationArray;

       // end function
      }


      /**
      *  @private
      *
      *  Extrahiert aus einem Array die Sub-Section-Angaben.<br />
      *
      *  @param array $SubsectionArray; Konfigurations-Array mit Subsections
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function __parseSubsections($SubsectionArray){

         // Konkatiniertes Array initialisieren
         $ConcatenatedArray = array();

         // Prüfen, ob Übergabewert ein Array ist
         if(is_array($SubsectionArray)){

            // Alle Offsets des Arrays durchgehen
            foreach($SubsectionArray as $Key => $Value){
               $ConcatenatedArray = array_merge_recursive($ConcatenatedArray,$this->__generateSubArray($Key,$Value));
             // end foreach
            }

          // end if
         }
         else{
            trigger_error('[configurationManager::__parseSubsections()] Given value is not an array!',E_USER_ERROR);
          // end else
         }

         return $ConcatenatedArray;

       // end function
      }


      /**
      *  @private
      *
      *  Generiert aus einem Subsection-String Unterarrays.<br />
      *
      *  @param string $Key; Array-Schlüssel
      *  @param array|string $Value; Wert des durch $Key definierten Offsets
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 28.01.2007<br />
      */
      function __generateSubArray($Key,$Value){

         // Rückgabe-Array initialisieren
         $SubArray = array();

         // Nachsehen, ob ein Punkt in einem Key enthalten ist
         if(substr_count($Key,$this->__NamespaceDelimiter) > 0){

            // Position des Punktes bestimmen
            $DelPos = strpos($Key,$this->__NamespaceDelimiter);

            // Neuen Offset extrahieren
            $Offset = substr($Key,0,$DelPos);

            // Rest-Offset erzeugen
            $RemainingString = substr($Key,$DelPos + strlen($this->__NamespaceDelimiter),strlen($Key));

            // Neues Array generieren
            $SubArray[$Offset] = $this->__generateSubArray($RemainingString,$Value);

          // end if
         }
         else{
            $SubArray[$Key] = $Value;
          // end els
         }

         // Subarray zurückgeben
         return $SubArray;

       // end function
      }

    // end class
   }
?>