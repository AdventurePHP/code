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
namespace APF\modules\usermanagement\biz\model;

use APF\modules\usermanagement\biz\provider\UserFieldEncryptionProvider;
use DateTime;

/**
 * This class represents the "APF\modules\usermanagement\biz\model\UmgtUser" domain object.
 * <p/>
 * Please use this class to add your own functionality.
 */
class UmgtUser extends UmgtUserBase {

   /**
    * Call the parent's constructor because the object name needs to be set.
    * <p/>
    * To create an instance of this object, just call
    * <code>
    * use APF\modules\usermanagement\biz\model\UmgtUser;
    * $object = new UmgtUser();
    * </code>
    *
    * @param string $objectName The internal object name of the domain object.
    */
   public function __construct($objectName = null) {
      parent::__construct();
   }

   /**
    * Let's you add the current user to the applied group. This method does not include
    * persistence handling but is for convenience!
    *
    * @param UmgtGroup $group The group to add the user to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.12.2011
    */
   public function addGroup(UmgtGroup $group) {
      $this->addRelatedObject('Group2User', $group);
   }

   /**
    * Let's you assign the current user to the applied role. This method does not include
    * persistence handling but is for convenience!
    *
    * @param UmgtRole $role The role to assign the user to.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.12.2011
    */
   public function addRole(UmgtRole $role) {
      $this->addRelatedObject('Role2User', $role);
   }

   /**
    * @param DateTime $date The user's birth date.
    *
    * @return UmgtUser The domain object for further usage.
    */
   public function setBirthday(DateTime $date) {
      return parent::setDateOfBirth($date->format('Y-m-d'));
   }

   /**
    * @return DateTime $date The user's birth date.
    */
   public function getBirthday() {
      return DateTime::createFromFormat('Y-m-d', parent::getDateOfBirth());
   }

   public function beforeSave() {
      UserFieldEncryptionProvider::encryptProperties($this);
   }

   public function afterSave() {
      UserFieldEncryptionProvider::decryptProperties($this);
   }

   public function afterLoad() {
      UserFieldEncryptionProvider::decryptProperties($this);
   }

}
