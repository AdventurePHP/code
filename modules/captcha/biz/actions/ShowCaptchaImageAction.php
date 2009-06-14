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

   import('core::session','SessionManager');


   /**
   *  @namespace modules::captcha::biz::actions
   *  @class ShowCaptchaImageAction
   *
   *  Front controller action that displays a captcha image.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 20.07.2008<br />
   */
   class ShowCaptchaImageAction extends AbstractFrontcontrollerAction
   {

      /**
      *  @private
      *  Defines the installed fonts.
      */
      private $__Fonts = array(
                           'XFILES.TTF'
                           );


      function ShowCaptchaImageAction(){
      }


      /**
      *  @public
      *
      *  Implements the front controller's run method.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 20.07.2008<br />
      */
      function run(){

         $sessMgr = new SessionManager('modules::captcha');

         // read captcha string from the session.
         $CaptchaStringName = $this->__Input->getAttribute('name');
         $text = $sessMgr->loadSessionData($CaptchaStringName);

         // choose background
         $background = APPS__PATH.'/modules/captcha/pres/images/captcha_'.rand(1,12).'.png';

         // create image from background
         $img = ImageCreateFromPNG($background);

         // define font color
         $color = ImageColorAllocate($img,0,0,0);

         // define font type
         $ttf = APPS__PATH.'/modules/captcha/pres/fonts/'.$this->__Fonts[rand(0,count($this->__Fonts) - 1)];

         // define font size
         $ttfsize = 25;

         // Winkel definieren
         $angle = rand(0,5);

         // define start point x
         $t_x = 10;

         // define start point y
         $t_y = 35;

         // insert text into image
         imagettftext($img,$ttfsize,$angle,$t_x,$t_y,$color,$ttf,$text);

         // display image
         header('Content-Type: image/png');
         imagepng($img);

         // free memory
         imagedestroy($img);

         // end program
         exit();

       // end function
      }

    // end function
   }
?>