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

abstract class permission_base_controller extends umgt_base_controller {

   protected static $FORM_NAME = 'add_perm';

   protected function mapSelectedOptions2DomainObjects($elementName, $objectName) {

      $form = &$this->getForm(self::$FORM_NAME);

      /* @var $control form_taglib_multiselect */
      $control = $form->getFormElementByName($elementName);
      $selectedOptions = $control->getSelectedOptions();

      $objects = array();
      foreach ($selectedOptions as $selectedUser) {
         $object = new $objectName;
         /* @var $object UmgtUser|UmgtGroup */
         $object->setObjectId($selectedUser->getAttribute('value'));
         $objects[] = $object;
      }
      return $objects;

   }

}
