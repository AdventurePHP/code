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
import('modules::usermanagement::pres::documentcontroller', 'umgt_base_controller');

/**
 * @package modules::usermanagement::pres::documentcontroller
 * @class umgt_list_controller
 *
 * Implements the list controller for users.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 26.12.2008<br />
 */
class umgt_list_controller extends umgt_base_controller {

   public function transformContent() {

      $t = &Singleton::getInstance('BenchmarkTimer');
      $t->start('getPagedUserList()');
      $uM = &$this->getManager();
      $userList = $uM->getPagedUserList();
      $t->stop('getPagedUserList()');

      $buffer = '';
      $template = $this->getTemplate('User');
      foreach ($userList as $user) {
         $template->setPlaceHolder('FirstName', $user->getProperty('FirstName'));
         $template->setPlaceHolder('LastName', $user->getProperty('LastName'));
         $template->setPlaceHolder('Username', $user->getProperty('Username'));
         $userid = $user->getObjectId();
         $template->setPlaceHolder('LinkUserDetails', $this->__generateLink(array('mainview' => 'user', 'userview' => 'details', 'userid' => $userid)));
         $template->setPlaceHolder('LinkUserEdit', $this->__generateLink(array('mainview' => 'user', 'userview' => 'edit', 'userid' => $userid)));
         $template->setPlaceHolder('LinkUserDelete', $this->__generateLink(array('mainview' => 'user', 'userview' => 'delete', 'userid' => $userid)));
         $template->setPlaceHolder('LinkUserAdd2Group', $this->__generateLink(array('mainview' => 'user', 'userview' => 'add2group', 'userid' => $userid)));
         $template->setPlaceHolder('LinkUserRemFromGroup', $this->__generateLink(array('mainview' => 'user', 'userview' => 'remfromgroup', 'userid' => $userid)));
         $buffer .= $template->transformTemplate();
      }

      $this->setPlaceHolder('UserList', $buffer);
   }

}
?>