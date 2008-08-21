<?php
   /**
   *  @package modules::guestbook::biz
   *  @class Entry
   *
   *  Domain-Objekt für einen Eintrag.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Entry extends coreObject
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
      *  Ort des Autors.
      */
      var $__City;


      /**
      *  @private
      *  Webseite des Authors.
      */
      var $__Website;


      /**
      *  @private
      *  ICQ-Nummer des Authors.
      */
      var $__ICQ;


      /**
      *  @private
      *  MSN-ID des Authors.
      */
      var $__MSN;


      /**
      *  @private
      *  Skype-Name des Authors.
      */
      var $__Skype;


      /**
      *  @private
      *  AIM-Nummer des Authors.
      */
      var $__AIM;


      /**
      *  @private
      *  Yahoo-Kennung des Authors.
      */
      var $__Yahoo;


      /**
      *  @private
      *  Text.
      */
      var $__Text;


      /**
      *  @private
      *  Kommentare.
      */
      var $__Comments = array();


      /**
      *  @private
      *  Datum des Eintrags.
      */
      var $__Date;


      /**
      *  @private
      *  Uhrzeit des Eintrags.
      */
      var $__Time;


      function Entry(){
      }


      /**
      *  @module getComments()
      *  @public
      *
      *  Gibt die Kommentare eines Eintrags zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function getComments(){
         return $this->__Comments;
       // end function
      }


      function setComments(){
      }


      /**
      *  @module addComment()
      *  @public
      *
      *  Fügt einen Kommentar zu einem Eintrag hinzu.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function addComment($Comment){
         $this->__Comments[] = $Comment;
       // end function
      }

    // end class
   }
?>