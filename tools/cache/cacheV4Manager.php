<?php
   import('core::filesystem','filesystemHandler');
   import('tools::cache','abstractCacheManager');


   /**
   *  @package tools::cache
   *  @class cacheV4Manager
   *
   *  Implementiert den cacheV4Manager, der die globalen CMS-Caches handelt.<br />
   *  Unterhalb des Basis-Ordners (Namespace)werden bei Bedarf weitere Strukturen angelegt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 22.05.2005<br />
   *  Version 0.2, 12.02.2006<br />
   *  Version 0.3, 26.02.2006 (Status ist nun konfigurierbar (aktiv/nicht aktiv))<br />
   *  Version 0.4, 27.03.2007 (cacheV4Manager erbt nun von einem abstrakten CacheManager)<br />
   */
   class cacheV4Manager extends abstractCacheManager
   {

      var $__PageFolderBaseName = 'Page';


      function cacheV4Manager(){
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
      *  Version 0.2, 31.03.2007 (in "init()" umbenannt um als SO aufgerufen werden zu können)<br />
      */
      function init($ConfigSection){
         parent::initAbstractCacheManager($ConfigSection);
       // end function
      }


      /**
      *  @public
      *
      *  Löscht den Cache je nach gegebenem Namespace.<br />
      *
      *  @param string $Page; Seite, für die der Cache gelöscht werden soll
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.05.2005<br />
      *  Version 0.2, 12.02.2006<br />
      *  Version 0.3, 27.03.2007<br />
      *  Version 0.4, 31.03.2007 (Es wird nun abgefragt, ob der Cache aktiv ist)<br />
      */
      function clearPageCache($Page){

         if($this->__cacheAktive == true){

            // Ordner-Namen generieren
            $Folder = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.base64_encode($this->__PageFolderBaseName.'_'.$Page);

            // Ordner löschen
            $fH = new filesystemHandler();
            $fH->deleteFolderRecursive($Folder);

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Löscht den Cache EINER CMS-Seite. Dies können je nach Konstellation auch mehrere Ordner sein.<br />
      *
      *  @param int $PageID; ID der Seite, die aus dem Cache gelöscht werden soll
      *  @param string $PageName; Titel der Seite, die aus dem Cache gelöscht werden soll
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.05.2005<br />
      *  Version 0.2, 12.02.2006<br />
      *  Version 0.3, 17.02.2007 (Es wird abgefragt, ob ein entsprechender Ordner besteht)<br />
      *  Version 0.4, 27.03.2007<br />
      *  Version 0.5, 28.03.2007 (Änderung wegen Neu-Implementierung filesystemHandler)<br />
      *  Version 0.6, 31.03.2007 (Es wird nun abgefragt, ob der Cache aktiv ist)<br />
      *  Version 0.7, 03.01.2008 (Es ist nun möglich Cache von Seiten zu löschen, die mit einem URL-Namen aufgerufen wurden)<br />
      */
      function clearCacheWherePageIsContained($PageID,$PageName){

         //echo '<br />$Page: '.$PageID;

         if($this->__cacheAktive == true){

            /*echo '<br />$Folder: '.*/$Folder = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace();
            $fH = new filesystemHandler($Folder);
            $Folders = $fH->showDirContent();


            // Cache-Ordner durchsuchen
            for($i = 0; $i < count($Folders); $i++){

               //echo '<br />$Folders[$i]: '.$Folders[$i];
               //echo '<br />base64_decode(): '.base64_decode($Folders[$i]);
               //echo '<br />MatchingFolder: '.$this->__PageFolderBaseName.'_([a-zA-Z0-9\.\;]+)';

               preg_match('='.$this->__PageFolderBaseName.'_([a-zA-Z0-9\.\;]+)=',base64_decode($Folders[$i]),$Matches);

               // Prüfen, ob eine Seite gefunden wurde
               if(isset($Matches[1])){

                  //echo '<br />Folder matches!';

                  // Dedektieren, wo Seite involviert ist
                  $Contents = split('[.]',$Matches[1]);
                  array_walk($Contents,'intval');

                  //echo printObject($Contents);

                  // Betroffene Ordner leeren und löschen
                  //if(in_array(intval($Page),$Contents)){
                  //echo '<br />'.$PageID.'|'.$PageName.' == '.implode(';',$Contents).' ???';
                  if(in_array($PageID,$Contents) || in_array($PageName,$Contents)){
                     //echo '<br />Folder is in array!';
                     $fH->deleteFolderRecursive($Folder.'/'.$Folders[$i]);
                   // end if
                  }

                // end if
               }

             // end for
            }

          // end if
         }

       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Cache-Datei-Namen.<br />
      *
      *  @param string $URL; Aktuelle URL
      *  @param string $Extension; Endung der Cache-Datei
      *  @return string $CacheFileName; Name der Cache-Datei
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.02.2006<br />
      *  Version 0.2, 05.03.2006<br />
      *  Version 0.3, 27.03.2007<br />
      */
      function __generateCacheFileName($URL,$Extension = 'htcf'){
         return md5($URL).'.'.$Extension;
       // end function
      }


      /**
      *  @private
      *
      *  Erzeugt den Namen des Cache-Ordners.<br />
      *
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @return string $CacheFolderName; Name des Cache-Ordners
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.02.2006<br />
      */
      function __generateCacheFolderName($Page){
         return base64_encode($this->__PageFolderBaseName.'_'.$Page);
       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung der Prüf-Routine für CMS-Cache-Files.<br />
      *
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @param string $URL; Aktuelle URL
      *  @return bool $CacheFileExists; true, falls Datei existiert, false, falls nicht
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2006<br />
      *  Version 0.2, 27.03.2007<br />
      */
      function cacheFileExists($Page,$URL){

         // Informationen erzeugen
         $PageFolder = $this->__generateCacheFolderName($Page);
         $this->__cacheFileName = $PageFolder.'/'.$this->__generateCacheFileName($URL);

         // Auf Existenz prüfen
         return parent::cacheFileExists();

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung der Schreib-Routine für CMS-Cache-Files.<br />
      *
      *  @param void $Content; Inhalt der zu schreibenden Cache-Datei
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @param string $URL; Aktuelle URL
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2006<br />
      *  Version 0.2, 27.03.2007<br />
      *  Version 0.3, 28.03.2007 ("__generateCacheNamespace()" wird erst beim Schreiben ausgeführt)<br />
      */
      function writeToCache($Content,$Page,$URL){

         if($this->__cacheAktive == true){

            // Cache-Namespace prüfen und ggf. anlegen
            $this->__generateCacheNamespace();

            // Informationen erzeugen
            $PageFolder = $this->__generateCacheFolderName($Page);
            $this->__cacheFileName = $PageFolder.'/'.$this->__generateCacheFileName($URL);

            // SeitenOrdner erstellen, falls dieser nicht existiert
            if(!is_dir($this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$PageFolder)){
               mkdir($this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$PageFolder,$this->__cacheFolderPermissions);
             // end if
            }

          // end if
         }

         // Cache schreiben
         parent::writeToCache($Content);

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung der Lese-Routine für CMS-Cache-Files.<br />
      *
      *  @param string $Page; Indikator für die aktuelle Seite
      *  @param string $URL; Aktuelle URL
      *  @return string $CacheContent; Inhalt der Cache-Datei
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.01.2006<br />
      *  Version 0.2, 27.03.2007<br />
      */
      function readFromCache($Page,$URL){

         // Informationen generieren
         $PageFolder = $this->__generateCacheFolderName($Page);
         $this->__cacheFileName = $PageFolder.'/'.$this->__generateCacheFileName($URL);

         // CacheFile zurückgeben
         return parent::readFromCache();

       // end function
      }

    // end class
   }
?>