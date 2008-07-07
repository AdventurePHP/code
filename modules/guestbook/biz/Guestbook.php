<?php
   /**
   *  @package modules::guestbook::biz
   *  @class Guestbook
   *
   *  Domain-Objekt für einen Gästebuch.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Guestbook extends coreObject
   {

      /**
      *  @private
      *  ID des Gästebuchs.
      */
      var $__ID = null;


      /**
      *  @private
      *  Name des Gästebuchs.
      */
      var $__Name;


      /**
      *  @private
      *  Beschreibung des Gästebuchs.
      */
      var $__Description;


      /**
      *  @private
      *  Einträge des Gästebuchs.
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
      *  Gibt die Einträge des Gästebuchs zurück.<br />
      *
      *  @return array $Entries; Liste mit Entry-Objekten
      *
      *  @author Christian Schäfer
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
      *  Gibt die Einträge des Gästebuchs zurück.<br />
      *
      *  @param array $Entries; Liste mit Entry-Objekten
      *
      *  @author Christian Schäfer
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
      *  Fügt einen Eintrag zum Gästebuch hinzu.<br />
      *
      *  @param Entry $Entry; Entry-Objekt
      *
      *  @author Christian Schäfer
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