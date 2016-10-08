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
namespace APF\modules\usermanagement\pres\documentcontroller\proxy;

use APF\modules\usermanagement\biz\model\UmgtVisibilityDefinition;
use APF\tools\form\taglib\MultiSelectBoxTag;

/**
 * Document controller to remove groups from an existing visibility definition.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 19.04.2010<br />
 */
class ProxyRemoveGroupsController extends UmgtPermissionBaseController {

   public function transformContent() {

      $proxyId = $this->getRequest()->getParameter('proxyid');
      $form = $this->getForm(self::$FORM_NAME);

      $proxyIdControl = $form->getFormElementByName('proxyid');
      $proxyIdControl->setAttribute('value', $proxyId);

      $uM = $this->getManager();
      $proxy = $uM->loadVisibilityDefinitionById($proxyId);
      $proxyType = $uM->loadVisibilityDefinitionType($proxy);

      $form->getLabel('intro-text')
            ->setPlaceHolder('app-object-id', $proxy->getAppObjectId())
            ->setPlaceHolder('proxy-type', $proxyType->getAppObjectName());

      $groups = $uM->loadGroupsWithVisibilityDefinition($proxy);

      if (count($groups) === 0) {
         $tmpl = $this->getTemplate('NoMoreGroups');
         $tmpl->getLabel('message-1')
               ->setPlaceHolder('app-object-id', $proxy->getAppObjectId())
               ->setPlaceHolder('object-type', $proxyType->getObjectName());
         $tmpl->getLabel('message-2')->setPlaceHolder('proxy-view-link',
               $this->generateLink(['mainview' => 'proxy', 'proxyview' => null, 'proxyid' => null]));
         $tmpl->transformOnPlace();

         return;
      }

      /* @var $groupsControl MultiSelectBoxTag */
      $groupsControl = $form->getFormElementByName('groups');
      foreach ($groups as $id => $DUMMY) {
         $groupsControl->addOption($groups[$id]->getDisplayName(), $groups[$id]->getObjectId());
      }

      if ($form->isSent() && $form->isValid()) {
         $proxy = new UmgtVisibilityDefinition();
         $proxy->setObjectId($proxyId);
         $uM->detachGroupsFromVisibilityDefinition($proxy, $this->mapSelectedOptions2DomainObjects('groups', 'UmgtGroup'));

         $this->getResponse()->forward(
               $this->generateLink(
                     [
                           'mainview'  => 'proxy',
                           'proxyview' => null,
                           'proxyid'   => null
                     ]
               )
         );

      }

      $form->transformOnPlace();

   }

}
