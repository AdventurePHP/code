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

/**
 * @package APF\extensions\postbox\biz\abstractdomainobjects
 * @class AbstractPostboxFolder
 *
 * This is the base class for "PostboxFolder" from the Postbox-Extension.
 * For further information visit the extension's documentation.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version 0.1,  22.02.2011<br />
 */
class AbstractPostboxFolder extends GenericDomainObject {

   /**
    * Checks if this folder contains any unread message.
    *
    * @return bool Returns true if the folder contains at least 1 new message.
    */
   public function hasUnreadMessages() {
      /* @var $DBDriver MySQLxHandler */
      $DBDriver = $this->getDataComponent()->getDbDriver();
      $result = $DBDriver->executeStatement(
         'postbox',
         'PostboxFolder_hasUnreadMessages.sql',
         array(
            'PostboxFolderID' => (int)$this->getObjectId()
         )
      );
      // limit is 1 - just convert 0 or 1 to a boolean
      return (bool)$DBDriver->getNumRows($result);
   }

   /**
    * Loads a list of MessageChannels depending on the current page and how
    * many channels should be returned per page.
    *
    * @param int $Page The number of the current page.
    * @param int $ChannelsPerPage How many channels should be shown per page?
    *
    * @return MessageChannel[] A list of MessageChannels
    */
   public function getChannelsByPage($Page = 1, $ChannelsPerPage = 15) {
      $start = ((int)$Page - 1) * (int)$ChannelsPerPage;
      return $this->getChannels($start, (int)$ChannelsPerPage);
   }

   /**
    * Loads a list of MessageChannels
    *
    * @param int $start The number of the first channel which should be returned (SQL LIMIT)
    * @param int $count The number of channels which should be returned (SQL LIMIT)
    *
    * @return MessageChannel[] A list of MessageChannels.
    */
   public function getChannels($start = 0, $count = 15) {
      $crit = new GenericCriterionObject();
      $crit->addCountIndicator((int)$start, (int)$count);
      return $this->loadRelatedObjects('PostboxFolder2MessageChannel', $crit);
   }

   /**
    * Adds an existing channel to the folder. If the given channel is already
    * part of another folder, it will be automatically removed from the old folder.
    *
    * @param MessageChannel $channel The channel which should be added to the folder.
    * @return AbstractPostboxFolder Returns itself (fluent-interface)
    */
   public function addChannel(MessageChannel $channel) {
      // first remove the channel
      /* @var $oldFolder AbstractPostboxFolder */
      $oldFolder = $channel->loadRelatedObject('PostboxFolder2MessageChannel');
      if ($oldFolder !== null) {
         $oldFolder->removeChannel($channel);
      }
      $this->createAssociation('PostboxFolder2MessageChannel', $channel);

      return $this;
   }

   /**
    * Removes a channel from the folder.
    *
    * @param MessageChannel $Channel The channel which should be removed.
    * @return AbstractPostboxFolder Returns itself (fluent-interface)
    */
   public function removeChannel(MessageChannel $Channel) {
      $this->deleteAssociation('PostboxFolder2MessageChannel', $Channel);

      return $this;
   }

   /**
    * Saves the folder
    *
    * @param bool $saveTree Optional. Default: true. If set to false only the folder will be saved, and not the relation-tree
    * @return AbstractPostboxFolder Returns itself (fluent-interface)
    * @throws \BadFunctionCallException
    */
   public function save($saveTree = true) {
      if ($this->getDataComponent() === null) {
         throw new \BadFunctionCallException('[PostboxFolder::save()] DataComponent is not set, if the object was not loaded by ORM, you need to set it manually!');
      }
      $this->getDataComponent()->saveObject($this, $saveTree);

      return $this;
   }

   /**
    * Deletes a Folder.
    *
    * @param bool $moveChannelsToPostbox Optional. If set to true, all channels within the folder will be moved to the postbox instead of throwing an exception.
    * @throws \Exception
    */
   public function delete($moveChannelsToPostbox = false) {
      /* @var $channels MessageChannel[] */
      $channels = $this->loadRelatedObjects('PostboxFolder2MessageChannel');
      if (count($channels) !== 0) {
         if ($moveChannelsToPostbox === true) {
            foreach ($channels as &$channel) {
               $this->removeChannel($channel);
            }
         } else {
            throw new \Exception('[AbstractPostboxFolder::delete()] This folder can\'t be deletes as long as it still contains channels!');
         }
      }

      $this->getDataComponent()->deleteObject($this);
   }
}
