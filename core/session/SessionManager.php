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
   *  @namespace core::session
   *  @class SessionManager
   *
   *  Provides advances session handling with namespaces. Example:
   *  <pre>$sessMgr = new SessionManager('<namespace>');
   *  $sessMgr->loadSessionData('<key>');
   *  $sessMgr->saveSessionData('<key>','<value>');</pre>
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 08.03.2006<br />
   *  Version 0.2, 12.04.2006 (Added the possibility to create the class singleton.)<br />
   */
   final class SessionManager
   {

      /**
      *  @private
      *  Namespace der aktuellen Instanz.
      */
      private $__Namespace;


      /**
      *  @public
      *
      *  Constructor. Initializes the namespace of the current instance.
      *
      *  @param string $namespace The desired namespace.
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function SessionManager($namespace = ''){

         // set namespace
         if($namespace != ''){
            $this->setNamespace($namespace);
          // end if
         }

         // init the session if not existent yet
         if(!isset($_SESSION[$namespace])){
            $this->createSession($namespace);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Sets the namespace of the current instance of the SessionManager.
      *
      *  @param string $namespace the desired namespace
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function setNamespace($namespace){
         $this->__Namespace = trim($namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Creates a session for the current namespace.
      *
      *  @param string $namespace the desired namespace
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function createSession($namespace){
         session_register($namespace);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes the session for a given namespace.
      *
      *  @param string $namespace the desired namespace
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 18.07.2006 (Fixed bug, that after a post request, the session was valid again (Server: w3service.net)!)<br />
      */
      function destroySession($namespace){

         // Is not working!
         // session_unregister($Namespace);
         // unset($_SESSION[$Namespace]);

         // works!
         $_SESSION[$namespace] = array();

       // end function
      }


      /**
      *  @public
      *
      *  Loads data from the session.
      *
      *  @param string $attribute the desired attribute
      *  @return string $data the desired session data of (bool)false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
      */
      function loadSessionData($attribute){

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
      *  @public
      *
      *  Loads data from session using an explicit namespace.
      *
      *  @param string $namespace the namespace of the value
      *  @param string $attribute the desired attribute
      *  @return string $data the desired session data of (bool)false
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      *  Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
      */
      function loadSessionDataByNamespace($namespace,$attribute){

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
      *  @public
      *
      *  Saves session data.
      *
      *  @param string $attribute the desired attribute
      *  @param string $value the value to save
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function saveSessionData($attribute,$value){
         $_SESSION[$this->__Namespace][$attribute] = $value;
       // end function
      }


      /**
      *  @public
      *
      *  Saves session data using an explicit namespace.
      *
      *  @param string $namespace the namespace of the value
      *  @param string $attribute the desired attribute
      *  @param string $value the value to save
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function saveSessionDataByNamespace($namespace,$attribute,$value){
         $_SESSION[$namespace][$attribute] = $value;
       // end function
      }


      /**
      *  @public
      *
      *  Deletes session data.
      *
      *  @param string $attribute the desired attribute
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function deleteSessionData($attribute){
         unset($_SESSION[$this->__Namespace][$attribute]);
       // end function
      }


      /**
      *  @public
      *
      *  Deletes session data using an explicit namespace.
      *
      *  @param string $namespace the namespace of the value
      *  @param string $attribute the desired attribute
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 08.03.2006<br />
      */
      function deleteSessionDataByNamespace($namespace,$attribute){
         unset($_SESSION[$namespace][$attribute]);
       // end function
      }


      /**
      *  @public
      *
      *  Returns the id of the current session.
      *
      *  @return string $sessionID the current session id
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