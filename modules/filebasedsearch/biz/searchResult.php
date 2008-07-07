<?php
   /**
   *  @package modules::filebasedsearch::biz
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