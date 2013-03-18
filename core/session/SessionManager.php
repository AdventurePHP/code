<?php
namespace APF\core\session;

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
   private $namespace;

   /**
    * @public
    *
    * Initializes the namespace of the current instance and starts the
    * session in case it is not started yet.
    *
    * @param string $namespace The desired namespace.
    * @throws \Exception In case, the namespace is empty.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    */
   public function SessionManager($namespace = '') {

      // change in 1.12: session manager must not be created without a namespace!
      if (empty($namespace)) {
         throw new \Exception('Session manager cannot be created using an empty session namespace!', E_USER_ERROR);
      }

      // set namespace
      $this->namespace = $namespace;

      // start session, because session_register() is
      // deprecated as of PHP 5.3. so we have to use
      // session_start(), but with an additional check :(
      if (isset($_SESSION) === false) {
         session_start();
      }

      // init the session if not existent yet.
      if (!isset($_SESSION[$namespace])) {
         $_SESSION[$namespace] = array();
      }

   }

   /**
    * @public
    *
    * Deletes the session for a given namespace.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 18.07.2006 (Fixed bug, that after a post request, the session was valid again (Server: w3service.net)!)<br />
    */
   public function destroySession() {
      $_SESSION[$this->namespace] = array();
   }

   /**
    * @public
    *
    * Loads data from the session.
    *
    * @param string $attribute The desired attribute.
    * @param string $default The default value to load, when no data is available.
    * @return string The desired session data or the default value (null, if not present).
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
    * Version 0.3, 30.01.2010 (Switched return value to null to be consistent with the RequestHandler)<br />
    * Version 0.4, 28.02.2010 (Added default value behaviour)<br />
    */
   public function loadSessionData($attribute, $default = null) {
      if (isset($_SESSION[$this->namespace][$attribute])) {
         return $_SESSION[$this->namespace][$attribute];
      } else {
         return $default;
      }
   }

   /**
    * @public
    *
    * Returns an associative array including all session keys registered
    * up to the point of calling this method.
    *
    * @return array{string} The complete session data list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.03.2010<br />
    */
   public function loadAllSessionData() {
      return $_SESSION[$this->namespace];
   }

   /**
    * @public
    *
    * Returns the list of registered session keys as a numeric array.
    *
    * @return array{string} The list of registered session keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.03.2010<br />
    */
   public function getEntryDataKeys() {
      return array_keys($_SESSION[$this->namespace]);
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
   public function saveSessionData($attribute, $value) {
      $_SESSION[$this->namespace][$attribute] = $value;
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
   public function deleteSessionData($attribute) {
      unset($_SESSION[$this->namespace][$attribute]);
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
   public function getSessionID() {
      return session_id();
   }

}
