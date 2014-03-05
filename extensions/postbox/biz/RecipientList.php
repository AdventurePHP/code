<?php
namespace APF\extensions\postbox\biz;

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

//<*RecipientListBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for RecipientList. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "RecipientList" which will extend this base-class.
 */
use APF\extensions\postbox\biz\AbstractRecipientList;
class RecipientListBase extends AbstractRecipientList {

   public function __construct($objectName = null) {
      parent::__construct('RecipientList');
   }

   public function getName() {
      return $this->getProperty('Name');
   }

   public function setName($value) {
      $this->setProperty('Name', $value);
      return $this;
   }

}

// DO NOT CHANGE THIS COMMENT! <*RecipientListBase:end*>

/**
 * @package APF\extensions\postbox\biz
 * @class RecipientList
 *
 * Domain object for "RecipientList"
 * Use this class to add your own functions.
 */
class RecipientList extends RecipientListBase {
   /**
    * Call parent's function because the objectName needs to be set.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

}
