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

   import('tools::cache','cacheV4Manager');


   /**
   *  @namespace tools::cache
   *  @class objectCacheManager
   *  @deprecated
   *
   *  Implementiert einen Cache-Manager f�r Objekte. Anwendung: z.B. Bildergalerie.<br />
   *  F�gt sich in die Architektur der Cache-Datei-Struktur des CMS ein.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 26.02.2006<br />
   *  Version 0.2, 05.03.2006<br />
   *  Version 0.3, 27.03.2007 (�berarbeitete Version PC V2)<br />
   */
   class objectCacheManager extends cacheV4Manager
   {

      /**
      *  @public
      *
      *  Konstruktor der Klasse. Initialisiert den Cache-Datei-Namen.<br />
      *
      *  @author Christian Sch�fer
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
      *  @author Christian Sch�fer
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
      *  Erneute Implementierung f�r den ObjectCacheManager.<br />
      *
      *  @param string $Page; Indikator f�r die aktuelle Seite
      *  @return bool $CacheFileExists; true, falls Datei existiert, false, falls nicht
      *
      *  @author Christian Sch�fer
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

         // Auf Existenz pr�fen
         return abstractCacheManager::cacheFileExists();

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung f�r den ObjectCacheManager.<br />
      *
      *  @param void $Object; Inhalt der zu schreibenden Cache-Datei
      *  @param string $Page; Indikator f�r die aktuelle Seite
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 26.02.2006<br />
      *  Version 0.2, 05.03.2006<br />
      *  Version 0.3, 27.03.2007<br />
      *  Version 0.4, 28.03.2007 ("__generateCacheNamespace()" wird erst beim Schreiben ausgef�hrt)<br />
      */
      function writeToCache($Object,$Page){

         if($this->__cacheAktive == true){

            // Cache-Namespace pr�fen und ggf. anlegen
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
      *  Erneute Implementierung f�r den ObjectCacheManager.<br />
      *
      *  @param string $Page; Indikator f�r die aktuelle Seite
      *  @return object $Object; Cache-Objekt
      *
      *  @author Christian Sch�fer
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

         // CacheObjekt zur�ckgeben
         $Object = abstractCacheManager::readFromCache();
         return unserialize(trim($Object));

       // end function
      }

    // end class
   }
?>