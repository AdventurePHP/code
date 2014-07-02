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
namespace APF\extensions\postbox\biz;

use APF\modules\genericormapper\data\GenericDomainObject;
use APF\modules\genericormapper\data\GenericORMapperDataObject;

/**
 * This is the base class for "RecipientList" from the Postbox-Extension.
 * For further information visit the extension's documentation.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
abstract class AbstractRecipientList extends GenericDomainObject {

   /**
    * Loads all recipients which are associated to the list.
    *
    * @return GenericORMapperDataObject[] A list of users
    */
   public function getRecipients() {
      return $this->loadRelatedObjects('RecipientList2Recipient');
   }

   /**
    * Adds a recipient to the list.
    * Does NOT save the list!
    *
    * @param GenericORMapperDataObject $User
    *
    * @return AbstractRecipientList Returns itself (fluent-interface)
    */
   public function addRecipient(GenericORMapperDataObject &$User) {
      $this->addRelatedObject('RecipientList2Recipient', $User);

      return $this;
   }

   /**
    * Adds a list of recipients to the list.
    * Does NOT save the list.
    *
    * @param array $Users
    *
    * @return AbstractRecipientList Returns itself (fluent-interface)
    */
   public function addRecipients(array &$Users) {
      foreach ($Users as &$User) {
         $this->addRecipient($User);
      }

      return $this;
   }

   /**
    * Removes a recipient from the list
    *
    * @param GenericORMapperDataObject $User
    *
    * @return AbstractRecipientList Returns itself (fluent-interface)
    */
   public function removeRecipient(GenericORMapperDataObject &$User) {
      $this->getDataComponent()->deleteAssociation('RecipientList2Recipient', $this, $User);

      return $this;
   }

   /**
    * Saves a new or changed list.
    *
    * @param bool $saveTree Optional. Default: true. If set to false only the message will be saved, and not the relation-tree
    *
    * @return AbstractRecipientList Returns itself (fluent-interface)
    * @throws \BadFunctionCallException
    */
   public function save($saveTree = true) {
      if ($this->getDataComponent() === null) {
         throw new \BadFunctionCallException('[AbstractRecipientList::save()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }
      $this->getDataComponent()->saveObject($this, $saveTree);

      return $this;
   }

   /**
    * Deletes a list and all it's associations.
    */
   public function delete() {
      $this->deleteAssociations('User2RecipientList');
      $this->deleteAssociations('RecipientList2Recipient');
      $this->getDataComponent()->deleteObject($this);
   }
}
