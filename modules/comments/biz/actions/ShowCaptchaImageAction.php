<?php
   import('core::session','sessionManager');


   /**
   *  @package modules::comments::biz::actions
   *  @class ShowCaptchaImageAction
   *
   *  FrontController Action zum Anzeigen eines Captcha-Bildes für das Kommentar-Modul.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 28.12.2007
   */
   class ShowCaptchaImageAction extends AbstractFrontcontrollerAction
   {

      /**
      *  @private
      *  Installierte Schriften.
      */
      var $__Fonts = array(
                           'XFILES.TTF'
                           );


      function ShowCaptchaImageAction(){
      }


      /**
      *  @public
      *
      *  Implementiert die abstrakte run()-Methode aus AbstractFrontcontrollerAction.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 28.12.2007
      */
      function run(){

         // sessionManager erzeugen
         $sessMgr = new sessionManager('modules::comment');

         // Text aus der Session auslesen. Text wurde in der Anwendung
         // generiert und im Model der Anwendung gespeichert.
         $text = $sessMgr->loadSessionData('CAPTCHA_STRING');

         // Hintergrund wählen
         $background = APPS__PATH.'/modules/comments/pres/images/captcha_'.rand(1,12).'.png';

         // Bild vom Hintergrund generieren
         $img = ImageCreateFromPNG($background);

         // Farbe der Schrift definieren
         $color = ImageColorAllocate($img,0,0,0);

         // Schriftart definieren
         $ttf = APPS__PATH.'/modules/comments/pres/fonts/'.$this->__Fonts[rand(0,count($this->__Fonts) - 1)];

         // Schriftgröße definieren
         $ttfsize = 25;

         // Winkel definieren
         $angle = rand(0,5);

         // Startpunkt x generieren
         $t_x = 10;

         // Startpunkt y definieren
         $t_y = 35;

         // Text einfügen
         imagettftext($img,$ttfsize,$angle,$t_x,$t_y,$color,$ttf,$text);

         // Bild flushen
         header('Content-Type: image/png');
         imagepng($img);

         // Bild aus dem Speicher entfernen
         imagedestroy($img);

         // Programm beenden
         exit();

       // end function
      }

    // end function
   }
?>