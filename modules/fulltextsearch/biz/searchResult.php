<?php
   /**
   *  @package modules::fulltextsearch::biz
   *  @module searchResult
   *
   *  Domain-Objekt f�r das Modul "fulltextsearch".<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 08.03.2008<br />
   */
   class searchResult extends coreObject
   {

      /**
      *  @private
      *  Name der Content-Datei (f�r Link!).
      */
      var $__Name;


      /**
      *  @private
      *  Titel der Seite.
      */
      var $__Title;


      /**
      *  @private
      *  Sprache der Seite.
      */
      var $__Language;


      /**
      *  @private
      *  Letzte �nderung.
      */
      var $__LastMod;


      /**
      *  @private
      *  Anzahl der W�rter im Index.
      */
      var $__WordCount;


      /**
      *  @private
      *  Fundstelle.
      */
      var $__IndexWord;


      function searchResult(){
      }

    // end class
   }
?>