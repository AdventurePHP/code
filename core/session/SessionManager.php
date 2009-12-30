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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
    * @package core::session
    * @class SessionManager
    *
    * Provides advances session handling with namespaces. Example:
    * <pre>$sessMgr = new SessionManager('<namespace>');
    * $sessMgr->loadSessionData('<key>');
    * $sessMgr->saveSessionData('<key>','<value>');</pre>
    * Further, you do not have to take care of starting or persisting sessions.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 12.04.2006 (Added the possibility to create the class singleton.)<br />
    * Version 0.3, 02.08.2009 (Ensured PHP 5.3.x/6.x.x compatibility with session handling.)<br />
    */
   final class SessionManager {

      /**
       * @private
       * The namespace of the current instance of the session manager.
       */
      private $__Namespace;

      /**
       * @public
       *
       * Initializes the namespace of the current instance and starts the
       * session in case it is not started yet.
       *
       * @param string $namespace The desired namespace.
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function SessionManager($namespace = null){

         // set namespace
         if($namespace !== null){
            $this->setNamespace($namespace);
          // end if
         }

         // start session, because session_register() is
         // deprecated as of PHP 5.3. so we have to use 
         // session_start(), but with an additional check :(
         if(isset($_SESSION) === false){
            session_start();
          // end if
         }

         // init the session if not existent yet.
         if(!isset($_SESSION[$namespace])){
            $_SESSION[$namespace] = array();
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Sets the namespace of the current instance of the SessionManager.
       *
       * @param string $namespace the desired namespace
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function setNamespace($namespace){
         $this->__Namespace = trim($namespace);
       // end function
      }

      /**
       * @public
       *
       * Deletes the session for a given namespace.
       *
       * @param string $namespace the desired namespace
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       * Version 0.2, 18.07.2006 (Fixed bug, that after a post request, the session was valid again (Server: w3service.net)!)<br />
       */
      public function destroySession($namespace){
         $_SESSION[$namespace] = array();
       // end function
      }

      /**
       * @public
       *
       * Loads data from the session.
       *
       * @param string $attribute the desired attribute
       * @return string The desired session data of (bool)false
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       * Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
       */
      public function loadSessionData($attribute){

         if(isset($_SESSION[$this->__Namespace][$attribute])){
            return $_SESSION[$this->__Namespace][$attribute];
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Loads data from session using an explicit namespace.
       *
       * @param string $namespace the namespace of the value
       * @param string $attribute the desired attribute
       * @return string The desired session data of (bool)false
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       * Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
       */
      public function loadSessionDataByNamespace($namespace,$attribute){

         if(isset($_SESSION[$namespace][$attribute])){
            return $_SESSION[$namespace][$attribute];
          // end if
         }
         else{
            return false;
          // end else
         }

       // end function
      }

      /**
       * @public
       *
       * Saves session data.
       *
       * @param string $attribute the desired attribute
       * @param string $value the value to save
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function saveSessionData($attribute,$value){
         $_SESSION[$this->__Namespace][$attribute] = $value;
       // end function
      }

      /**
       * @public
       *
       * Saves session data using an explicit namespace.
       *
       * @param string $namespace the namespace of the value
       * @param string $attribute the desired attribute
       * @param string $value the value to save
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function saveSessionDataByNamespace($namespace,$attribute,$value){
         $_SESSION[$namespace][$attribute] = $value;
       // end function
      }

      /**
       * @public
       *
       * Deletes session data.
       *
       * @param string $attribute the desired attribute
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      function deleteSessionData($attribute){
         unset($_SESSION[$this->__Namespace][$attribute]);
       // end function
      }

      /**
       * @public
       *
       * Deletes session data using an explicit namespace.
       *
       * @param string $namespace the namespace of the value
       * @param string $attribute the desired attribute
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function deleteSessionDataByNamespace($namespace,$attribute){
         unset($_SESSION[$namespace][$attribute]);
       // end function
      }

      /**
       * @public
       *
       * Returns the id of the current session.
       *
       * @return string The current session id
       *
       * @author Christian Schäfer
       * @version
       * Version 0.1, 08.03.2006<br />
       */
      public function getSessionID(){
         return session_id();
       // end function
      }

    // end class
   }
?>