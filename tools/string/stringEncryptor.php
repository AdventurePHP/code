<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   /**
    * @package tools::string
    * @class stringEncryptor
    *
    * Providers password encryption services using a configured salt.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.06.2006<br />
    * Version 0.2, 02.06.2007 (Now inherits from APFObject to be able to use as service object)<br />
    */
   class stringEncryptor extends APFObject {

      /**
       * @public
       *
       * Erzeugt einen Passwort-Hash mit einem statischen Salt, da variable<br />
       * Salts bei der Authentifizierung Probleme generieren. Dazu wird der Salt<br />
       * aus der Konfigurationsdatei {ENVIRONMENT}_encryption.txt gelesen.<br />
       * Diese muss im Namespace config::tools::string::{CONTEXT}::iniconfig<br />
       * vorgehalten werden.<br />
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 24.06.2006<br />
       * Version 0.2, 02.06.2007 (Switched to ConfigurationManager)<br />
       */
      public function getPasswordHash($password,$section = 'Standard'){

         $config = $this->getConfiguration('tools::string','encryption.ini');
         return crypt($password,$config->getSection($name)->getValue('PasswortSalt'));

       // end function
      }

    // end class
   }
?>