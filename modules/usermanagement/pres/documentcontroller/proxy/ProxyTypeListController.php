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

/**
 * Displays the list of proxy types defined. Offers the possibility to
 * edit and delete a type definition.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.06.2010<br />
 */
class ProxyTypeListController extends UmgtBaseController {

   public function transformContent() {

      $uM = & $this->getManager();

      $buffer = (string) '';
      $template = & $this->getTemplate('Type');

      $list = $uM->loadVisibilityDefinitionTypes();
      foreach ($list as $id => $DUMMY) {
         $template->setPlaceHolder('AppObjectName', $list[$id]->getAppObjectName());

         $proxyTypeId = $list[$id]->getObjectId();
         $template->setPlaceHolder('type_edit', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'typeedit', 'proxytypeid' => $proxyTypeId)));
         $template->setPlaceHolder('type_delete', $this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'typedelete', 'proxytypeid' => $proxyTypeId)));

         $buffer .= $template->transformTemplate();
      }
      $this->setPlaceHolder('TypeList', $buffer);

   }

}
