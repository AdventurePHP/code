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

   import('modules::socialbookmark::biz','socialBookmarkManager');

   /**
    * @package modules::socialbookmark::pres::taglib
    * @class social_taglib_bookmark
    *
    * Implementiert eine TagLib f�r die Ausgabe von Bookmarks per Tag.<br />
    * Optional k�nnen die Parameter<br />
    * <br />
    * - width (Breite der Bookmark-Icons)<br />
    * - height (H�he der Bookmark-Icons)<br />
    * <br />
    * angegeben werden. Beispiel:<br />
    * <br />
    * &lt;social:bookmark width="16" height="16"/&gt;<br />
    * <br />
    * Um das Tag verwenden zu k�nnen muss der BookmarkManager konfiguriert sein!<br />
    *
    * @author Christian W. Sch�fer
    * @version
    * Version 0.1, 08.09.2007<br />
    */
   class social_taglib_bookmark extends Document {

      /**
       * @public
       *
       * Initialisiert die ben�tigten Attribute.
       *
       * @author Christian W. Sch�fer
       * @version
       * Version 0.1, 08.09.2007<br />
       */
      function social_taglib_bookmark(){
         $this->setAttribute('width','20');
         $this->setAttribute('height','20');
         $this->setAttribute('title',null);
         $this->setAttribute('url',null);
         $this->setAttribute('target',null);
       // end function
      }

      /**
      *  @public
      *
      *  Erzeugt die Ausgabe mit Hilfe des BookmarkManager.<br />
      *
      *  @author Christian W. Sch�fer
      *  @version
      *  Version 0.1, 08.09.2007<br />
      *  Version 0.2, 16.09.2007 (Attribute url, title und target hinzugef�gt)<br />
      */
      function transform(){

         // Bookmark-Manager holen
         $sBM = &$this->__getServiceObject('modules::socialbookmark::biz','socialBookmarkManager');

         // Breite und H�he konfigurieren
         $sBM->set('Width',$this->__Attributes['width']);
         $sBM->set('Height',$this->__Attributes['height']);

         // URL-Parameter konfigurieren
         if($this->__Attributes['url'] != null){
            $sBM->set('URL',$this->__Attributes['url']);
          // end if
         }
         if($this->__Attributes['title'] != null){
            $sBM->set('Title',$this->__Attributes['title']);
          // end if
         }
         if($this->__Attributes['target'] != null){
            $sBM->set('Target',$this->__Attributes['target']);
          // end if
         }

         // Bookmark-Quelltext zur�ckliefern
         return $sBM->getBookmarkCode();

       // end function
      }

    // end class
   }
?>