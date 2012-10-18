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

/**
 * @package extensions::postbox::biz::abstractdomainobjects
 * @class AbstractMessage
 *
 * This is the base class for "Message" from the Postbox-Extension.
 * For further information visit the extension's documentation.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
abstract class AbstractMessage extends GenericDomainObject {
   /**
    * Cache for the channel's author.
    * @var mixed Can be a string or GenericORMapperDataObject
    */
   protected $Author = null;

   /**
    * Marks the message as unread for the given user.
    *
    * @param GenericORMapperDataObject $User
    * @return AbstractMessage Returns itself (fluent-interface)
    */
   public function setUnreadForUser(GenericORMapperDataObject &$User) {
      $this->addRelatedObject('User2UnreadMessage', $User);
      return $this;
   }

   /**
    * Sets the author of the message (Does NOT save the object).
    *
    * @param GenericORMapperDataObject $User
    * @return AbstractMessage Returns itself (fluent-interface)
    */
   public function setAuthor(GenericORMapperDataObject &$User) {
      $this->Author = $User;
      $this->addRelatedObject('Author2Message', $User);
      $this->setAuthorNameFallback($User->getProperty('Username'));
      return $this;
   }

   /**
    * Returns the GenericORMapperDataObject of the author, or his name-fallback if author doesn't exist anymore.
    *
    * @return mixed GenericORMapperDataObject of the author or his name-fallback as a string.
    */
   public function getAuthor() {
      if ($this->Author === null) {
         $this->Author = $this->loadRelatedObject('Author2Message');
         if ($this->Author === null) {
            $this->Author = $this->getAuthorNameFallback();
         }
      }
      return $this->Author;
   }

   /**
    * Saves the message.
    *
    * @param bool $saveTree Optional. Default: true. If set to false only the message will be saved, and not the relation-tree
    * @return AbstractMessage Returns itself (fluent-interface)
    */
   public function save($saveTree = true) {
      if ($this->getDataComponent() === null) {
         throw new BadFunctionCallException('[Message::save()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }
      $this->getDataComponent()->saveObject($this, $saveTree);
      return $this;
   }

   /**
    * Deletes a message and all it's associations.
    */
   public function delete() {
      $this->deleteAssociations('Author2Message');
      $this->deleteAssociations('User2UnreadMessage');
      $this->getDataComponent()->deleteObject($this);
   }
}

?>