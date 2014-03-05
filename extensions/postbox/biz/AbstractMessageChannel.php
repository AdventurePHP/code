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
use APF\core\database\MySQLxHandler;
use APF\modules\genericormapper\data\GenericCriterionObject;
use APF\modules\genericormapper\data\GenericDomainObject;
use APF\modules\genericormapper\data\GenericORMapperDataObject;

/**
 * @package APF\extensions\postbox\biz\abstractdomainobjects
 * @class AbstractMessageChannel
 *
 * This is the base class for "MessageChannel" from the Postbox-Extension.
 * For further information visit the extension's documentation.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
abstract class AbstractMessageChannel extends GenericDomainObject {

   /**
    * Returns a list of all readers from the channel.
    *
    * @return GenericORMapperDataObject[]
    */
   public function getReaders() {
      return $this->loadRelatedObjects('User2MessageChannel');
   }

   /**
    * Returns the time of the last message which was written in the channel.
    *
    * @return string The MySQL-timestamp from the last message
    */
   public function getLastMessageTime() {
      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('MessageID', 'DESC');
      $LastMessage = $this->loadRelatedObject('MessageChannel2Message', $crit);
      return $LastMessage->getProperty('CreationTimestamp');
   }

   /**
    * Returns a list of all messages in the channel, ordered by the time of creation.
    *
    * @param string $OrderDirection Optional. Default: DESC. Sets the order direction in which the messages should be returned.
    * @return Message[] A list of messages in the channel.
    */
   public function getMessages($OrderDirection = 'DESC') {
      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('CreationTimestamp', $OrderDirection);
      return $this->loadRelatedObjects('MessageChannel2Message', $crit);
   }

   /**
    * Marks the channel as unread for the given user.
    *
    * @param GenericORMapperDataObject $User
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    */
   public function setUnreadForUser(GenericORMapperDataObject &$User) {
      if (!$this->isUnreadForUser($User)) {
         $this->createAssociation('User2UnreadMessageChannel', $User);
      }
      return $this;
   }

   /**
    * Marks the channel as read for the given user.
    *
    * @param GenericORMapperDataObject $User
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    */
   public function setReadForUser(GenericORMapperDataObject &$User) {
      /* @var $DBDriver MySQLxHandler */
      $DBDriver = $this->getDataComponent()->getDbDriver();
      $DBDriver->executeStatement(
         'APF\extensions\postbox',
         'MessageChannel_setReadForUser.sql',
         array(
            'MessageChannelID' => (int)$this->getObjectId(),
            'UserID' => (int)$User->getObjectId()
         )
      );
      $this->deleteAssociation('User2UnreadMessageChannel', $User);
      return $this;
   }

   /**
    * Checks if the channel is *NOT* read by the given user.
    *
    * @param GenericORMapperDataObject $User
    * @return bool Returns true if the given user has not read at least 1 message in the channel.
    */
   public function isUnreadForUser(GenericORMapperDataObject &$User) {
      return $this->getDataComponent()->isAssociated('User2UnreadMessageChannel', $User, $this);
   }

   /**
    * Adds a list of users as new readers to the channel and marks the channel as
    * unread for this users.
    * If a user is already a reader, nothing will be done for him.
    *
    *
    * @param array $Readers A list of User-objects which should be added as reader.
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    */
   public function addReaders(array $Readers) {
      foreach ($Readers as &$Reader) {
         $this->addReader($Reader);
      }
      return $this;
   }

   /**
    * Adds a user as new reader to the channel and marks the channel as unread for him.
    * If the user is already a reader, nothing will be done.
    *
    * @param GenericORMapperDataObject $Reader
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    * @throws \BadFunctionCallException
    */
   public function addReader(GenericORMapperDataObject &$Reader) {
      if ($this->getDataComponent() === null) {
         throw new \BadFunctionCallException('[MessageChannel::addReader()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }

      // check if user is already associated to channel
      if ($this->getDataComponent()->isAssociated('User2MessageChannel', $Reader, $this)) {
         return $this;
      }

      // check if new reader is blocking any of the other readers, if so, we do not add the new reader
      $Readers = $this->getReaders();
      foreach ($Readers as $ExistingReader) {
         if ($this->getDataComponent()->isAssociated('User2BlockedUser', $Reader, $ExistingReader)) {
            return $this;
         }
      }

      $this->getDataComponent()->createAssociation('User2MessageChannel', $Reader, $this);
      $this->getDataComponent()->createAssociation('User2UnreadMessageChannel', $Reader, $this);

      // set all messages in channel as unread for the new reader
      $Messages = $this->getMessages();
      foreach ($Messages as &$Message) {
         // don't use the Message::setUnreadForUser()-method, because they wouldn't get saved...
         $this->getDataComponent()->createAssociation('User2UnreadMessage', $Reader, $Message);
      }
      return $this;
   }

   /**
    * Adds a new message to the channel and marks the channel as unread for all readers.
    *
    * @param Message $Message The Message which should be added.
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    */
   public function addMessage(Message &$Message) {
      $this->addRelatedObject('MessageChannel2Message', $Message);

      // set the new message unread for all channel readers except the author of the message
      $AuthorID = $Message->getAuthor()->getObjectId();
      $Readers = $this->getReaders();
      foreach ($Readers as $Reader) {
         if ($Reader->getObjectId() !== $AuthorID) {
            $Message->setUnreadForUser($Reader);
            $this->setUnreadForUser($Reader);
         }
      }
      $this->save();
      return $this;
   }

   /**
    * Returns the User which "opened" the channel.
    *
    * @return GenericORMapperDataObject The User which opened the channel.
    */
   public function getAuthor() {
      $crit = new GenericCriterionObject();
      $crit->addCountIndicator(1);
      $crit->addOrderIndicator('MessageID', 'ASC');
      /* @var $FirstMessage Message */
      $FirstMessage = $this->loadRelatedObject('MessageChannel2Message', $crit);

      return $FirstMessage->getAuthor();
   }

   /**
    * Saves the channel.
    *
    * @param bool $saveTree Optional. Default: true. If set to false only the channel will be saved, and not the relation-tree
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    * @throws \BadFunctionCallException
    */
   public function save($saveTree = true) {
      if ($this->getDataComponent() === null) {
         throw new \BadFunctionCallException('[MessageChannel::save()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }
      $this->getDataComponent()->saveObject($this, $saveTree);

      return $this;
   }

   /**
    * Removes the given user from the channel, if he is a reader.
    * If he is the last reader, the channel will be completely deleted.
    *
    * @param GenericORMapperDataObject $User The user which should be removed from the list of readers.
    * @return AbstractMessageChannel Returns itself (fluent-interface)
    * @throws \BadFunctionCallException
    */
   public function removeReader(GenericORMapperDataObject &$User) {
      if ($this->getDataComponent() === null) {
         throw new \BadFunctionCallException('[MessageChannel::removeReader()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }

      if ($this->getDataComponent()->isAssociated('User2MessageChannel', $User, $this)) {

         // If this user is the last reader, we delete the channel completely
         if (count($this->getReaders()) === 1) {
            $this->delete();
         } else {

            // remove unread-relations
            $this->setReadForUser($User);

            // check if channel is in a folder from the given user and remove it from there if necessary
            $crit = new GenericCriterionObject();
            $crit->addRelationIndicator('User2PostboxFolder', $User);
            /* @var $folder AbstractPostboxFolder */
            $folder = $this->loadRelatedObject('PostboxFolder2MessageChannel', $crit);
            if ($folder !== null) {
               $folder->removeChannel($this);
            }

            // finally remove the relation between user and channel
            $this->getDataComponent()->deleteAssociation('User2MessageChannel', $User, $this);

         }

      }

      return $this;
   }


   /**
    * Returns the PostboxFolder in which the channel is currently stored.
    *
    * @return PostboxFolder
    */
   public function getPostboxFolder() {
      return $this->loadRelatedObject('PostboxFolder2MessageChannel');
   }

   /**
    * Deletes the channel with all it's relations and messages.
    */
   public function delete() {
      $this->deleteAssociations('PostboxFolder2MessageChannel');
      $this->deleteAssociations('User2MessageChannel');
      $this->deleteAssociations('User2UnreadMessageChannel');

      $Messages = $this->getMessages();
      foreach ($Messages as $Message) {
         $Message->delete();
      }

      $this->getDataComponent()->deleteObject($this);
   }

}
