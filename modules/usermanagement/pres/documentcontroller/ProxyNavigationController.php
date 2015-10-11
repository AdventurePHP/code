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
namespace APF\modules\usermanagement\pres\documentcontroller;

/**
 * Implements the documentcontroller to display the role sub menu.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.12.2008<br />
 */
class ProxyNavigationController extends UmgtBaseController {

   public function transformContent() {
      $this->setPlaceHolder('display_proxy_types', $this->generateLink(['mainview' => 'proxy', 'proxyview' => '', 'proxytypeid' => '', 'appobjectid' => null, 'proxyid' => null, 'objectid' => null, 'objecttype' => null]));
      $this->setPlaceHolder('proxy_type_add', $this->generateLink(['mainview' => 'proxy', 'proxyview' => 'typeadd', 'proxytypeid' => '', 'appobjectid' => null, 'proxyid' => null, 'objectid' => null, 'objecttype' => null]));
      $this->setPlaceHolder('proxy_add', $this->generateLink(['mainview' => 'proxy', 'proxyview' => 'proxyadd', 'proxytypeid' => '', 'appobjectid' => null, 'proxyid' => null, 'objectid' => null, 'objecttype' => null]));
      $this->setPlaceHolder('proxy_type_list', $this->generateLink(['mainview' => 'proxy', 'proxyview' => 'typelist', 'proxytypeid' => '', 'appobjectid' => null, 'proxyid' => null, 'objectid' => null, 'objecttype' => null]));
   }

}
