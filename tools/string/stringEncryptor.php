<?php
   /**
   *  @package tools::string
   *  @class stringEncryptor
   *
   *  Abstrahiert Verschlüsselungs-Algorithmen für Strings. Dient der Generierung von Passwörtern.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 24.06.2006<br />
   *  Version 0.2, 02.06.2007 (Klasse erbt von coreObject, damit diese als ServiveObject genutzt werden kann)<br />
   */
   class stringEncryptor extends coreObject
   {

      function stringEncryptor(){
      }


      /**
      *  @public
      *
      *  Erzeugt einen Passwort-Hash mit einem statischen Salt, da variable<br />
      *  Salts bei der Authentifizierung Probleme generieren. Dazu wird der Salt<br />
      *  aus der Konfigurationsdatei {ENVIRONMENT}_encryption.txt gelesen.<br />
      *  Diese muss im Namespace config::tools::string::{CONTEXT}::iniconfig<br />
      *  vorgehalten werden.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 24.06.2006<br />
      *  Version 0.2, 02.06.2007 (Auf ConfigurationManager umgestellt)<br />
      */
      function getPasswordHash($Password,$Section = 'Standard'){

         $Config = &$this->__getConfiguration('tools::string','encryption');
         return crypt($Password,$Config->getValue($Section,'PasswortSalt'));

       // end function
      }

    // end class
   }
?>