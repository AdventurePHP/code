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

use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;

class ProxyTypeDeleteController extends UmgtBaseController {

   public function transformContent() {

      $formNo = &$this->getForm('ProxyTypeDelNo');
      $formYes = &$this->getForm('ProxyTypeDelYes');

      $uM = &$this->getManager();
      $proxyTypeId = $this->getRequest()->getParameter('proxytypeid');
      $proxyType = $uM->loadVisibilityDefinitionTypeById($proxyTypeId);

      if ($formNo->isSent() || $formYes->isSent()) {
         if ($formYes->isSent()) {
            $uM->deleteVisibilityDefinitionType($proxyType);
         }
         $this->getResponse()->forward($this->generateLink(['mainview' => 'proxy', 'proxyview' => 'typelist', 'proxytypeid' => null]));
      }

      // fill the intro text
      $this->getLabel('intro-text')->setPlaceHolder('display-name', $proxyType->getAppObjectName());

      $formNo->transformOnPlace();
      $formYes->transformOnPlace();

   }

}
