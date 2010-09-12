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
    * @package extensions::forwardmessage::biz
    * @class ForwardMessageManager
    *
    * Stores the status messages.
    *
    * @author Daniel Seemaier
    * @version
    * Version 0.1, 10.09.2010
    */
   class ForwardMessageManager extends APFObject {

      /**
       * @private
       * Stores the status messages.
       */
      private $messages = array();

      /**
       * @public
       *
       * Adds a status message.
       *
       * @param string $name The name of the message. Needed to show/hide the message
       * in the documentcontroller.
       * @param string $message The message to add.
       * @param bool $show The default state of the message. true -> show / false -> hide
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 10.09.2010
       */
      public function addMessage($name, $message, $show = false) {
         $this->messages[$name] = array('Message' => $message, 'Show' => $show);
      }

      /**
       * @public
       *
       * Shows a message.
       *
       * @param string $name The name of the message to show.
       * @throws Exception
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 10.09.2010
       */
      public function showMessage($name) {
         if (!isset($this->messages[$name])) {
            throw new Exception('[ForwardMessageManager::showMessage()] Message "' . $name . '" does not exists.');
         }

         $this->messages[$name]['Show'] = true;
      }

      /**
       * @public
       *
       * Hides a message.
       *
       * @param string $name The name of the message to hide.
       * @throws Exception
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 11.09.2010
       */
      public function hideMessage($name) {
         if (!isset($this->messages[$name])) {
            throw new Exception('[ForwardMessageManager::hideMessage()] Message "' . $name . '" does not exists.');
         }

         $this->messages[$name]['Show'] = false;
      }

      /**
       * @public
       *
       * Gets and deletes the added messages.
       *
       * @return string[] All added messages.
       *
       * @author Daniel Seemaier
       * @version
       * Version 0.1, 10.09.2010
       */
      public function getMessages() {
         $messages = array();
         foreach ($this->messages as $message) {
            if ($message['Show']) {
               $messages[] = $message['Message'];
            }
         }

         $this->messages = array();
         return $messages;
      }

   }
?>