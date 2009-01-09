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