<?php
   /**
   *  @file bild.php
   *
   *  Repräsentiert die Ausgabe-Datei für Bildausgeben mit UND ohne Resizer.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.02.2006<br />
   *  Version 0.2, 27.03.2007 (Implementierung für die neue Version des imageManager's und cacheManager's)<br />
   *  Version 0.3, 02.06.2007 (Erweiterung, dass Bild mit seinem eigenen Namen gespeichert werden kann.<br />
   *                           Siehe http://ffm.junetz.de/members/reeg/DSP/node16.html#4567 und
   *                           http://www.developers-guide.net/forums/5587,bild-ueber-php-skript-ausgeben-dateinamen-mit-header-setzen).<br />
   */

   // ApplicationManager einbinden
   require_once(APPS__PATH.'/core/applicationmanager/ApplicationManager.php');


   // Benötigte Klassen einbinden
   import('tools::variablen','variablenHandler');
   import('modules::imageresizer::biz','imageCacheResizerManager');


   // Bild und Pfad aus der URL extrahieren
   $_LOCALS = variablenHandler::registerLocal(array('Bild','Pfad' => 'MEDIA_PATH','Groesse' => '100'));


   // ResizeManager instanziieren
   $iRM = new imageCacheResizerManager();


   // Context der aktuellen Applikation mitgeben
   $iRM->set('Context',$Context);


   // Resize-CacheManager
   $iRM->initImageCacheResizerManager('imageresizer');
   $ImageInfo = $iRM->resizeImage($_LOCALS['Pfad'],$_LOCALS['Bild'],$_LOCALS['Groesse']);


   // Dateigröße bestimmen
   $ImageFileSize = filesize($ImageInfo['Bild']);
   clearstatcache();


   // Header ausgeben
   if($ImageInfo['Endung'] == 'gif'){
      header('Content-Type: image/gif');
    // end if
   }
   else{
      header('Content-Type: image/jpeg');
    // end else
   }

   header('Content-disposition: inline; filename="'.$_LOCALS['Bild'].'"');
   header('Content-Transfer-Encoding: binary');
   header('Content-Length: '.$ImageFileSize);


   // Bild ausgeben
   @readfile($ImageInfo['Bild']);
?>