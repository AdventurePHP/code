<?php
   import('tools::cache','abstractCacheManager');


   /**
   *  @package tools::cache
   *  @class statCacheManager
   *
   *  Implementiert den CacheManager f�r das Statistik-Tool.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 05.06.2006<br />
   *  Version 0.2, 01.04.2007 (Neuimplementierung auf Basis des abstractCacheManager's)<br />
   */
   class statCacheManager extends abstractCacheManager
   {

      function statCacheManager(){
      }


      /**
      *  @public
      *
      *  Initialisiert den statCacheManager.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 27.03.2007<br />
      */
      function init($ConfigSection){
         parent::initAbstractCacheManager($ConfigSection);
       // end function
      }


      /**
      *  @public
      *
      *  L�sst das setzen des Cache-Datei-Namens von aussen zu..<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.04.2007<br />
      */
      function setCacheFileName($CacheFileName){
         $this->__cacheFileName = md5($CacheFileName).'.aocf';
       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung f�r den statCacheManager.<br />
      *
      *  @param void $Object; Inhalt der zu schreibenden Cache-Datei
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.04.2007<br />
      */
      function writeToCache($Object){

         if($this->__cacheAktive == true){

            // Cache-Namespace pr�fen und ggf. anlegen
            $this->__generateCacheNamespace();

            // Cache schreiben
            abstractCacheManager::writeToCache(serialize($Object));

          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Erneute Implementierung f�r den statCacheManager.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 01.04.2007<br />
      */
      function readFromCache(){

         // CacheObjekt zur�ckgeben
         $Object = abstractCacheManager::readFromCache();
         return unserialize(trim($Object));

       // end function
      }

    // end class
   }
?>