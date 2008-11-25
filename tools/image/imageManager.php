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

   import('core::filesystem','filesystemHandler');


   /**
   *  @namespace tools::image
   *  @class imageManager
   *
   *  Stellt Methoden zur Bildbearbeitung bereit.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 08.09.2003<br />
   *  Version 0.2, 17.09.2004<br />
   *  Version 0.3, 21.01.2006<br />
   *  Version 0.4, 06.03.2007 (Anpassungen am Code)<br />
   *  Version 0.5, 31.03.2007 (Refactoring und PNG-Support hinzugefügt)<br />
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
      *  Höhe des Bildes.
      */
      var $__Height;


      /**
      *  @private
      *  Qualität eines JPG-Bildes.
      */
      var $__JPGQuality;


      /**
      *  @public
      *
      *  Konstruktor der Klasse. Setzt die Statdard-Einstellungen der Library.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.09.2003<br />
      *  Version 0.2, 17.09.2004<br />
      *  Version 0.3, 21.01.2006<br />
      *  Version 0.4, 06.03.2007 (JPG-Qualität wird nicht mehr aus dem Config-File geladen)<br />
      */
      function imageManager($Width = 80,$Height = 80){

         // Breite und Höhe setzen
         $this->__Width = $Width;
         $this->__Height = $Height;


         // JPG-Qualität setzen
         $this->__JPGQuality = 80;

       // end function
      }


      /**
      *  @public
      *
      *  Ermöglicht das setzen von Breite und Hoehe auch auch ausserhalb des Konstruktors.<br />
      *
      *  @author Christian Schäfer
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
      *  Erzeugt ein Pictogramm der Größe 80x80px (standard) und<br />
      *  speichert dieses im übergebenen Pfad ab.<br />
      *  Als Namen wird der Name des Quellbildes mit dem Suffix<br />
      *  _klein gewählt. Zurückgegeben wird der Link zum<br />
      *  Pictogramm.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.09.2003<br />
      *  Version 0.2, 17.09.2004<br />
      *  Version 0.3, 22.11.2004<br />
      *  Version 0.4, 01.12.2004<br />
      *  Version 0.5, 02.12.2004<br />
      *  Version 0.6, 17.02.2005 (transparenten GIFs ergänzt)<br />
      *  Version 0.7, 21.01.2006 (Meldung entfernt)<br />
      *  Version 0.8, 22.01.2006<br />
      *  Version 0.9, 31.03.2007 (PNG-Support hinzugefügt)<br />
      */
      function generateThumbnail($ImageName,$ThumbnailPath){

         // Breite und Höhe des Pictogramms definierten
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

            // Zielbild mit transparenter Farbe füllen
            imagefill($ziel_bild,0,0,$colorTransparent);

            // Die Füllfarbe als transparent deklarieren
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


         // Thumb aus Speicher löschen
         imagedestroy($ziel_bild);

         // Quellbild aus Speicher löschen
         imagedestroy($quell_bild);

         // Link des Pictogramms zurückgeben
         return $ThumbnailPath.'/'.$thumb_name;

       // end function
      }


      /**
      *  @public
      *  @static
      *
      *  Gibt wichtige Bildmaße wie Breite, Höhe und Typ eines Bildes aus.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 22.11.2004<br />
      *  Version 0.2, 15.07.2006 (Endung im Ergebnis-Array ergänzt; Alternativer Algorithmus zur Extraktion von DateiEigenName und Endung)<br />
      */
      function showImageAttributes($ImageName){

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

         // Rückgabe-Array zusammensetzen
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
      *  Resized ein Bild nach bei der Initialisierung angegebenen Maßen.<br />
      *  Rückgabewert ist der Bildname.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 14.01.2005<br />
      *  Version 0.2, 16.02.2005 (transparente GIFs ergänzt)<br />
      *  Version 0.3, 22.01.2006 (Fehler bei der Behandlung der JPG-Qualität beseitigt)<br />
      *  Version 0.4, 31.03.2007 (PNG-Support hinzugefügt)<br />
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


         // Breiten und Höhen definierten
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

            // Zielbild mit transparenter Farbe füllen
            imagefill($ziel_bild,0,0,$colorTransparent);

            // Die Füllfarbe als transparent deklarieren
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

         // Thumb aus Speicher löschen
         imagedestroy($ziel_bild);

         // Quellbild aus Speicher löschen
         imagedestroy($quell_bild);

         // Link des Pictogramms zurückgeben
         return $thumb_pfad.'/'.$thumb_name;

       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Möglichkeit die in der Konfiguration eingetragene<br />
      *  Qualität manuell zu setzen.<br />
      *
      *  @author Christian Schäfer
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