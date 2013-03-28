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
use APF\core\pagecontroller\APFObject;
use APF\extensions\postbox\biz\MessageChannel;
use APF\extensions\postbox\biz\Message;
use APF\modules\genericormapper\data\GenericCriterionObject;
use APF\modules\genericormapper\data\GenericORMapperDataObject;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use APF\extensions\postbox\biz\PostboxFolder;

/**
 * @package extensions::postbox::biz
 * @class Postbox
 *
 * Represents a Postbox for a defined User. Must be loaded through PostboxFactory!
 * This is the central component of the extension.
 * @example:
 * $PostboxFactory = $this->getServiceObject('extensions::postbox::biz','PostboxFactory');
 * $Postbox = $PostboxFactory->getPostbox($User);
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
class Postbox extends APFObject {

   /**
    * @var GenericORRelationMapper
    */
   protected $ORM = null;

   /**
    * @var GenericORMapperDataObject
    */
   protected $User = null;

   /**
    * Set's the data component.
    * @param GenericORRelationMapper $ORM
    * @return Postbox Returns itself.
    */
   public function setORM(GenericORRelationMapper &$ORM) {
      $this->ORM = $ORM;
      return $this;
   }

   /**
    * Set's the user who's postbox this will be.
    * @param GenericORMapperDataObject $User
    * @return Postbox Returns itself.
    */
   public function setUser(GenericORMapperDataObject &$User) {
      if ($User->getDataComponent() === null) {
         $User->setDataComponent($this->ORM);
      }
      $this->User = $User;
      return $this;
   }

   /**
    * Checks if the postbox (including folders in the postbox) contains any new message for the user.
    *
    * @return bool Returns true if the user didn't read at least 1 message in the postbox
    */
   public function hasUnreadMessages() {
      if ($this->User->loadRelatedObject('User2UnreadMessageChannel') === null) {
         return false;
      }
      return true;
   }

   /**
    * Returns a folder which is related to the postbox and has the given name.
    *
    * @param string $Name The name of the folder.
    * @return PostboxFolder
    */
   public function getPostboxFolderByName($Name) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('Name', $Name);
      return $this->User->loadRelatedObject('User2PostboxFolder', $crit);
   }

   /**
    * Returns a folder which is related to the postbox and has the given ID
    *
    * @param string $ID The folder's ID
    * @return PostboxFolder
    */
   public function getPostboxFolderByID($ID) {
      $Folder = $this->ORM->loadObjectByID('PostboxFolder', $ID);
      if ($Folder === null || !$this->ORM->isComposed('User2PostboxFolder', $Folder, $this->User)) {
         return null;
      }
      return $Folder;
   }


   /**
    * Creates a new folder with the given name in the postbox.
    *
    * @param string $Name The name of the Folder
    * @return PostboxFolder The new created folder
    * @throws \InvalidArgumentException
    */
   public function createPostboxFolder($Name) {
      if ($this->getPostboxFolderByName($Name) !== null) {
         throw new \InvalidArgumentException('[Postbox::addPostboxFolder()] This postbox already contains a folder with this name!', 1);
      }

      $Folder = new PostboxFolder();
      $Folder->setName($Name);
      $Folder->addRelatedObject('User2PostboxFolder', $this->User);
      $Folder->setDataComponent($this->ORM);
      $Folder->save();

      return $Folder;
   }

   /**
    * Returns all folders from the current postbox.
    *
    * @return PostboxFolder[] A list of PostboxFolder's
    */
   public function getPostboxFolders() {
      return $this->User->loadRelatedObjects('User2PostboxFolder');
   }

   /**
    * Creates a new channel in the postbox.
    *
    * @param string $Title The title of the new channel.
    * @param string $MessageText The text for the first message in the channel.
    * @param GenericORMapperDataObject[] $Readers An array of users which will be recipients.
    *
    * @return MessageChannel The new created channel
    * @throws \InvalidArgumentException
    */
   public function createChannel($Title, $MessageText, array $Readers) {
      $Message = new Message();
      $Message->setText($MessageText);
      $Message->setAuthor($this->User);

      $Channel = new MessageChannel();
      $Channel->setTitle($Title);
      $Channel->addRelatedObject('MessageChannel2Message', $Message);

      $Channel->addRelatedObject('User2MessageChannel', $this->User);
      // we do not use the channel's addReaders() function here, because this would only work for an existing channel!
      $RealReadersPresent = false;
      foreach ($Readers as &$Reader) {
         // author could have been added as reader as well, let's filter this possibility
         // and check if reader is blocking the current user
         if (
            $Reader->getObjectId() !== $this->User->getObjectId() &&
            !$this->isOnUsersBlacklist($Reader)
         ) {
            $RealReadersPresent = true;
            $Channel->addRelatedObject('User2MessageChannel', $Reader);
            $Message->addRelatedObject('User2UnreadMessage', $Reader);
            $Channel->addRelatedObject('User2UnreadMessageChannel', $Reader);
         }
      }

      if (!$RealReadersPresent) {
         throw new \InvalidArgumentException('[Postbox::createChannel()] There are no recipients, maybe you tried to send a message to yourself, or the recipient(s) are blocking you!');
      }

      $Channel->setDataComponent($this->ORM);
      return $Channel->save();
   }

   /**
    * Returns the number of channels of the postbox which are *NOT* in a folder.
    *
    * @return int
    */
   public function countChannelsWithoutFolder() {
      $result = $this->ORM->getDBDriver()->executeStatement('extensions::postbox', 'Postbox_countChannelsWithoutFolder.sql', array(
         'UserID' => (int)$this->User->getObjectId()
      ));
      return (int)$this->ORM->getDBDriver()->getNumRows($result);
   }

   /**
    * Loads a list of MessageChannels depending on the current page and how
    * many channels should be returned per page.
    *
    * @param int $Page The number of the current page.
    * @param int $ChannelsPerPage How many channels should be shown per page?
    * @return MessageChannel[] A list of message channels.
    */
   public function getChannelsWithoutFolderByPage($Page = 1, $ChannelsPerPage = 15) {
      $start = ((int)$Page - 1) * (int)$ChannelsPerPage;
      return $this->getChannelsWithoutFolder($start, (int)$ChannelsPerPage);
   }

   /**
    * Loads a list of MessageChannels
    * @param int $start The number of the first channel which should be returned (SQL LIMIT)
    * @param int $count The number of channels which should be returned (SQL LIMIT)
    *
    * @return MessageChannel[] A list of message channels.
    */
   public function getChannelsWithoutFolder($start = 0, $count = 15) {
      return $this->ORM->loadObjectListByStatement('MessageChannel', 'extensions::postbox', 'Postbox_getChannelsWithoutFolder.sql', array(
         'UserID' => (int)$this->User->getObjectId(),
         'Start' => (int)$start,
         'Count' => (int)$count
      ));
   }

   /**
    * Returns the channel with the given ID if it is part of this postbox.
    * If the user is not a reader of the channel NULL will be returned.
    *
    * @param int $ChannelID The ID of the channel which should be loaded.
    * @return MessageChannel
    */
   public function getChannelByID($ChannelID) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('MessageChannelID', (int)$ChannelID);

      return $this->User->loadRelatedObject('User2MessageChannel', $crit);
   }

   /**
    * Loads all RecipientLists which belong to the user.
    *
    * @return RecipientList[] An array with RecipientLists.
    */
   public function getRecipientLists() {
      return $this->User->loadRelatedObjects('User2RecipientList');
   }

   /**
    * Loads the RecipientList with the given ID, if it belongs to the user.
    *
    * @param int $ID The RecipientList's id
    * @return RecipientList
    */
   public function getRecipientListByID($ID) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('RecipientListID', (int)$ID);
      return $this->User->loadRelatedObject('User2RecipientList', $crit);
   }

   /**
    * Loads the RecipientList with the given name, which belongs to the user.
    *
    * @param string $Name The name of the list which should be loaded
    * @return RecipientList
    */
   public function getRecipientListByName($Name) {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('Name', $Name);
      return $this->User->loadRelatedObject('User2RecipientList', $crit);
   }

   /**
    * Adds the given RecipientList to the postbox and saves everything.
    *
    * @param RecipientList $RecipientList
    * @return Postbox Returns itself.
    */
   public function addRecipientList(RecipientList $RecipientList) {
      $this->User->addRelatedObject('User2RecipientList', $RecipientList);
      $this->ORM->saveObject($this->User, true);
      return $this;
   }

   /**
    * Adds the given user to the blacklist of the current user. Blocked user will not be able to
    * add the current user to any channel anymore.
    *
    * @param GenericORMapperDataObject $User The user which should be blocked.
    * @return Postbox Returns itself.
    */
   public function addUserToBlacklist(GenericORMapperDataObject &$User) {
      if (!$this->hasUserOnBlacklist($User)) {
         $this->ORM->createAssociation('User2BlockedUser', $this->User, $User);
      }
      return $this;
   }

   /**
    * Checks if the current user is blocking the given user.
    *
    * @param GenericORMapperDataObject $User The user which is maybe blocked by the current user.
    * @return bool
    */
   public function hasUserOnBlacklist(GenericORMapperDataObject &$User) {
      return $this->ORM->isAssociated('User2BlockedUser', $this->User, $User);
   }

   /**
    * Checks if the current user is being blocked by the given user.
    *
    * @param GenericORMapperDataObject $User
    * @return bool
    */
   public function isOnUsersBlacklist(GenericORMapperDataObject &$User) {
      return $this->ORM->isAssociated('User2BlockedUser', $User, $this->User);
   }

}
