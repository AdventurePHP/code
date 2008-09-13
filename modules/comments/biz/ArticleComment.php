<?php
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
      *  @private
      *  ID des Eintrags.
      */
      var $__ID = null;


      /**
      *  @private
      *  Name des Autors.
      */
      var $__Name;


      /**
      *  @private
      *  E-Mail des Autors.
      */
      var $__EMail;


      /**
      *  @private
      *  Kommentar.
      */
      var $__Comment;


      /**
      *  @private
      *  Datum.
      */
      var $__Date;


      /**
      *  @private
      *  Uhrzeit.
      */
      var $__Time;


      /**
      *  @private
      *  Kategorie.
      */
      var $__CategoryKey;


      function ArticleComment(){
      }

    // end class
   }
?>