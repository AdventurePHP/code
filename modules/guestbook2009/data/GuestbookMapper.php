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
namespace APF\modules\guestbook2009\data;

use APF\core\benchmark\BenchmarkTimer;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;
use APF\modules\genericormapper\data\GenericCriterionObject;
use APF\modules\genericormapper\data\GenericDomainObject;
use APF\modules\genericormapper\data\GenericORRelationMapper;
use APF\modules\guestbook2009\biz\Entry;
use APF\modules\guestbook2009\biz\Guestbook;
use APF\modules\guestbook2009\biz\GuestbookModel;
use APF\modules\guestbook2009\biz\User;
use InvalidArgumentException;

/**
 * Implements the data mapper for the guestbook module. Translates the single-language
 * domain model into a multi-language database layout.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 03.05.2009<br />
 */
class GuestbookMapper extends APFObject {

   /**
    * The database connection name.
    *
    * @var string $connectionName
    */
   private $connectionName;

   /**
    * The GORM instance.
    *
    * @var GenericORRelationMapper $orm
    */
   private $orm;

   /**
    * Loads the list of Entries
    *
    * @return Entry[] The desired entries for the current guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   public function loadEntryList() {

      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('CreationTimestamp', 'DESC');

      $gb = $this->getCurrentGuestbook();

      return $this->mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry', $crit)
      );

   }

   /**
    * Returns the list of all entries for filling a selection field.
    *
    * @return Entry[] The desired entry list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   public function loadEntryListForSelection() {

      $sortCrit = new GenericCriterionObject();
      $sortCrit->addOrderIndicator('CreationTimestamp', 'DESC');
      $gb = $this->getCurrentGuestbook();

      return $this->mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry', $sortCrit),
            false
      );

   }


   /**
    * Returns the current guestbook domain object.
    *
    * @return Guestbook The desired guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function loadGuestbook() {
      $gb = $this->getCurrentGuestbook();

      return $this->mapGenericGuestbook2DomainObject($gb);
   }


   /**
    * Translates the generic guestbook object into the guestbook's
    * equivalent domain object.
    *
    * @param GenericDomainObject $guestbook The generic domain object.
    *
    * @return Entry The guestbook's domain object.
    * @throws InvalidArgumentException In case the guestbook entry cannot be loaded.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   private function mapGenericGuestbook2DomainObject(GenericDomainObject $guestbook) {

      if ($guestbook == null) {
         /* @var $model GuestbookModel */
         $model = & $this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');
         $gbId = $model->getGuestbookId();
         throw new InvalidArgumentException('[GuestbookManager::mapGenericGuestbook2DomainObject()] '
               . 'No guestbook with id "' . $gbId . '" stored in database! Please check your guestbook tag '
               . 'inclusion.', E_USER_ERROR);
      }

      $crit = new GenericCriterionObject();
      $lang = $this->getCurrentLanguage();
      $crit->addRelationIndicator('Attribute2Language', $lang);

      $attributes = $this->orm->loadRelatedObjects($guestbook, 'Guestbook2LangDepValues', $crit);

      $domGuestbook = new Guestbook();
      foreach ($attributes as $attribute) {

         if ($attribute->getProperty('Name') == 'title') {
            $domGuestbook->setTitle($attribute->getProperty('Value'));
         }
         if ($attribute->getProperty('Name') == 'description') {
            $domGuestbook->setDescription($attribute->getProperty('Value'));
         }

      }

      return $domGuestbook;
   }


   /**
    * Loads a single entry with it's editor to perform an update.
    *
    * @param int $id The id of the entry.
    *
    * @return Entry The desired entry.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   public function loadEntry($id) {
      $crit = new GenericCriterionObject();
      $crit->addOrderIndicator('CreationTimestamp', 'DESC');
      $crit->addPropertyIndicator('EntryID', $id);
      $gb = $this->getCurrentGuestbook();
      $entryList = $this->mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry', $crit)
      );

      return $entryList[0];
   }

   /**
    * Saves the new / edited guestbook entry.
    *
    * @param Entry $entry The guestbook entry to save.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2009<br />
    */
   public function saveEntry(Entry $entry) {
      $genericEntry = $this->mapDomainObject2GenericEntry($entry);
      $this->orm->saveObject($genericEntry);
   }

   /**
    * Deletes the given entry from the database.
    *
    * @param Entry $domEntry The guestbook entry to delete.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 21.05.2009<br />
    */
   public function deleteEntry(Entry $domEntry) {

      $entry = new GenericDomainObject('Entry');
      $entry->setProperty('EntryID', $domEntry->getId());

      // Delete the attributes of the entry, so that the object
      // itself can be deleted. If we don't do this, the ORM
      // will not be glad, because the entry still has child objects!
      $attributes = $this->orm->loadRelatedObjects($entry, 'Entry2LangDepValues');
      foreach ($attributes as $attribute) {
         $this->orm->deleteObject($attribute);
      }

      // delete associated users, because we don't need them anymore.
      $users = $this->orm->loadRelatedObjects($entry, 'Editor2Entry');
      foreach ($users as $user) {
         $this->orm->deleteObject($user);
      }

      // now delete entry object (associations are deleted automatically)
      $this->orm->deleteObject($entry);
   }

   /**
    * Checks the user's credentials.
    *
    * @param User $user The user to authenticate.
    *
    * @return boolean True in case, the user is allowed to login, false otherwise.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 17.05.2009<br />
    */
   public function validateCredentials(User $user) {
      $authCrit = new GenericCriterionObject();
      $authCrit->addPropertyIndicator('Username', $user->getUsername());
      $authCrit->addPropertyIndicator('Password', md5($user->getPassword()));
      $gbUser = $this->orm->loadObjectByCriterion('User', $authCrit);

      return $gbUser !== null;
   }


   /**
    * Maps a domain object to a GenericDomainObject to prepare the entry for saving it.
    *
    * @param Entry $domEntry The Entry domain object.
    *
    * @return GenericDomainObject The generic domain object representing the Entry object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   private function mapDomainObject2GenericEntry(Entry $domEntry) {

      $lang = $this->getCurrentLanguage();

      $domEditor = $domEntry->getEditor();
      $editor = new GenericDomainObject('User');
      $editor->setProperty('Name', $domEditor->getName());
      $editor->setProperty('Email', $domEditor->getEmail());
      $editor->setProperty('Website', $domEditor->getWebsite());
      $editorId = $domEditor->getId();
      if (!empty($editorId)) {
         $editor->setProperty('UserID', $editorId);
      }

      // try to load an existing title attribute to avoid new attributes
      // on updates and merge changes
      $title = $this->getGenericAttribute($domEntry, 'title');
      $title->setProperty('Name', 'title');
      $title->setProperty('Value', $domEntry->getTitle());
      $title->addRelatedObject('Attribute2Language', $lang);

      $text = $this->getGenericAttribute($domEntry, 'text');
      $text->setProperty('Name', 'text');
      $text->setProperty('Value', $domEntry->getText());
      $text->addRelatedObject('Attribute2Language', $lang);

      // setup generic domain object structure to preserve the relations
      $entry = new GenericDomainObject('Entry');
      $entry->addRelatedObject('Entry2LangDepValues', $title);
      $entry->addRelatedObject('Entry2LangDepValues', $text);
      $entry->addRelatedObject('Editor2Entry', $editor);
      $gb = $this->getCurrentGuestbook();
      $entry->addRelatedObject('Guestbook2Entry', $gb);

      $entryId = $domEntry->getId();
      if (!empty($entryId)) {
         $entry->setProperty('EntryID', $entryId);
      }

      return $entry;

   }

   /**
    * Try to load an existing  attribute. If possible, merge the attributes to not
    * generate new objects in database. Otherwise return a new generic domain object.
    *
    * @param Entry $domEntry The entry domain object.
    * @param string $name The name of the generic attribute to return.
    *
    * @return GenericDomainObject The attribute's data layer representation.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   private function getGenericAttribute(Entry $domEntry, $name) {

      // try to load
      $entry = new GenericDomainObject('Entry');
      $entry->setProperty('EntryID', $domEntry->getId());

      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('Name', $name);
      $crit->addRelationIndicator('Attribute2Language', $this->getCurrentLanguage());

      $attributes = $this->orm->loadRelatedObjects($entry, 'Entry2LangDepValues', $crit);
      if (isset($attributes[0])) {
         return $attributes[0];
      }

      return new GenericDomainObject('Attribute');
   }

   /**
    * Returns the desired instance of the GenericORMapper configured for this application case.
    *
    * @param GenericDomainObject[] A list of generic entries.
    * @param boolean $addEditor Indicates, if the editor should be mapped to the entry object.
    *
    * @return Entry[] A list of guestbook domain objects.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   private function mapGenericEntries2DomainObjects(array $entries = array(), $addEditor = true) {

      // return empty array, because having no entries means nothing to do!
      if (count($entries) == 0) {
         return array();
      }

      // invoke benchmarker to be able to monitor the performance
      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');
      $t->start('mapGenericEntries2DomainObjects()');

      // load the language object for the current language to enable
      // language dependent mapping!
      $lang = $this->getCurrentLanguage();

      // define the criterion
      $critEntries = new GenericCriterionObject();
      $critEntries->addRelationIndicator('Attribute2Language', $lang);

      $gbEntries = array();
      /* @var $current GenericDomainObject */
      foreach ($entries as $current) {

         // Check, whether there are attributes related in the current language.
         // If not, do NOT add an entry, because it will be empty!
         $attributes = $this->orm->loadRelatedObjects($current, 'Entry2LangDepValues', $critEntries);
         if (count($attributes) > 0) {

            // load the entry itself
            $entry = new Entry();
            $entry->setCreationTimestamp($current->getProperty('CreationTimestamp'));

            foreach ($attributes as $attribute) {

               if ($attribute->getProperty('Name') == 'title') {
                  $entry->setTitle($attribute->getProperty('Value'));
               }
               if ($attribute->getProperty('Name') == 'text') {
                  $entry->setText($attribute->getProperty('Value'));
               }

            }

            // add the editor's data
            if ($addEditor === true) {
               $editor = new User();
               $user = $this->orm->loadRelatedObjects($current, 'Editor2Entry');
               $editor->setName($user[0]->getProperty('Name'));
               $editor->setEmail($user[0]->getProperty('Email'));
               $editor->setWebsite($user[0]->getProperty('Website'));
               $editor->setId($user[0]->getProperty('UserID'));
               $entry->setEditor($editor);
            }

            $entry->setId($current->getProperty('EntryID'));
            $gbEntries[] = $entry;
         }

      }

      $t->stop('mapGenericEntries2DomainObjects()');

      return $gbEntries;
   }

   /**
    * Initializer method for usage with the DIServiceManager. Sets the database
    * connection name.
    *
    * @param string $connectionName The database connection to use.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   public function setConnectionName($connectionName) {
      $this->connectionName = $connectionName;
   }

   /**
    * Injects the GenericORRelationMapper into the guestbook mapper.
    *
    * @param GenericORRelationMapper $orm The generic o/r mapper instance.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 13.06.2009<br />
    */
   public function setORM(GenericORRelationMapper $orm) {
      $this->orm = $orm;
   }

   /**
    * @return GenericDomainObject The active language's representation object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 06.05.2009<br />
    */
   private function getCurrentLanguage() {
      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('ISOCode', $this->language);

      return $this->orm->loadObjectByCriterion('Language', $crit);
   }

   /**
    * Returns the current instance of the guestbook.
    *
    * @return GenericDomainObject The current instance of the guestbook.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 16.05.2009<br />
    */
   private function getCurrentGuestbook() {
      /* @var $model GuestbookModel */
      $model = & $this->getServiceObject('APF\modules\guestbook2009\biz\GuestbookModel');

      return $this->orm->loadObjectByID('Guestbook', $model->getGuestbookId());
   }

}
