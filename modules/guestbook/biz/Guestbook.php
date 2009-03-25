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
   *  @class Guestbook
   *
   *  Domain-Objekt f�r einen G�stebuch.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Guestbook extends coreObject
   {

      /**
      *  @private
      *  ID des G�stebuchs.
      */
      var $__ID = null;


      /**
      *  @private
      *  Name des G�stebuchs.
      */
      var $__Name;


      /**
      *  @private
      *  Beschreibung des G�stebuchs.
      */
      var $__Description;


      /**
      *  @private
      *  Eintr�ge des G�stebuchs.
      */
      var $__Entries = array();


      /**
      *  @private
      *  Admin-Benutzername.
      */
      var $__Admin_Username;


      /**
      *  @private
      *  Admin-Passwort.
      */
      var $__Admin_Password;


      function Guestbook(){
      }


      /**
      *  @public
      *
      *  Gibt die Eintr�ge des G�stebuchs zur�ck.<br />
      *
      *  @return array $Entries; Liste mit Entry-Objekten
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function getEntries(){
         return $this->__Entries;
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die Eintr�ge des G�stebuchs zur�ck.<br />
      *
      *  @param array $Entries; Liste mit Entry-Objekten
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function setEntries($Entries){
         $this->__Entries = $Entries;
       // end function
      }


      /**
      *  @public
      *
      *  F�gt einen Eintrag zum G�stebuch hinzu.<br />
      *
      *  @param Entry $Entry; Entry-Objekt
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      function addEntry($Entry){
         $this->__Entries[] = $Entry;
       // end function
      }

    // end class
   }
?>