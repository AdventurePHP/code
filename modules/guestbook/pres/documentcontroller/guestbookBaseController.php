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
    * @package modules::guestbook::pres::documentcontroller
    * @class guestbookBaseController
    * @abstract
    *
    * Provides basic functionality for the guestbook's MVC controllers.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   abstract class guestbookBaseController extends base_controller {

      /**
       *  @private
       *
       *  Returns the instance of the manager
       *
       *  @return GuestbookManager The guestbook manager
       *
       *  @author Christian Achatz
       *  @version
       *  Version 0.1, 05.05.2007<br />
       */
      protected function &getGuestbookManager() {
         return $this->getAndInitServiceObject('modules::guestbook::biz', 'GuestbookManager', $this->getGuestbookId());
      }

      /**
       * @protected
       *
       * Returns the id of the current guestbook. The id is defined within the main template
       * of the guestbook module. Thus, the attribute can be retrieved by requesting the parent
       * object's attributes list.
       *
       * @return int The id of the current guestbook.
       */
      protected function getGuestbookId() {
         $parentDocument = &$this->__Document->getParentObject();
         return $parentDocument->getAttribute('guestbookid');
      }

      /**
       * @protected
       *
       * Returns the namespace of the current guestbook. It consists of the module's namespace
       * and the id of the current guestbook. This is done, because two guestbooks of the same
       * code base could be hijacked by knowing one of the instance's credentials.
       *
       * @return string The guestbook's namespace.
       */
      protected function getGuestbookNamespace() {
         return 'modules::guestbook::' . $this->getGuestbookId();
      }

   }
?>