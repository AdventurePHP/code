<?php
   import('modules::bildergalerie::data','BilderGalerieMapper');
   import('modules::bildergalerie::biz','BildObjekt');
   import('modules::bildergalerie::biz','ThemaObjekt');
   import('modules::bildergalerie::biz','GalerieObjekt');
   import('tools::cache','objectCacheManager');
   import('tools::variablen','variablenHandler');


   /**
   *  @package modules::bildergalerie::biz
   *  @module BilderGalerieManager
   *
   *  Implementiert den Manager auf Business-Ebene.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 14.05.2005<br />
   *  Version 0.2, 16.05.2005<br />
   *  Version 0.3, 17.05.2005<br />
   *  Version 0.4, 22.05.2005<br />
   *  Version 0.5, 09.11.2005<br />
   *  Version 0.6, 30.11.2005<br />
   *  Version 0.7, 26.02.2006<br />
   *  Version 0.8, 05.03.2006<br />
   *  Version 0.9, 17.03.2007 (Implementierung auf PC V2)<br />
   */
   class BilderGalerieManager extends coreObject
   {

      /**
      *  @private
      *  Hält aktuelle Seite für Caching.
      */
      var $_LOCALS;


      /**
      *  @module BilderGalerieManager()
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.05.2005<br />
      *  Version 0.2, 17.05.2005<br />
      *  Version 0.3, 01.06.2005<br />
      */
      function BilderGalerieManager(){
         $this->_LOCALS = variablenHandler::registerLocal(array('Seite'));
       // end function
      }


      /**
      *  @module ladeGalerie()
      *  @public
      *
      *  Implementiert das Laden eines GalerieBaumes mit Caching.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.05.2005<br />
      *  Version 0.2, 17.05.2005<br />
      *  Version 0.3, 01.06.2005<br />
      *  Version 0.4, 26.02.2006 (Neues Caching eingeführt)<br />
      *  Version 0.5, 05.03.2006 (Caching schreibt jetzt Objekt-Cache-Dateien (=Galerie-Baum) seitenabhängig)<br />
      *  Version 0.6, 17.03.2007 (Implementierung auf PC V2)<br />
      */
      function ladeGalerie($Index){

         // Instanz des Mappers holen
         $BGMapper = &$this->__getServiceObject('modules::bildergalerie::data','BilderGalerieMapper');


         // Instanz des CacheManagers holen
         $oCM = &$this->__getAndInitServiceObject('tools::cache','objectCacheManager','galerie');


         // Objekt-Baum aus Cache laden, wenn Cache aktiv
         if($oCM->cacheFileExists($this->_LOCALS['Seite'])){
            $Galerie = $oCM->readFromCache($this->_LOCALS['Seite']);
          // end if
         }
         else{

            // Galerie laden
            $Galerie = $BGMapper->ladeGalerieDatenPerIndex($Index);

            // Themen der Galerie laden
            $Themen = $BGMapper->ladeThemaDatenPerGalerie($Index);

            // Bilder für jedes Thema laden
            for($i = 0; $i < count($Themen); $i++){

               // Bilder des Themas laden
               $Bilder = $BGMapper->ladeBildDatenPerThema($Themen[$i]->zeigeGTIndex());

               // Bilder in Thema einhängen
               $Themen[$i]->haengeBilderBaumEin($Bilder);

             // end for
            }

            // Themen in Galerie einhängen
            $Galerie->haengeThemaBaumEin($Themen);


            // Galerie cachen, wenn Cache aktiv
            $oCM->writeToCache($Galerie,$this->_LOCALS['Seite']);

          // end else
         }

         return $Galerie;

       // end function
      }


      /**
      *  @module zeigeThemenOffsetZuGTIndex()
      *  @public
      *
      *  Extrahiert den Themenoffset aus einem Galeriebaum.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.05.2005<br />
      *  Version 0.2, 17.05.2005<br />
      */
      function zeigeThemenOffsetZuGTIndex(&$GalerieObjekt,$GTIndex){

         $Themen = $GalerieObjekt->zeigeThemen();

         for($i = 0; $i < count($Themen); $i++){
            if($Themen[$i]->zeigeGTIndex() == $GTIndex){
               $ThemenOffset = $i;
             // end if
            }
          // end for
         }

         return $ThemenOffset;

       // end function
      }


      /**
      *  @module zeigeBildOffsetZuGBIndex()
      *  @public
      *
      *  Gibt den Bildoffset im Themenbaum zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.05.2005<br />
      *  Version 0.2, 17.05.2005<br />
      */
      function zeigeBildOffsetZuGBIndex(&$ThemenObjekt,$GBIndex){

         $Bilder = $ThemenObjekt->zeigeBilder();

         for($i = 0; $i < count($Bilder); $i++){
            if($Bilder[$i]->zeigeGBIndex() == $GBIndex){
               $BildOffset = $i;
             // end if
            }
          // end for
         }

         return $BildOffset;

       // end function
      }

    // end class
   }
?>