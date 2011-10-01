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
import('tools::request', 'RequestHandler');

class umgt_proxy_details_controller extends umgt_base_controller {

   public function transformContent() {

      $uM = &$this->getManager();
      $proxyId = RequestHandler::getValue('proxyid');
      $proxy = $uM->loadVisibilityDefinitionById($proxyId);
      $type = $uM->loadVisibilityDefinitionType($proxy);

      // display the proxy type's name
      $this->setPlaceHolder('appobjectid', $proxy->getAppObjectId());
      $this->setPlaceHolder('appproxytype', $type->getAppObjectName());

      // load visibility permission list for the current permission
      $template = &$this->getTemplate('listitem');
      $buffer = (string)'';
      $list = $uM->loadUsersAndGroupsWithVisibilityDefinition($proxy);

      // sort list with respect to the display name of the current element
      $sortedList = array();
      foreach ($list as $offset => $DUMMY) {
         $key = $list[$offset]->getDisplayName() . '-' . $list[$offset]->getObjectId();
         $sortedList[$key] = $list[$offset];
      }
      ksort($sortedList);

      foreach ($sortedList as $item) {

         /* @var $item UmgtUser|UmgtGroup */
         $template->setPlaceHolder('item', $item->getDisplayName());

         $icon = &$this->getIcon($template);
         if ($item instanceof UmgtUser) {
            $icon->setAttribute('filename', 'cc-personal-128x128.png');
            $icon->setAttribute('alt', 'User');
            $icon->setAttribute('title', 'User');
         }
         else {
            $icon->setAttribute('filename', 'cc-yast_kuser-128x128.png');
            $icon->setAttribute('alt', 'Group');
            $icon->setAttribute('title', 'Group');
         }

         // insert links
         $template->setPlaceHolder(
            'delete_link',
            $this->generateLink(
               array(
                    'proxyview' => 'proxyrevokeaccess',
                    'proxyid' => $proxyId,
                    'objectid' => $item->getObjectId(),
                    'objecttype' => $item->getObjectName()
               )
            )
         );

         $buffer .= $template->transformTemplate();
      }

      $this->setPlaceHolder('list', $buffer);

   }

}

?>