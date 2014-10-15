<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\modules\captcha\biz\actions;

use APF\core\frontcontroller\AbstractFrontcontrollerAction;
use APF\core\http\HeaderImpl;
use APF\core\loader\RootClassLoader;
use APF\core\session\Session;

/**
 * Front controller action that displays a captcha image.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 20.07.2008<br />
 */
class ShowCaptchaImageAction extends AbstractFrontcontrollerAction {

   /**
    * Defines the installed fonts.
    *
    * @var array $fonts
    */
   private $fonts = array('XFILES.TTF');

   /**
    * Implements the front controller's run method.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.07.2008<br />
    */
   public function run() {

      $session = new Session('APF\modules\captcha');

      // read captcha string from the session.
      $CaptchaStringName = $this->getInput()->getParameter('name');
      $text = $session->load($CaptchaStringName);

      // choose background
      $rootPath = $this->getRootPath();
      $background = $rootPath . '/modules/captcha/pres/images/captcha_' . rand(1, 12) . '.png';

      // create image from background
      $img = ImageCreateFromPNG($background);

      // define font color
      $color = ImageColorAllocate($img, 0, 0, 0);

      // define font type
      $font = $rootPath . '/modules/captcha/pres/fonts/' . $this->fonts[rand(0, count($this->fonts) - 1)];

      // define font size
      $fontSize = 25;

      // define the angle
      $angle = rand(0, 5);

      // define start point x
      $t_x = 10;

      // define start point y
      $t_y = 35;

      // insert text into image
      imagettftext($img, $fontSize, $angle, $t_x, $t_y, $color, $font, $text);

      // display image
      $response = self::getResponse();
      $response->setHeader(new HeaderImpl('Content-Type', 'image/png'));
      $response->setHeader(new HeaderImpl('Cache-Control', 'private'));
      $response->setHeader(new HeaderImpl('Pragma', 'no-cache'));
      $response->setHeader(new HeaderImpl('Expires', '0'));

      $response->send(false);

      imagepng($img);

      // free memory
      imagedestroy($img);
   }

   /**
    * @return string The root path of the APF code base.
    */
   private function getRootPath() {
      return RootClassLoader::getLoaderByVendor('APF')->getRootPath();
   }

}
