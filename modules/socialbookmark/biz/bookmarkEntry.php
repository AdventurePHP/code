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

   /**
   *  @namespace modules::socialbookmark::biz
   *  @class bookmarkEntry
   *
   *  Bookmark-Service-Objekt.<br />
   *
   *  @author Christian W. Schäfer
   *  @version
   *  Version 0.1, 02.06.2007<br />
   */
   class bookmarkEntry extends coreObject
   {

      /**
      *  @private
      *  Basis-URL des Bookmark-Services.
      */
      var $__ServiceBaseURL;


      /**
      *  @private
      *  Name des URL-Parameters für die zu bookmarkende URL.
      */
      var $__ServiceParams_URL;


      /**
      *  @private
      *  Name des Titel-Parameters für die zu bookmarkende URL.
      */
      var $__ServiceParams_Title;


      /**
      *  @private
      *  Titel des Bookmark-Eintrags (Link-Titel, Alt-Text).
      */
      var $__Title;


      /**
      *  @private
      *  URL des Bookmark-Icons ohne Endung.
      */
      var $__ImageURL;


      /**
      *  @private
      *  Endung des Bookmark-Icons.
      */
      var $__ImageExt;


      /**
      *  @public
      *
      *  @param string $ServiceBaseURL; Basis-URL des Bookmark-Services
      *  @param string $ServiceParams_URL; Name des URL-Parameters
      *  @param string $ServiceParams_Title; Name des Titel-Parameter
      *  @param string $Title; Titel des Services
      *  @param string $ImageURL; Bildname ohne Endung
      *  @param string $ImageExt; Endung des Bildes
      *
      *  @author Christian W. Schäfer
      *  @version
      *  Version 0.1, 02.06.2007<br />
      */
      function bookmarkEntry($ServiceBaseURL,$ServiceParams_URL,$ServiceParams_Title,$Title,$ImageURL,$ImageExt){

         $this->__ServiceBaseURL = $ServiceBaseURL;
         $this->__ServiceParams_URL = $ServiceParams_URL;
         $this->__ServiceParams_Title = $ServiceParams_Title;
         $this->__Title = $Title;
         $this->__ImageURL = $ImageURL;
         $this->__ImageExt = $ImageExt;

       // end function
      }

    // end class
   }
?>