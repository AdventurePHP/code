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

   import('tools::media::taglib','ui_mediastream');

   /**
    * @package modules::usermangement::pres::taglib
    * @class umgt_taglib_media
    *
    * Implements the image displaying tablib. Based on the *:mediastream taglib.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 26.12.2008<br />
    */
   class umgt_taglib_media extends ui_mediastream {

      public function umgt_taglib_media(){
      }

      /**
       * @public
       *
       * Overwrites the parent's onParseTime().
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       */
      public function onParseTime(){
      }

      /**
       * @public
       *
       * Overwrites the parent's onAfterAppend().
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       */
      public function onAfterAppend(){
      }

      /**
       * @public
       *
       * Returns the image tag, that includes the image resource (front controller action). The
       * tag definition also contains size and border instruction.
       *
       * @return string The image tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 26.12.2008<br />
       */
      public function transform(){

         // setup the desired attributes
         $this->__Attributes['namespace'] = 'modules::usermanagement::pres::icons';

         // execute the parent's onParseTime()
         parent::onParseTime();

         // generate the image tag
         $this->deleteAttribute('namespace');
         $this->deleteAttribute('filename');
         $this->deleteAttribute('extension');
         $this->deleteAttribute('filebody');
         $attributes = $this->__getAttributesAsString($this->__Attributes);
         $imgsrc = parent::transform();
         return '<img src="'.$imgsrc.'" '.$attributes.' style="width: 20px; height: 20px; border-width: 0px;" />';

       // end function
      }

    // end class
   }
?>