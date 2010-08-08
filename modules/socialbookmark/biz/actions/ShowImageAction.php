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

   session_cache_limiter('none');
   import('tools::http','HeaderManager');

   /**
   *  @package modules::socialbookmark::biz::actions
   *  @class ShowImageAction
   *
   *  Implements a front controller action to display the bookmark icons.
   *
   *  @author Christian W. Sch�fer
   *  @version
   *  Version 0.1, 07.09.2007<br />
   */
   class ShowImageAction extends AbstractFrontcontrollerAction {

      public function ShowImageAction(){
      }

      /**
      *  @public
      *
      *  Displays the image with respect of some caching headers.
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 07.09.2007<br />
      *  Version 0.2, 26.04.2009 (Added caching header)<br />
      */
      public function run(){

         // retrieve image information from the input object
         $Image = APPS__PATH.'/modules/socialbookmark/pres/image/'.$this->getInput()->getAttribute('img').'.'.$this->getInput()->getAttribute('imgext');

         // send headers to allow caching
         HeaderManager::send('Content-Type: image/'.$this->getInput()->getAttribute('imgext'));
         $delta = 7 * 24 * 60 * 60; // chaching for 7 days
         HeaderManager::send('Cache-Control: public; max-age='.$delta);
         $modifiedDate = date('D, d M Y H:i:s \G\M\T', time());
         HeaderManager::send('Last-Modified: '.$modifiedDate);
         $expiresDate = date('D, d M Y H:i:s \G\M\T', time() + $delta);
         HeaderManager::send('Expires: '.$expiresDate);

         // stream file
         readfile($Image);

         exit(0);

       // end function
      }

    // end class
   }
?>