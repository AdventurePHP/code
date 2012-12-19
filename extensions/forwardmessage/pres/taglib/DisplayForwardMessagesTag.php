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
 * @package extensions::forwardmessage::pres::taglib
 * @class DisplayForwardMessagesTag
 *
 * @param string $groups String of groups that should be displayed. Separated by "," (e.g "message,error")
 * @param string $delimiter
 * @return string Returns the added flash messages.
 *
 * @example
 * <code>
 * <core:addtaglib
 *    namespace="extensions::forwardmessage::pres::taglib"
 *    class="DisplayForwardMessagesTag"
 *    prefix="html"
 *    name="getforwardmessages"
 * />
 * <html:getforwardmessages
 *    [groups="..."]
 *    [delimiter="..."]
 * />
 * </code>
 *
 * @author Daniel Seemaier, Werner Liemberger
 * @version
 * Version 0.1, 10.09.2010<br />
 * Version 0.2, 25.2.2011, Group option added by Werner Liemberger
 */
class DisplayForwardMessagesTag extends Document {

   public function transform() {
      return implode($this->getAttribute('delimiter', ''), $this->getMessages());
   }

   /**
    * @protected
    *
    * Retrieves the relevant messages from the central store.
    *
    * @return string[] The list of messages.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.03.2012<br />
    */
   protected function getMessages() {
      $groups = $this->getAttribute('groups');
      if (empty($groups)) {
         $groups = array();
      } else {
         $groups = explode(',', $groups);
      }

      /* @var $manager ForwardMessageManager */
      $manager = &$this->getServiceObject('extensions::forwardmessage::biz', 'ForwardMessageManager', APFService::SERVICE_TYPE_SESSION_SINGLETON);
      return $manager->getMessages($groups);
   }

}
