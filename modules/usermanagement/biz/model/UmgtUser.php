<?php

namespace APF\modules\usermanagement\biz\model;
use APF\modules\usermanagement\biz\provider\UserFieldEncryptionProvider;

//<*UmgtUserBase:start*> DO NOT CHANGE THIS COMMENT!
/**
 * Automatically generated BaseObject for UmgtUser. !!DO NOT CHANGE THIS BASE-CLASS!!
 * CHANGES WILL BE OVERWRITTEN WHEN UPDATING!!
 * You can change class "UmgtUser" which extends this base-class.
 */
use APF\modules\genericormapper\data\GenericDomainObject;

/**
 * @package APF\modules\usermanagement\biz\model
 * @class UmgtUserBase
 *
 * This class provides the descriptive getter and setter methods for the "APF\modules\usermanagement\biz\model\UmgtUser" domain object.
 */
abstract class UmgtUserBase extends GenericDomainObject {

    public function __construct($objectName = null) {
        parent::__construct('User');
    }

    /**
     * @return string The value for property "DisplayName".
     */
    public function getDisplayName() {
        return $this->getProperty('DisplayName');
    }

    /**
     * @param string $value The value to set for property "DisplayName".
     * @return UmgtUser The domain object for further usage.
     */
    public function setDisplayName($value) {
        $this->setProperty('DisplayName', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteDisplayName() {
        $this->deleteProperty('DisplayName');
        return $this;
    }

    /**
     * @return string The value for property "FirstName".
     */
    public function getFirstName() {
        return $this->getProperty('FirstName');
    }

    /**
     * @param string $value The value to set for property "FirstName".
     * @return UmgtUser The domain object for further usage.
     */
    public function setFirstName($value) {
        $this->setProperty('FirstName', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteFirstName() {
        $this->deleteProperty('FirstName');
        return $this;
    }

    /**
     * @return string The value for property "LastName".
     */
    public function getLastName() {
        return $this->getProperty('LastName');
    }

    /**
     * @param string $value The value to set for property "LastName".
     * @return UmgtUser The domain object for further usage.
     */
    public function setLastName($value) {
        $this->setProperty('LastName', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteLastName() {
        $this->deleteProperty('LastName');
        return $this;
    }

    /**
     * @return string The value for property "StreetName".
     */
    public function getStreetName() {
        return $this->getProperty('StreetName');
    }

    /**
     * @param string $value The value to set for property "StreetName".
     * @return UmgtUser The domain object for further usage.
     */
    public function setStreetName($value) {
        $this->setProperty('StreetName', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteStreetName() {
        $this->deleteProperty('StreetName');
        return $this;
    }

    /**
     * @return string The value for property "StreetNumber".
     */
    public function getStreetNumber() {
        return $this->getProperty('StreetNumber');
    }

    /**
     * @param string $value The value to set for property "StreetNumber".
     * @return UmgtUser The domain object for further usage.
     */
    public function setStreetNumber($value) {
        $this->setProperty('StreetNumber', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteStreetNumber() {
        $this->deleteProperty('StreetNumber');
        return $this;
    }

    /**
     * @return string The value for property "ZIPCode".
     */
    public function getZIPCode() {
        return $this->getProperty('ZIPCode');
    }

    /**
     * @param string $value The value to set for property "ZIPCode".
     * @return UmgtUser The domain object for further usage.
     */
    public function setZIPCode($value) {
        $this->setProperty('ZIPCode', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteZIPCode() {
        $this->deleteProperty('ZIPCode');
        return $this;
    }

    /**
     * @return string The value for property "City".
     */
    public function getCity() {
        return $this->getProperty('City');
    }

    /**
     * @param string $value The value to set for property "City".
     * @return UmgtUser The domain object for further usage.
     */
    public function setCity($value) {
        $this->setProperty('City', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteCity() {
        $this->deleteProperty('City');
        return $this;
    }

    /**
     * @return string The value for property "EMail".
     */
    public function getEMail() {
        return $this->getProperty('EMail');
    }

    /**
     * @param string $value The value to set for property "EMail".
     * @return UmgtUser The domain object for further usage.
     */
    public function setEMail($value) {
        $this->setProperty('EMail', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteEMail() {
        $this->deleteProperty('EMail');
        return $this;
    }

    /**
     * @return string The value for property "Phone".
     */
    public function getPhone() {
        return $this->getProperty('Phone');
    }

    /**
     * @param string $value The value to set for property "Phone".
     * @return UmgtUser The domain object for further usage.
     */
    public function setPhone($value) {
        $this->setProperty('Phone', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deletePhone() {
        $this->deleteProperty('Phone');
        return $this;
    }

    /**
     * @return string The value for property "Mobile".
     */
    public function getMobile() {
        return $this->getProperty('Mobile');
    }

    /**
     * @param string $value The value to set for property "Mobile".
     * @return UmgtUser The domain object for further usage.
     */
    public function setMobile($value) {
        $this->setProperty('Mobile', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteMobile() {
        $this->deleteProperty('Mobile');
        return $this;
    }

    /**
     * @return string The value for property "Username".
     */
    public function getUsername() {
        return $this->getProperty('Username');
    }

    /**
     * @param string $value The value to set for property "Username".
     * @return UmgtUser The domain object for further usage.
     */
    public function setUsername($value) {
        $this->setProperty('Username', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteUsername() {
        $this->deleteProperty('Username');
        return $this;
    }

    /**
     * @return string The value for property "Password".
     */
    public function getPassword() {
        return $this->getProperty('Password');
    }

    /**
     * @param string $value The value to set for property "Password".
     * @return UmgtUser The domain object for further usage.
     */
    public function setPassword($value) {
        $this->setProperty('Password', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deletePassword() {
        $this->deleteProperty('Password');
        return $this;
    }

    /**
     * @return string The value for property "DynamicSalt".
     */
    public function getDynamicSalt() {
        return $this->getProperty('DynamicSalt');
    }

    /**
     * @param string $value The value to set for property "DynamicSalt".
     * @return UmgtUser The domain object for further usage.
     */
    public function setDynamicSalt($value) {
        $this->setProperty('DynamicSalt', $value);
        return $this;
    }

    /**
     * @return UmgtUser The domain object for further usage.
     */
    public function deleteDynamicSalt() {
        $this->deleteProperty('DynamicSalt');
        return $this;
    }

    /**
     * @return string The value for property "CreationTimestamp".
     */
    public function getCreationTimestamp() {
        return $this->getProperty('CreationTimestamp');
    }

    /**
     * @return string The value for property "ModificationTimestamp".
     */
    public function getModificationTimestamp() {
        return $this->getProperty('ModificationTimestamp');
    }

}

// DO NOT CHANGE THIS COMMENT! <*UmgtUserBase:end*>

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
    
    public function afterLoad()  {
        UserFieldEncryptionProvider::decryptProperties($this);
    }

}
