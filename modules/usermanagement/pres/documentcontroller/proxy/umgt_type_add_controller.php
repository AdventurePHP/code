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
 * @package modules::usermanagement::pres::documentcontroller::proxy
 * @class umt_type_add_controller
 */
class umgt_type_add_controller extends umgt_base_controller {

   public function transformContent() {

      $form = &$this->getForm('add');

      if ($form->isSent() && $form->isValid()) {

         $proxyName = &$form->getFormElementByName('proxytypename');
         $proxyType = new UmgtVisibilityDefinitionType();
         $proxyType->setAppObjectName($proxyName->getAttribute('value'));
         $uM = &$this->getManager();
         try {
            $uM->saveVisibilityDefinitionType($proxyType);
            HeaderManager::forward($this->generateLink(array('mainview' => 'proxy', 'proxyview' => 'typelist')));
         } catch (DatabaseHandlerException $dhe) {
            // mark field as invalid
            // due to the fact, that we have a
            // form error, it is also displayed!
            $proxyName->markAsInvalid();
            $proxyName->setAttribute('class', 'apf-form-error');
         }

      }
      $form->transformOnPlace();

   }

}

?>