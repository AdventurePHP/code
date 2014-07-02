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
namespace APF\core\session;

use InvalidArgumentException;

/**
 * Provides advances session handling with namespaces. Example:
 * <pre>$session = new Session('{namespace}');
 * $session->load('{key}');
 * $session->save('{key}','{value}');</pre>
 * Further, you do not have to take care of starting or persisting sessions.
 *
 * @author Christian Schäfer, Christian Achatz
 * @version
 * Version 0.1, 08.03.2006<br />
 * Version 0.2, 12.04.2006 (Added the possibility to create the class singleton.)<br />
 * Version 0.3, 02.08.2009 (Ensured PHP 5.3.x/6.x.x compatibility with session handling.)<br />
 * Version 0.4, 06.05.2013 (Renamed from SessionManager to Session to ensure better naming)<br />
 */
final class Session {

   /**
    * The namespace of the current instance of the session.
    */
   private $namespace;

   /**
    * Initializes the namespace of the current instance and starts the
    * session in case it is not started yet.
    *
    * @param string $namespace The desired namespace.
    *
    * @throws InvalidArgumentException In case, the namespace is empty.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 15.05.2013 (Changed to __construct [Tobias Lückel|Megger])<br />
    */
   public function __construct($namespace) {

      if (empty($namespace)) {
         throw new InvalidArgumentException('Session cannot be created with an empty namespace!');
      }

      $this->namespace = $namespace;

      // start session, because session_register() is
      // deprecated as of PHP 5.3. so we have to use
      // session_start(), but with an additional check :(
      if (isset($_SESSION) === false) {
         session_start();
      }

      // init the session if not existent yet to avoid array access issues and weired error messages.
      if (!isset($_SESSION[$namespace])) {
         $_SESSION[$namespace] = array();
      }

   }

   /**
    * Deletes the session for a given namespace.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 18.07.2006 (Fixed bug, that after a post request, the session was valid again!)<br />
    */
   public function destroy() {
      $_SESSION[$this->namespace] = array();
   }

   /**
    * Loads data from the session.
    *
    * @param string $attribute The desired attribute.
    * @param string $default The default value to load, when no data is available.
    *
    * @return string The desired session data or the default value (null, if not present).
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    * Version 0.2, 15.06.2006 (Now false is returned if the data is not present in the session)<br />
    * Version 0.3, 30.01.2010 (Switched return value to null to be consistent with the RequestHandler)<br />
    * Version 0.4, 28.02.2010 (Added default value behaviour)<br />
    */
   public function load($attribute, $default = null) {
      if (isset($_SESSION[$this->namespace][$attribute])) {
         return $_SESSION[$this->namespace][$attribute];
      } else {
         return $default;
      }
   }

   /**
    * Returns an associative array including all session keys registered
    * up to the point of calling this method.
    *
    * @return array{string} The complete session data list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.03.2010<br />
    */
   public function loadAll() {
      return $_SESSION[$this->namespace];
   }

   /**
    * Returns the list of registered session keys as a numeric array.
    *
    * @return string[] The list of registered session keys.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 15.03.2010<br />
    */
   public function getEntryKeys() {
      return array_keys($_SESSION[$this->namespace]);
   }

   /**
    * Saves session data.
    *
    * @param string $attribute the desired attribute
    * @param string $value the value to save
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    */
   public function save($attribute, $value) {
      $_SESSION[$this->namespace][$attribute] = $value;
   }

   /**
    * Deletes session data.
    *
    * @param string $attribute the desired attribute
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 08.03.2006<br />
    */
   public function delete($attribute) {
      unset($_SESSION[$this->namespace][$attribute]);
   }

   /**
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
