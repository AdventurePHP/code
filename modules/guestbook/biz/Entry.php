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
   *  @namespace modules::guestbook::biz
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