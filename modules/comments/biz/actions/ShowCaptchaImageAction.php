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

   import('core::session','sessionManager');


   /**
   *  @namespace modules::comments::biz::actions
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