<?php
   import('tools::image','imageManager');
   import('tools::cache','abstractCacheManager');


   /**
   *  @package modules::imageresizer::biz
   *  @module imageCacheResizerManager
   *
   *  Implementiert den imageCacheResizerManager.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.02.2006<br />
   *  Version 0.2, 27.03.2007<br />
   */
   class imageCacheResizerManager extends abstractCacheManager
   {

      function imageCacheResizerManager(){
      }


      /**
      *  @module initImageResizerManager()
      *  @public
      *
      *  Initialisiert den CacheManager.<br />
      *
      *  @param string $ConfigSection; Konfigurations-Abschnitt f�r die Initialisierung
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 27.03.2007<br />
      */
      function initImageCacheResizerManager($ConfigSection){
         parent::initAbstractCacheManager($ConfigSection);
       // end function
      }


      /**
      *  @module resizeImage()
      *  @public
      *
      *  Resized das angegebene Bild, cached dieses und gibt
      *  Bildinformationen (Verzeichnis-Pfad, MIME-Typ) zur�ck.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.02.2006<br />
      *  Version 0.2, 27.03.2007<br />
      *  Version 0.3, 28.03.2007 ("__generateCacheNamespace()" wird nun erst beim Schreiben ausgef�hrt)<br />
      */
      function resizeImage($MediaPath,$ImageName,$ImageSize){

         // Pfad des Bildes ermitteln
         $Constants = get_defined_constants();
         $MediaPath = $Constants[trim($MediaPath)];

         // Bildgroe�e
         $ImageSize = (int) trim($ImageSize);

         // Bildname
         $ImageName = trim($ImageName);


         // Return-Array definieren
         $return = array();

         // Falls Gr��e = 100(%) -> Bild ohne Bearbeitung ausgeben
         if($ImageSize == 100){

            $return['Bild'] = $MediaPath.'/'.$ImageName;
            $Temp = imageManager::showImageAttributes($return['Bild']);
            $return['Endung'] = $Temp['Type'];

          // end if
         }
         else{

            // Bildpfad des Orginalbildes
            $Image = $MediaPath.'/'.$ImageName;

            // Ma�e des Orginalbildes abfragen
            $Temp = imageManager::showImageAttributes($Image);

            // Endung setzen
            $return['Endung'] = $Temp['Type'];

            // Breite und H�he des Ziel-Bildes berechnen
            $Width = round(($ImageSize / 100) * intval($Temp['Width']),0);
            $Height = round(($ImageSize / 100) * intval($Temp['Height']),0);

            // Cache-Namen vorbereiten
            $this->__cacheFileName = md5($_SERVER['REQUEST_URI']).'.'.$Temp['Type'];

            // Cache-Namespace pr�fen und ggf. anlegen
            $this->__generateCacheNamespace();

            // Fertiges Bild einsetzen
            $return['Bild'] = $this->__cacheFolder.'/'.$this->__generatePathFromNamespace().'/'.$this->__cacheFileName;

            // Bild resizen
            if(!file_exists($return['Bild'])){
               $iM = new imageManager($Width,$Height);
               $iM->resizeImage($Image,$this->__cacheFolder.'/'.$this->__generatePathFromNamespace(),$this->__cacheFileName);
             // end if
            }

          // end else
         }

         // Bild-Infos zur�ckgeben
         return $return;

       // end function
      }

    // end class
   }
?>