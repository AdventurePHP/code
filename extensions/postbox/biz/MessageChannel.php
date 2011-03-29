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

//<*MessageChannelBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for MessageChannel. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "MessageChannel" which will extend this base-class.
 */
import('extensions::postbox::biz::abstractdomainobjects', 'AbstractMessageChannel');
class MessageChannelBase extends AbstractMessageChannel {

    public function __construct($objectName = null){
        parent::__construct('MessageChannel');
    }

    public function getTitle() {
        return $this->getProperty('Title');
    }

    public function setTitle($value) {
        $this->setProperty('Title', $value);
        return $this;
    }

}
// DO NOT CHANGE THIS COMMENT! <*MessageChannelBase:end*>

/**
 * @package extensions::postbox::biz
 * @class MessageChannel
 * 
 * Domain object for "MessageChannel"
 * Use this class to add your own functions.
 */
class MessageChannel extends MessageChannelBase {
    /**
     * Call parent\'s function because the objectName needs to be set.
     */
    public function __construct($objectName = null){
        parent::__construct();
    }
    
}

?>