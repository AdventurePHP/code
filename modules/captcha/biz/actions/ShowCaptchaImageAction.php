<?php
   import('core::session','sessionManager');


   /**
   *  @package modules::captcha::biz::actions
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
      *  Defined the installed fonts.
      */
      var $__Fonts = array(
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

         // create sessionManager
         $sessMgr = new sessionManager('modules::captcha');

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