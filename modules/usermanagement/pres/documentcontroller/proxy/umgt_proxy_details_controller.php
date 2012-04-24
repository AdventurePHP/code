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

class umgt_proxy_details_controller extends umgt_base_controller {

   public function transformContent() {

      $uM = &$this->getManager();
      $proxyId = RequestHandler::getValue('proxyid');
      $proxy = $uM->loadVisibilityDefinitionById($proxyId);
      $type = $uM->loadVisibilityDefinitionType($proxy);

      // display the proxy type's name
      $this->getLabel('intro-text')
            ->setPlaceHolder('app-object-id', $proxy->getAppObjectId())
            ->setPlaceHolder('app-proxy-type', $type->getAppObjectName());

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

      $config = $this->getConfiguration('modules::usermanagement::pres', 'labels.ini');
      $section = $config->getSection($this->getLanguage());

      foreach ($sortedList as $item) {

         /* @var $item UmgtUser|UmgtGroup */
         $template->setPlaceHolder('item', $item->getDisplayName());

         $icon = &$this->getIcon($template);
         if ($item instanceof UmgtUser) {
            $icon->setAttribute('filename', 'user.png');
            $icon->setAttribute('title', $section->getValue('frontend.proxy.details.user-img.label'));
         } else {
            $icon->setAttribute('filename', 'group.png');
            $icon->setAttribute('title', $section->getValue('frontend.proxy.details.group-img.label'));
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

      // display special visibility definitions
      $tmpl = &$this->getTemplate('access-perms');

      $read = $tmpl->getChildNode('id', 'read', 'umgt_taglib_media');
      $proxy->getReadPermission() == '1'
            ? $read->setAttribute('filename', 'yes.png')
            : $read->setAttribute('filename', 'no.png');


      $write = $tmpl->getChildNode('id', 'write', 'umgt_taglib_media');
      $proxy->getWritePermission() == '1'
            ? $write->setAttribute('filename', 'yes.png')
            : $write->setAttribute('filename', 'no.png');

      $relation = $tmpl->getChildNode('id', 'relation', 'umgt_taglib_media');
      $proxy->getLinkPermission() == '1'
            ? $relation->setAttribute('filename', 'yes.png')
            : $relation->setAttribute('filename', 'no.png');

      $delete = $tmpl->getChildNode('id', 'delete', 'umgt_taglib_media');
      $proxy->getDeletePermission() == '1'
            ? $delete->setAttribute('filename', 'yes.png')
            : $delete->setAttribute('filename', 'no.png');

      $tmpl->transformOnPlace();
   }

}

?>