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

   /**
    * @package modules::socialbookmark::biz
    * @class bookmarkEntry
    *
    * Represents a single bookmark service (e.g. google, technorati, ...).
    *
    * @author Christian W. Sch�fer
    * @version
    * Version 0.1, 02.06.2007<br />
    */
   class bookmarkEntry extends coreObject {

      /**
       * @protected
       * @var string Basis-URL des Bookmark-Services.
       */
      protected $__ServiceBaseURL;

      /**
       * @protected
       * @var string Name des URL-Parameters f�r die zu bookmarkende URL.
       */
      protected $__ServiceParams_URL;


      /**
       * @protected
       * @var string Name des Titel-Parameters f�r die zu bookmarkende URL.
       */
      protected $__ServiceParams_Title;

      /**
       * @protected
       * @var string Titel des Bookmark-Eintrags (Link-Titel, Alt-Text).
       */
      protected $__Title;

      /**
       * @protected
       * @var string URL des Bookmark-Icons ohne Endung.
       */
      protected $__ImageURL;

      /**
       * @protected
       * @var string Endung des Bookmark-Icons.
       */
      protected $__ImageExt;

      /**
       * @public
       *
       * @param string $baseURL Basis-URL des Bookmark-Services.
       * @param string $bookmarkURL Name des URL-Parameters.
       * @param string $bookmarkTitle Name des Titel-Parameter.
       * @param string $title Titel des Services.
       * @param string $imageURL Bildname ohne Endung.
       * @param string $imageExt Endung des Bildes.
       *
       * @author Christian W. Sch�fer
       * @version
       * Version 0.1, 02.06.2007<br />
       */
      function bookmarkEntry($baseURL,$bookmarkURL,$bookmarkTitle,$title,$imageURL,$imageExt){
         $this->__ServiceBaseURL = $baseURL;
         $this->__ServiceParams_URL = $bookmarkURL;
         $this->__ServiceParams_Title = $bookmarkTitle;
         $this->__Title = $title;
         $this->__ImageURL = $imageURL;
         $this->__ImageExt = $imageExt;
       // end function
      }

    // end class
   }
?>