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
 * @author Daniel Seemaier, Werner Liemberger
 * @version
 * Version 0.1, 10.09.2010
 * Version 0.2 25.2.2011, Group option added by Werner Liemberger
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
     * @param string $group The group of the message.
     *
     * @author Daniel Seemaier, Werner Liemberger
     * @version
     * Version 0.1, 10.09.2010
     * Version 0.2 25.2.2011, Group option added by Werner Liemberger
     */
    public function addMessage($name, $message, $show = false, $group='message') {
        if ($group == '') {
            $group = 'message';
        }
        $this->messages[$group][$name] = array('Message' => $message, 'Show' => $show);
    }

    /**
     * @public
     *
     * Shows a message.
     *
     * @param string $name The name of the message to show.
     * @param string $group The group of the message.
     * @throws Exception
     *
     * @author Daniel Seemaier, Werner Liemberger
     * @version
     * Version 0.1, 10.09.2010
     * Version 0.2 25.2.2011, Group option added by Werner Liemberger
     */
    public function showMessage($name, $group='message') {
        if ($group == "") {
            $group = "message";
        }
        if (!isset($this->messages[$group][$name])) {
            throw new Exception('[ForwardMessageManager::showMessage()] Message "' . $name . '" does not exists in group ' . $group);
        }

        $this->messages[$group][$name]['Show'] = true;
    }

    /**
     * @public
     *
     * Hides a message.
     *
     * @param string $name The name of the message to hide.
     * @param string $group The group of the message.
     * @throws Exception
     *
     * @author Daniel Seemaier, Werner Liemberger
     * @version
     * Version 0.1, 11.09.2010
     * Version 0.2 25.2.2011, Group option added by Werner Liemberger
     */
    public function hideMessage($name, $group='message') {
        if ($group == '') {
            $group = 'message';
        }
        if (!isset($this->messages[$group][$name])) {
            throw new Exception('[ForwardMessageManager::hideMessage()] Message "' . $name . '" does not exists in group' . $group);
        }

        $this->messages[$group][$name]['Show'] = false;
    }

    /**
     * @public
     *
     * Gets and deletes the added messages.
     *
     * @param array $groups The groups which should be displayed.
     * @return string[] All added messages.
     *
     * @author Daniel Seemaier, Werner Liemberger
     * @version
     * Version 0.1, 10.09.2010
     * Version 0.2 25.2.2011, Group option added by Werner Liemberger
     */
    public function getMessages($groups=array()) {

        // Falls keine Gruppe angegeben wurde, werden alle aufgelistet.
        if (count($groups) == 0) {
            $groups = $this->messages;
        }

        $messages = array();
        foreach ($groups as $group) {
            if (isset($this->messages[$group]) && is_array($this->messages[$group])) {
                foreach ($this->messages[$group] as $key => $message) {
                    if ($message['Show']) {
                        $messages[] = $message['Message'];
                        //Delete Messages after they are displayed
                        unset($this->messages[$group][$key]);
                    }
                }
            }
        }
        #$this->messages = array();
        return $messages;
    }

}
?>