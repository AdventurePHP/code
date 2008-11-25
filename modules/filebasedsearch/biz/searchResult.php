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
   *  @namespace modules::filebasedsearch::biz
   *  @module searchResult
   *
   *  Repräsentiert ein Such-Ergebnis-Objekt.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 16.06.2007<br />
   *  Version 0.1, 24.06.2007 (Um Title erweitert)<br />
   */
   class searchResult extends coreObject
   {

      /**
      *  @private
      *  Name der Datei, in der das Suchwort gefunden wurde.
      */
      var $__File = null;


      /**
      *  @private
      *  @since 0.2
      *  Titel des Objekts.
      */
      var $__Title = null;


      /**
      *  @private
      *  Text um das Suchwort herum.
      */
      var $__Content = null;


      /**
      *  @private
      *  Datei-Größe.
      */
      var $__Size = null;


      /**
      *  @module searchResult()
      *  @public
      *
      *  Initialisiert ein Such-Ergebnis-Objekt.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 16.06.2007<br />
      *  Version 0.1, 24.06.2007 (Um Title erweitert)<br />
      */
      function searchResult($File,$Title,$Content,$Size){

         $this->__File = $File;
         $this->__Title = $Title;
         $this->__Content = $Content;
         $this->__Size = $Size;

       // end function
      }

    // end class
   }
?>