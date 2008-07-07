<?php
   import('core::filesystem','filesystemHandler');


   /**
   *  @package tools::image
   *  @class imageManager
   *
   *  Stellt Methoden zur Bildbearbeitung bereit.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 08.09.2003<br />
   *  Version 0.2, 17.09.2004<br />
   *  Version 0.3, 21.01.2006<br />
   *  Version 0.4, 06.03.2007 (Anpassungen am Code)<br />
   *  Version 0.5, 31.03.2007 (Refactoring und PNG-Support hinzugef�gt)<br />
   */
   class imageManager
   {

      /**
      *  @private
      *  Breite des Bildes.
      */
      var $__Width;


      /**
      *  @private
      *  H�he des Bildes.
      */
      var $__Height;


      /**
      *  @private
      *  Qualit�t eines JPG-Bildes.
      */
      var $__JPGQuality;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Setzt die Statdard-Einstellungen der Library.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2003<br />
      *  Version 0.2, 17.09.2004<br />
      *  Version 0.3, 21.01.2006<br />
      *  Version 0.4, 06.03.2007 (JPG-Qualit�t wird nicht mehr aus dem Config-File geladen)<br />
      */
      function imageManager($Width = 80,$Height = 80){

         // Breite und H�he setzen
         $this->__Width = $Width;
         $this->__Height = $Height;


         // JPG-Qualit�t setzen
         $this->__JPGQuality = 80;

       // end function
      }


      /**
      *  @public
      *
      *  Erm�glicht das setzen von Breite und Hoehe auch auch ausserhalb des Konstruktors.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 20.03.2005<br />
      */
      function setImageSize($Width,$Height){
         $this->__Width = $Width;
         $this->__Height = $Height;
       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt ein Pictogramm der Gr��e 80x80px (standard) und<br />
      *  speichert dieses im �bergebenen Pfad ab.<br />
      *  Als Namen wird der Name des Quellbildes mit dem Suffix<br />
      *  _klein gew�hlt. Zur�ckgegeben wird der Link zum<br />
      *  Pictogramm.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 08.09.2003<br />
      *  Version 0.2, 17.09.2004<br />
      *  Version 0.3, 22.11.2004<br />
      *  Version 0.4, 01.12.2004<br />
      *  Version 0.5, 02.12.2004<br />
      *  Version 0.6, 17.02.2005 (transparenten GIFs erg�nzt)<br />
      *  Version 0.7, 21.01.2006 (Meldung entfernt)<br />
      *  Version 0.8, 22.01.2006<br />
      *  Version 0.9, 31.03.2007 (PNG-Support hinzugef�gt)<br />
      */
      function generateThumbnail($ImageName,$ThumbnailPath){

         // Breite und H�he des Pictogramms definierten
         $thumb_hoehe = $this->__Height;
         $thumb_breite = $this->__Width;


         // Quellbild analysieren
         $Eigenschaften = imageManager::showImageAttributes($ImageName);

         $quell_bild_breite = $Eigenschaften['Width'];
         $quell_bild_hoehe = $Eigenschaften['Height'];
         $quell_bild_endung = $Eigenschaften['Type'];
         $quell_bild_eigenname = $Eigenschaften['FileBaseName'];


         // Ziel-Bild erzeugen
         if($quell_bild_endung == 'jpg'){
            $ziel_bild = imagecreatetruecolor($thumb_breite,$thumb_hoehe);
          // end if
         }
         else{
            $ziel_bild = imagecreate($thumb_breite,$thumb_hoehe);
          // end if
         }


         // Aktuelles Bild laden
         if($quell_bild_endung == 'jpg'){
            $quell_bild = imagecreatefromjpeg($ImageName);
          // end if
         }
         elseif($quell_bild_endung == 'gif'){
            $quell_bild = imagecreatefromgif($ImageName);
          // end if
         }
         else{
            $quell_bild = imagecreatefrompng($ImageName);
          // end else
         }


         // Transparenz kopieren, falls GIF
         if($quell_bild_endung == 'gif'){

            // Transparente Farbe des Quell-Bildes abfragen
            $colorTransparent = imagecolortransparent($quell_bild);

            // Parlette kopieren
            imagepalettecopy($ziel_bild,$quell_bild);

            // Zielbild mit transparenter Farbe f�llen
            imagefill($ziel_bild,0,0,$colorTransparent);

            // Die F�llfarbe als transparent deklarieren
            imagecolortransparent($ziel_bild,$colorTransparent);

          // end if
         }


         // Name des Piktogramms zusammensetzen
         $thumb_name = $quell_bild_eigenname.'_thumb.'.$quell_bild_endung;


         // Quellbild verkleinert in das Zielbild kopieren
         imagecopyresized($ziel_bild,$quell_bild,0,0,0,0,$thumb_breite,$thumb_hoehe,$quell_bild_breite,$quell_bild_hoehe);


         // Zielbild unter angegebenem Namen speichern
         if($quell_bild_endung == 'jpg'){
            imagejpeg($ziel_bild,$ThumbnailPath.'/'.$thumb_name);
          // end if
         }
         elseif($quell_bild_endung == 'gif'){
            imagegif($ziel_bild,$ThumbnailPath.'/'.$thumb_name);
          // end if
         }
         else{
            imagepng($ziel_bild,$ThumbnailPath.'/'.$thumb_name);
          // end else
         }


         // Thumb aus Speicher l�schen
         imagedestroy($ziel_bild);

         // Quellbild aus Speicher l�schen
         imagedestroy($quell_bild);

         // Link des Pictogramms zur�ckgeben
         return $ThumbnailPath.'/'.$thumb_name;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt wichtige Bildma�e wie Breite, H�he und Typ eines Bildes aus.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 22.11.2004<br />
      *  Version 0.2, 15.07.2006 (Endung im Ergebnis-Array erg�nzt; Alternativer Algorithmus zur Extraktion von DateiEigenName und Endung)<br />
      */
      static function showImageAttributes($ImageName){

         // Quellbild analysieren
         $size = getimagesize($ImageName);
         $bild_breite = $size[0];
         $bild_hoehe = $size[1];
         $bild_type = $size[2];

         // Bild-Flags deklarieren
         $img_flag[1] = 'gif';
         $img_flag[2] = 'jpg';
         $img_flag[3] = 'png';
         $img_flag[4] = 'swf';

         // Dateinamen und Endung extrahieren
         $BildName = basename($ImageName);
         $EigenName = substr($BildName,0,strlen($BildName) - (strlen($BildName) - strpos($BildName,'.')));
         $Endung = substr(basename($BildName),strpos($BildName,'.') + 1,strlen($BildName));

         // R�ckgabe-Array zusammensetzen
         $attributes = array();
         $attributes['FileName'] = basename($ImageName);
         $attributes['FileBaseName'] = $EigenName;
         $attributes['Extension'] = $Endung;
         $attributes['Width'] = $bild_breite;
         $attributes['Height'] = $bild_hoehe;
         $attributes['Type'] = $img_flag[$bild_type];

         return $attributes;

       // end function
      }


      /**
      *  @public
      *
      *  Resized ein Bild nach bei der Initialisierung angegebenen Ma�en.<br />
      *  R�ckgabewert ist der Bildname.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 14.01.2005<br />
      *  Version 0.2, 16.02.2005 (transparente GIFs erg�nzt)<br />
      *  Version 0.3, 22.01.2006 (Fehler bei der Behandlung der JPG-Qualit�t beseitigt)<br />
      *  Version 0.4, 31.03.2007 (PNG-Support hinzugef�gt)<br />
      */
      function resizeImage($Bild,$BildPfad,$BildName){

         // Speicherpfad festlegen
         $thumb_pfad = $BildPfad;

         // Quelle festlegen
         $bild_name = $Bild;


         // BildTyp ermitteln
         $BildEigenschaften = imageManager::showImageAttributes($Bild);
         $quell_bild_breite = $BildEigenschaften['Width'];
         $quell_bild_hoehe = $BildEigenschaften['Height'];
         $quell_bild_endung = $BildEigenschaften['Type'];


         // Breiten und H�hen definierten
         $thumb_hoehe = $this->__Height;
         $thumb_breite = $this->__Width;


         // Ziel-Bild erzeugen
         if($quell_bild_endung == 'jpg'){
            $ziel_bild = imagecreatetruecolor($thumb_breite,$thumb_hoehe);
          // end if
         }
         else{
            $ziel_bild = imagecreate($thumb_breite,$thumb_hoehe);
          // end if
         }


         // Aktuelles Bild laden
         if($quell_bild_endung == 'jpg'){
            $quell_bild = imagecreatefromjpeg($bild_name);
          // end if
         }
         elseif($quell_bild_endung == 'gif'){
            $quell_bild = imagecreatefromgif($bild_name);
          // end
         }
         else{
            $quell_bild = imagecreatefrompng($bild_name);
          // end else
         }


         // Transparenz kopieren, falls GIF
         if($quell_bild_endung == 'gif'){

            // Transparente Farbe des Quell-Bildes abfragen
            $colorTransparent = imagecolortransparent($quell_bild);

            // Parlette kopieren
            imagepalettecopy($ziel_bild,$quell_bild);

            // Zielbild mit transparenter Farbe f�llen
            imagefill($ziel_bild,0,0,$colorTransparent);

            // Die F�llfarbe als transparent deklarieren
            imagecolortransparent($ziel_bild,$colorTransparent);

          // end if
         }


         // Name des Pictogramms zusammensetzen:
         $thumb_name = $BildName;

         // Quellbild verkleinert in das Zielbild kopieren
         imagecopyresized($ziel_bild,$quell_bild,0,0,0,0,$thumb_breite,$thumb_hoehe,$quell_bild_breite,$quell_bild_hoehe);


         // Zielbild unter angegebenem Namen speichern
         if($quell_bild_endung == 'jpg'){
            imagejpeg($ziel_bild,$thumb_pfad.'/'.$thumb_name,(int)$this->__JPGQuality);
          // end if
         }
         elseif($quell_bild_endung == 'gif'){
            imagegif($ziel_bild,$thumb_pfad.'/'.$thumb_name);
          // end
         }
         else{
            imagepng($ziel_bild,$thumb_pfad.'/'.$thumb_name);
          // end else
         }

         // Thumb aus Speicher l�schen
         imagedestroy($ziel_bild);

         // Quellbild aus Speicher l�schen
         imagedestroy($quell_bild);

         // Link des Pictogramms zur�ckgeben
         return $thumb_pfad.'/'.$thumb_name;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die M�glichkeit die in der Konfiguration eingetragene<br />
      *  Qualit�t manuell zu setzen.<br />
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 17.02.2005<br />
      */
      function setJPGQuality($JPGQuality){
         $this->__JPGQuality = $JPGQuality;
       // end function
      }

    // end class
   }
?>