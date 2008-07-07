<?php
   /**
   *  @package tools::cache
   *  @class abstractCacheManager
   *
   *  Implementiert den abstractCacheManager.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 22.05.2005<br />
   *  Version 0.2, 12.02.2006<br />
   *  Version 0.3, 26.02.2006 (Status ist nun konfigurierbar (aktiv/nicht aktiv))<br />
   *  Version 0.4, 27.03.2007 (Abstraktion von gemeinsam verwendeten Methoden)<br />
   */
   class abstractCacheManager extends coreObject
   {

      /**
      *  @private
      *  Basis-Ordner für die Ablage von Cache-Files.
      */
      var $__cacheFolder;


      /**
      *  @private
      *  Cache-Namespace.
      */
      var $__cacheNamespace;


      /**
      *  @private
      *  Indikator, ob Cache aktiv ist.
      */
      var $__cacheAktive;


      /**
      *  @private
      *  LifeTime eines CacheObjekts.
      */
      var $__cacheLifeTime;


      /**
      *  @private
      *  Name des CacheFiles.
      */
      var $__cacheFileName;


      /**
      *  @private
      *  Ordner-Rechte, mit denen Cache-Ordner angelegt werden.
      */
      var $__cacheFolderPermissions = 0777;


      function abstractCacheManager(){
      }


      /**
      *  @public
      *
      *  Initialisiert den CacheManager.<br />
      *
      *  @param string $ConfigSection; Konfigurations-Abschnitt für die Initialisierung
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 27.03.2007<br />
      *  Version 0.2, 28.03.2007 ("__generateCacheNamespace()" wird nun erst beim Schreiben ausgeführt)<br />
      */
      function initAbstractCacheManager($ConfigSection = 'standard'){

         // Konfiguration lesen
         $Config = &$this->__getConfiguration('tools::cache','cacheconfig');


         // Namespace prüfen
         if($Config->getSection($ConfigSection) == null){
            trigger_error('['.get_class($this).'->initAbstractCacheManager()] No configuration section found for name "'.$this->__cacheNamespace.'"!');
            $this->__cacheAktiv = false;
          // end if
         }
         else{

            // Konfiguration einlesen
            $this->__cacheFolder = $Config->getValue($ConfigSection,'Cache.BaseFolder');

            if($Config->getValue($ConfigSection,'Cache.Aktive') == 'true' || $Config->getValue($ConfigSection,'Cache.Aktive') == '1'){
               $this->__cacheAktive = true;
             // end if
            }
            else{
               $this->__cacheAktive = false;
             // end else
            }

            $this->__cacheLifeTime = $Config->getValue($ConfigSection,'Cache.LifeTime');
            $this->__cacheNamespace = $Config->getValue($ConfigSection,'Cache.Namespace');

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Prüft, ob ein Objekt im Cache vorhanden ist.<br />
      *
      *  @return bool $FileExists; true, falls Datei existiert, false, falls nicht
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.06.2006<br />
      *  Version 0.2, 27.03.2007<br />
      *  Version 0.3, 27.03.2007 (Cache-File wird bei POST-Request als nicht existent ausgegeben)<br /><
      */
      function cacheFileExists(){

         if($this->__cacheAktive == true){

            if(count($_POST) < 1){

               // Datei deklarieren
               $CacheFile = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$this->__cacheFileName;

               // Ausgeben, ob Cache-Datei existiert und noch gültig ist
               if(file_exists($CacheFile)){

                  // Gültigkeit der Cache-Datei erzeugen (Cache-Datei ist 60 Min gültig)
                  $CreationDate = filemtime($CacheFile);

                  // Status-Cache leeren
                  clearstatcache();

                  // Zeiten vergleichen
                  $CurrentDateMinusHalfHour = time() - (60 * $this->__cacheLifeTime); // Cache x Minute gültig
                  $Difference = $CreationDate - $CurrentDateMinusHalfHour;

                  // Zeiten auswerten
                  if($Difference > 0){
                     // Cache-Datei ist aktuell
                     return true;
                   // end if
                  }
                  else{
                     // Cache-Datei ist veraltet
                     return false;
                   // end else
                  }

                // end if
               }
               else{
                  return false;
                // end else
               }

             // end if
            }
            else{
               return false;
             // end else
            }

          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Schreibt ein Objekt in den Cache.<br />
      *
      *  @param void $Object; Cache-Objekt, das geschreiben werden soll
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2006<br />
      *  Version 0.2, 27.03.2007<br />
      */
      function writeToCache($Object){

         if($this->__cacheAktive == true){

            // Cache nur für GET-Requests verwenden
            if(count($_POST) < 1){

               $CacheFile = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$this->__cacheFileName;
               $fH = @fopen($CacheFile,'w+');
               @fwrite($fH,$Object);
               @fclose($fH);

             // end if
            }

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Liest Content aus dem Cache.<br />
      *
      *  @return void $CacheObject; Cache-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2006<br />
      *  Version 0.2, 27.03.2007<br />
      */
      function readFromCache(){

         // CacheFile deklarieren
         $CacheFile = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$this->__cacheFileName;

         // Falls Cachefile vorhanden ist, den Inhalte dieses zurückgeben
         if(file_exists($CacheFile)){
            $Content = file_get_contents($CacheFile);
            return trim($Content);
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
      *  Prüft, ob der Cache-Namespace vorhanden ist. Falls nicht, wird dieser erstellt.<br />
      *
      *  @author Christian Wiedemann
      *  @version
      *  Version 0.1, 31.10.2005 (ursprünglich: Funktion createFolderRecursive())<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.06.2006<br />
      *  Version 0.2, 14.08.2006 (Bug behoben, dass auf einem LINUX-System die Ordner nicht sauber angelegt werden)<br />
      *  Version 0.3, 01.10.2006 (Ergänzung eingepflegt, dass Ordner-Struktur sowohl auf LINUX als auch WINDOWS sauber angelegt werden)<br />
      */
      function __generateCacheNamespace(){

         $cacheFolder = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace();

         // Falls Cache-Ordner nicht direkt existiert, diesen anlegen
         if(!is_dir($cacheFolder)){

            // Pfad in Abschnitte zerlegen
            $ordnerArray = explode('/',$cacheFolder);

            // Ersten Ordner-Pfad (=leer) entfernen
            if(empty($ordnerArray[0])){
               array_shift($ordnerArray);
             // end if
            }

            // Aktuellen Pfad initialisieren
            $aktuellerPfad = '/';

            // Verzeichnis-Trenner initialisieren
            $verzeichnisTrenner = '';

            // Verzeichnisse rekursiv anlegen
            for($i = 0; $i < count($ordnerArray); $i++){

               $aktuellerPfad .= $verzeichnisTrenner.$ordnerArray[$i];

               // Sonderverhalten für WINDOWS: Sollte der aktuelle Pfad z.B. ein '/e:' sein, so muss dieser ersetzt werden
               if(preg_match("=/[a-z]{1}:=i",$aktuellerPfad)){
                  $aktuellerPfad = str_replace('/','',$aktuellerPfad);
                // end if
               }

               // Ordner anlegen, falls kein Symlink auf aktuelles Verzeichnis oder Vater-Verzeichnis
               if($ordnerArray[$i] != '..' && $ordnerArray[$i] != '.'){

                  if(!is_dir($aktuellerPfad)){

                     // Aktuellen Pfad erzeugen
                     mkdir($aktuellerPfad,$this->__cacheFolderPermissions);

                   // end if
                  }

                // end if
               }

               $verzeichnisTrenner = '/';

             // end for
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt aus einem Namespace (z.B. 'cms::sites') einen Ordnerpfad (z.B. 'cms/sites').<br />
      *
      *  @return string $NamespacePath; In einen Pfad umgewandelten Namespace
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.06.2006<br />
      */
      function __generatePathFromNamespace(){
         return str_replace('::','/',$this->__cacheNamespace);
       // end function
      }

    // end class
   }
?>