<?php
   /**
   *  @package core::session
   *  @class sessionManager
   *
   *  Stellt ein globales Session-Handling bereit.<br />
   *  <br />
   *  Verwendungsbeispiel:
   *     $oSessMgr = new sessionManager('<namespace>');
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 08.03.2006<br />
   *  Version 0.2, 12.04.2006 (Möglichkeit hinzugefügt die Klasse singleton instanzieren zu können)<br />
   */
   class sessionManager
   {

      /**
      *  @private
      *  Namespace der aktuellen Instanz.
      */
      var $__Namespace;


      /**
      *  @public
      *
      *  Konstruktor der Klasse.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function sessionManager($Namespace = ''){

         // Namespace setzen
         if($Namespace != ''){
            $this->setNamespace($Namespace);
          // end if
         }

         // Session initialisieren, falls noch nicht vorhanden
         if(!isset($_SESSION[$Namespace])){
            $this->createSession($Namespace);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Setzt den Namespace des aktuellen Instanz des sessionManager's.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function setNamespace($Namespace){
         $this->__Namespace = trim($Namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Erzeugt einen Session-Namespace.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function createSession($Namespace){
         session_register($Namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Löscht die Session im angegebenen Namespace.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 18.07.2006 (Bug behoben, dass nach einem neuen Post die Session wieder gültig war (Server w3service.net)!)<br />
      */
      function destroySession($Namespace){

         // Macht Probleme:
         // session_unregister($Namespace);
         // unset($_SESSION[$Namespace]);

         // Funktioniert:
         $_SESSION[$Namespace] = array();

       // end function
      }


      /**
      *  @public
      *
      *  Läd Benutzer-Daten aus der Session.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 15.06.2006 (Sollte ein Element nicht in der Session vorhanden sein, wird nun false statt '' zurückgegeben)<br />
      */
      function loadSessionData($Attribute){

         if(isset($_SESSION[$this->__Namespace][$Attribute])){
            return $_SESSION[$this->__Namespace][$Attribute];
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Läd Benutzer-Daten aus der Session unter Angabe des Namespaces.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 15.06.2006 (Sollte ein Element nicht in der Session vorhanden sein, wird nun false statt '' zurückgegeben)<br />
      */
      function loadSessionDataByNamespace($Namespace,$Attribute){

         if(isset($_SESSION[$Namespace][$Attribute])){
            return $_SESSION[$Namespace][$Attribute];
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Speichert Benutzer-Daten in die Session.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function saveSessionData($Attribute,$Value){
         $_SESSION[$this->__Namespace][$Attribute] = $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Speichert Benutzer-Daten in die Session.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function saveSessionDataByNamespace($Namespace,$Attribute,$Value){
         $_SESSION[$Namespace][$Attribute] = $Value;
       // end function
      }


      /**
      *  @public
      *
      *  Löscht Benutzer-Daten in die Session.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function deleteSessionData($Attribute){
         unset($_SESSION[$this->__Namespace][$Attribute]);
       // end function
      }


      /**
      *  @public
      *
      *  Löscht Benutzer-Daten in die Session.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function deleteSessionDataByNamespace($Namespace,$Attribute){
         unset($_SESSION[$Namespace][$Attribute]);
       // end function
      }


      /**
      *  @public
      *
      *  Gibt die aktuelle Session-ID zurück.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function getSessionID(){
         return session_id();
       // end function
      }

    // end class
   }
?>