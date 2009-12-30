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

   /**
   *  @package modules::comments::biz
   *  @class ArticleComment
   *
   *  Implementiert das Business-Objekt f�r das Modul Artikel-Kommentare.<br />
   *
   *  @author Christian W. Sch�fer
   *  @version
   *  Version 0.1, 22.08.2007<br />
   *  Version 0.2, 03.09.2007 (Dokumentation erg�nzt)<br />
   */
   class ArticleComment extends coreObject
   {

      /**
      *  @protected
      *  ID des Eintrags.
      */
      protected $__ID = null;


      /**
      *  @protected
      *  Name des Autors.
      */
      protected $__Name;


      /**
      *  @protected
      *  E-Mail des Autors.
      */
      protected $__EMail;


      /**
      *  @protected
      *  Kommentar.
      */
      protected $__Comment;


      /**
      *  @protected
      *  Datum.
      */
      protected $__Date;


      /**
      *  @protected
      *  Uhrzeit.
      */
      protected $__Time;


      /**
      *  @protected
      *  Kategorie.
      */
      protected $__CategoryKey;


      function ArticleComment(){
      }

    // end class
   }
?>