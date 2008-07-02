<?php
   import('tools::cache','cacheV4Manager');


   /**
   *  @package tools::cache
   *  @class objectCacheManager
   *
   *  Implementiert einen Cache-Manager für Objekte. Anwendung: z.B. Bildergalerie.<br />
   *  Fügt sich in die Architektur der Cache-Datei-Struktur des CMS ein.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 26.02.2006<br />
   *  Version 0.2, 05.03.2006<br />
   *  Version 0.3, 27.03.2007 (Überarbeitete Version PC V2)<br />
   */
   class objectCacheManager extends cacheV4Manager
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert den Cache-Datei-Namen.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 27.03.2007<br />
      */
      function objectCacheManager(){
         $this->__cacheFileName = md5('objektCacheManager_'.date('Y_m_d')).'.ocf';
       // end function
      }


      /**
      *  @public
      *
      *  Initialisiert den CacheManager.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 27.03.2007<br />
      *  Version 0.2, 01.04.2007 (in "init()" umbenannt, damit mit Methode "__getAndInitServiceObject()" nutzbar)<br />
      */
      function init($ConfigSection){
         abstractCacheManager::initAbstractCacheManager($ConfigSection);
       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung für den ObjectCacheManager.<br />
      *
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @return bool $CacheFileExists; true, falls Datei existiert, false, falls nicht
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 26.02.2006<br />
      *  Version 0.2, 05.03.2006<br />
      *  Version 0.3, 27.03.2007<br />
      */
      function cacheFileExists($Page){

         // Informationen erzeugen
         $PageFolder = $this->__generateCacheFolderName($Page);

         if(substr_count($this->__cacheFileName,$PageFolder) < 1){
            $this->__cacheFileName = $PageFolder.'/'.$this->__cacheFileName;
          // end if
         }

         // Auf Existenz prüfen
         return abstractCacheManager::cacheFileExists();

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung für den ObjectCacheManager.<br />
      *
      *  @param void $Object; Inhalt der zu schreibenden Cache-Datei
      *  @param string $Page; Indikator für die aktuelle Seite
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 26.02.2006<br />
      *  Version 0.2, 05.03.2006<br />
      *  Version 0.3, 27.03.2007<br />
      *  Version 0.4, 28.03.2007 ("__generateCacheNamespace()" wird erst beim Schreiben ausgeführt)<br />
      */
      function writeToCache($Object,$Page){

         if($this->__cacheAktive == true){

            // Cache-Namespace prüfen und ggf. anlegen
            $this->__generateCacheNamespace();

            // Informationen erzeugen
            $PageFolder = $this->__generateCacheFolderName($Page);

            if(substr_count($this->__cacheFileName,$PageFolder) < 1){
               $this->__cacheFileName = $PageFolder.'/'.$this->__cacheFileName;
             // end if
            }


            // SeitenOrdner erstellen, falls dieser nicht existiert
            $CurrentCacheFolder = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$PageFolder;
            if(!is_dir($CurrentCacheFolder)){
               mkdir($CurrentCacheFolder,$this->__cacheFolderPermissions);
             // end if
            }

          // end if
         }

         // Cache schreiben
         abstractCacheManager::writeToCache(serialize($Object));

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung für den ObjectCacheManager.<br />
      *
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @return object $Object; Cache-Objekt
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 26.02.2006<br />
      *  Version 0.2, 05.03.2006<br />
      *  Version 0.3, 27.03.2007<br />
      */
      function readFromCache($Page){

         // Informationen erzeugen
         $PageFolder = $this->__generateCacheFolderName($Page);

         if(substr_count($this->__cacheFileName,$PageFolder) < 1){
            $this->__cacheFileName = $PageFolder.'/'.$this->__cacheFileName;
          // end if
         }

         // CacheObjekt zurückgeben
         $Object = abstractCacheManager::readFromCache();
         return unserialize(trim($Object));

       // end function
      }

    // end class
   }
?>