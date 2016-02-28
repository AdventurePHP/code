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
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

/**
 * Deletes a visibility definition.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 05.06.2010<br />
 */
class ProxyDeleteController extends UmgtBaseController {

   public function transformContent() {

      $uM = $this->getManager();

      $proxyId = $this->getRequest()->getParameter('proxyid');

      $proxy = new UmgtVisibilityDefinition();
      $proxy->setObjectId($proxyId);

      $formYes = $this->getForm('DelYes');
      $formNo = $this->getForm('DelNo');

      if ($formYes->isSent()) {
         $uM->deleteVisibilityDefinition($proxy);
      } elseif ($formNo->isSent()) {
      } else {
         $proxyType = $uM->loadVisibilityDefinitionType($proxy);
         $this->getLabel('intro-text')
               ->setPlaceHolder('proxy-type', $proxyType->getAppObjectName())
               ->setPlaceHolder('proxy-id', $proxyId);

         $formYes->transformOnPlace();
         $formNo->transformOnPlace();

         return;
      }

      $this->getResponse()->forward($this->generateLink(
            [
                  'mainview'  => 'proxy',
                  'proxyview' => null
            ]
      )
      );

   }

}
