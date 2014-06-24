<?php
namespace APF\modules\usermanagement\biz\model;

use APF\modules\usermanagement\biz\provider\UserFieldEncryptionProvider;

/**
 * @package APF\modules\usermanagement\biz\model
 * @class UmgtUser
 *
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
