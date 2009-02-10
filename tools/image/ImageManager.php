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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @namespace tools::image
   *  @class ImageManager
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
   class ImageManager
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
      function ImageManager($Width = 80,$Height = 80){

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
      *  @static
      *
      *  Returns information about an image. The return list contains the following offsets:
      *  <ul>
      *    <li>width: the width of the image</li>
      *    <li>height: the height of the image</li>
      *    <li>type: the type of the image</li>
      *    <li>mimetype: the mime type of the image</li>
      *    <li>bitdepth: the bitdepth of the image</li>
      *    <li>colormode: the color mode (RGB or CMYK)</li>
      *  </ul>
      *  If the second argument contains a image attribute, the value is returned instead of a list!
      *
      *  @param string $image a full qualified image path
      *  @param string $attributeName the name of the attribute, that should be returned
      *  @return array $imageAttributes the attributes of an image
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.11.2004<br />
      *  Version 0.2, 15.07.2006 (Added the extension in the attributes list; added another algo to guess the extension)<br />
      *  Version 0.3, 31.01.2009 (Refactoring of the method. Now only the relevant image indicators are returned)<br />
      *  Version 0.4, 01.02.2009 (Added a check, if the channel attribute is returned by getimagesize())<br />
      */
      function getImageAttributes($image,$attributeName = null){

         // check if the image is present on disk
         if(!file_exists($image)){
            trigger_error('[ImageManager::showImageAttributes()] The given image ("'.$image.'") does not exist! Hence, no attributes can be analyzed.');
            return null;
          // end if
         }

         // declare image flags
         $flags[1] = 'gif';
         $flags[2] = 'jpg';
         $flags[3] = 'png';
         $flags[4] = 'swf';

         // initialize the return list
         $imageAttributes = array();

         // analyze the image attributes
         $attributes = getimagesize($image);

         // image define the image dimensions
         $imageAttributes['width'] = $attributes[0];
         $imageAttributes['height'] = $attributes[1];

         // define the image type
         $imageAttributes['type'] = $flags[$attributes[2]];

         // define the mime type
         if(isset($attributes['mime'])){
            $imageAttributes['mimetype'] = $attributes['mime'];
          // end if
         }

         // define the bit depth
         if(isset($attributes['bits'])){
            $imageAttributes['bitdepth'] = $attributes['bits'];
          // end if
         }

         // define the color mode
         if(isset($attributes['channels'])){

            if($attributes['channels'] == '3'){
               $imageAttributes['colormode'] = 'RGB';
             // end if
            }
            else{
               $imageAttributes['colormode'] = 'CMYK';
             // end else
            }

          // end if
         }

         // return attribute
         if($attributeName !== null){

            if(isset($imageAttributes[$attributeName])){
               return $imageAttributes[$attributeName];
             // end if
            }
            else{
               trigger_error('[ImageManager::getImageAttributes()] The desired image attribute ("'.$attributeName.'") does not exist!');
               return null;
             // end else
            }

          // end if
         }

         // return the complete list
         return $imageAttributes;

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
      function resizeImageOld($Bild,$BildPfad,$BildName){

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


      /**
      *  @public
      *  @static
      *
      *  Resizes an image to the given dimensions. If a target image is given, the file is saved to
      *  the desired file.
      *
      *  @param string $sourceImage full qualified path to the image file
      *  @param int $width width of the resized image
      *  @param int $height height of the resized image
      *  @param string $targetImage full qualified path to the target image
      *  @param int $jpgQuality the jpg quality (0-100)
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 31.01.2009<br />
      */
      function resizeImage($sourceImage,$width,$height,$targetImage = null,$jpgQuality = 80){

         // check if the image is present on disk
         if(!file_exists($sourceImage)){
            trigger_error('[ImageManager::resizeImage()] The given image ("'.$sourceImage.'") does not exist! Hence, it cannot be resized.');
          // end if
         }

         // gather the current dimensions of the image
         $attributes = ImageManager::getImageAttributes($sourceImage);
         $sourceImageWidth = $attributes['width'];
         $sourceImageHeight = $attributes['height'];
         $sourceImageType = $attributes['type'];

         // create the current and the target image stream
         if($sourceImageType == 'jpg'){
            $sourceImageStream = imagecreatefromjpeg($sourceImage);
            $targetImageStream = imagecreatetruecolor($width,$height);
          // end if
         }
         elseif($sourceImageType == 'gif'){
            $sourceImageStream = imagecreatefromgif($sourceImage);
            $targetImageStream = imagecreate($width,$height);
          // end
         }
         else{
            $sourceImageStream = imagecreatefrompng($sourceImage);
            $targetImageStream = imagecreate($width,$height);
          // end else
         }

         // copy transparency if we resize a gif image
         if($sourceImageType == 'gif'){

            // query the transparency color
            $transparentColor = imagecolortransparent($sourceImageStream);

            // copy parlette
            imagepalettecopy($targetImageStream,$sourceImageStream);

            // fill with transparent color
            imagefill($targetImageStream,0,0,$transparentColor);

            // declare the transparent color as transparent :)
            imagecolortransparent($targetImageStream,$transparentColor);

          // end if
         }

         // copy source image stream to target image stream and resize it
         imagecopyresized($targetImageStream,$sourceImageStream,0,0,0,0,$width,$height,$sourceImageWidth,$sourceImageHeight);

         // save image (if desired) or flush it to stdout
         if($sourceImageType == 'jpg'){

            if($targetImage === null){
               imagejpeg($targetImageStream,'',$jpgQuality);
             //end if
            }
            else{
               imagejpeg($targetImageStream,$targetImage,$jpgQuality);
             // end else
            }

          // end if
         }
         elseif($sourceImageType == 'gif'){

            if($targetImage === null){
               imagegif($targetImageStream);
             //end if
            }
            else{
               imagegif($targetImageStream,$targetImage);
             // end else
            }

          // end
         }
         else{

            if($targetImage === null){
               imagepng($targetImageStream);
             //end if
            }
            else{
               imagepng($targetImageStream,$targetImage);
             // end else
            }

          // end else
         }

         // cleam memory
         imagedestroy($targetImageStream);
         imagedestroy($sourceImageStream);

       // end function
      }

    // end class
   }
?>