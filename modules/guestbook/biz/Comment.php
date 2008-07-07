<?php
   /**
   *  @package modules::guestbook::biz
   *  @class Comment
   *
   *  Domain-Objekt für einen Kommentar.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Comment extends coreObject
   {

      /**
      *  @private
      *  ID des Kommentars.
      */
      var $__ID = null;


      /**
      *  @private
      *  Titel des Kommentars.
      */
      var $__Title;


      /**
      *  @private
      *  Datum des Kommentars.
      */
      var $__Date;


      /**
      *  @private
      *  Text des Kommentars.
      */
      var $__Text;


      /**
      *  @private
      *  Uhrzeit des Kommentars.
      */
      var $__Time;


      function Comment(){
      }

    // end class
   }
?>