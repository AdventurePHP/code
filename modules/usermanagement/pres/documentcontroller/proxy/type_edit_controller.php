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

   import('tools::http','HeaderManager');
   import('tools::request','RequestHandler');
   
   /**
    * @package modules::usermanagement::pres::documentcontroller::proxy
    * @class umgt_type_edit_controller
    */
   class umgt_type_edit_controller extends umgt_base_controller {

      public function transformContent() {
         $form = &$this->__getForm('add');
         $proxyTypeId = RequestHandler::getValue('proxytypeid');
         $uM = &$this->getManager();

         if($form->isSent() && $form->isValid()) {

            $proxyName = &$form->getFormElementByName('proxytypename');
            $proxyType = new GenericDomainObject('AppProxyType');
            $proxyType->setProperty('AppProxyTypeID',$proxyTypeId);
            $proxyType->setProperty('AppObjectName',$proxyName->getAttribute('value'));
            try {
               $uM->saveProxyType($proxyType);
               HeaderManager::forward($this->__generateLink(array('mainview' => 'proxy','proxyview' => 'typelist')));
            } catch(DatabaseHandlerException $dhe) {
               // mark field as invalid
               // due to the fact, that we have a
               // form error, it is also displayed!
               $proxyName->markAsInvalid();
               $proxyName->setAttribute('class','apf-form-error');
            }

         }
         else {
            $proxyType = $uM->loadProxyTypeById($proxyTypeId);
            $name = &$form->getFormElementByName('proxytypename');
            $name->setAttribute('value',$proxyType->getProperty('AppObjectName'));
         }
         $form->transformOnPlace();

       // end function
      }

    // end class
   }
?>