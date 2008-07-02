<?php
   import('modules::imageresizer::biz','imageCacheResizerManager');


   /**
   *  @package modules::imageresizer::biz::actions
   *  @module ShowImageAction
   *
   *  Implementiert die FrontControllerAction für den ImageResizer.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.10.2007
   */
   class ShowImageAction extends AbstractFrontcontrollerAction
   {

      function ShowImageAction(){
      }


      /**
      *  @module run()
      *  @public
      *
      *  Implementiert die abstrakte Methode run().<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.10.2007<br />
      */
      function run(){

         // Bild und Pfad aus dem Input-Objekt extrahieren
         $Image = $this->__Input->getAttribute('Bild');
         $Path = $this->__Input->getAttribute('Pfad');
         $Size = $this->__Input->getAttribute('Groesse');


         // Prüfen, ob Attribute vollständig
         if($Image == null){
            trigger_error('[ShowImageAction::run()] No image name ("Bild") given in frontcontroller input params!',E_USER_ERROR);
            exit;
          // end if
         }
         if($Path == null){
            trigger_error('[ShowImageAction::run()] No path information ("Pfad") given in frontcontroller input params!',E_USER_ERROR);
            exit;
          // end if
         }


         // ResizeManager instanziieren
         $iRM = &$this->__getServiceObject('modules::imageresizer::biz','imageCacheResizerManager');


         // Resize-CacheManager
         $iRM->initImageCacheResizerManager('imageresizer');
         $ImageInfo = $iRM->resizeImage($Path,$Image,$Size);


         // Dateigröße bestimmen
         $ImageFileSize = filesize($ImageInfo['Bild']);
         clearstatcache();


         // ContentType zusammensetzen
         switch($ImageInfo['Endung']){

            case 'gif':
               $ContentType = 'image/gif';
               break;
            case 'jpg':
               $ContentType = 'image/jpg';
               break;
            case 'jpeg':
               $ContentType = 'image/jpg';
               break;
            case 'png':
               $ContentType = 'image/png';
               break;
            default:
               $ContentType = 'application/octet-stream';
               break;

          // end switch
         }


         // Header ausgeben
         header('Content-Type: '.$ContentType);
         header('Content-disposition: inline; filename="'.$Image.'"');
         header('Content-Transfer-Encoding: binary');
         header('Content-Length: '.$ImageFileSize);
         // Caching-Header ausgeben!


         // Bild ausgeben
         @readfile($ImageInfo['Bild']);


         // Ausgabe beenden
         exit();

       // end function
      }

    // end class
   }
?>