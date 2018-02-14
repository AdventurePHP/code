<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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

use APF\modules\usermanagement\biz\model\UmgtGroup;
use APF\modules\usermanagement\biz\model\UmgtUser;
use APF\modules\usermanagement\pres\documentcontroller\UmgtBaseController;
use APF\tools\form\taglib\MultiSelectBoxTag;

/**
 * Base controller for visibility definition functionality.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 06.06.2010<br />
 */
abstract class UmgtPermissionBaseController extends UmgtBaseController {

   protected static $FORM_NAME = 'add_perm';

   protected function mapSelectedOptions2DomainObjects($elementName, $objectName) {

      $form = $this->getForm(self::$FORM_NAME);

      /* @var $control MultiSelectBoxTag */
      $control = $form->getFormElementByName($elementName);
      $selectedOptions = $control->getSelectedOptions();

      $objects = [];
      foreach ($selectedOptions as $selectedUser) {
         $class = 'APF\modules\usermanagement\biz\model\\' . $objectName;
         $object = new $class;
         /* @var $object UmgtUser|UmgtGroup */
         $object->setObjectId($selectedUser->getAttribute('value'));
         $objects[] = $object;
      }

      return $objects;

   }

}
