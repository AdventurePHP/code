<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('modules::genericormapper::data','GenericORMapperFactory');
   import('modules::guestbook2009::biz','User');
   
   /**
    * @namespace modules::guestbook2009::data
    * @class GuestbookMapper
    *
    * Implements the data mapper for the guestbook module. Translates the single-language
    * domain model into a multi-language database layout.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 03.05.2009<br />
    */
   class GuestbookMapper extends coreObject {

      /**
       * @public
       *
       * Loads the list of Entries
       *
       * @return Entry[] The desired entries for the current guestbook.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 06.05.2009<br />
       */
      public function loadEntryList(){

         $sortCrit = new GenericCriterionObject();
         $sortCrit->addOrderIndicator('CreationTimestamp','DESC');
         $gb = $this->__getCurrentGuestbook();
         return $this->__mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry',$sortCrit)
         );
         
       // end function
      }

      /**
       * @public
       * 
       * Returns the list of all entries for filling a selection field.
       *
       * @return Entry[] The desired entry list.
       */
      public function loadEntryListForSelection(){
         
         $sortCrit = new GenericCriterionObject();
         $sortCrit->addOrderIndicator('CreationTimestamp','DESC');
         $gb = $this->__getCurrentGuestbook();
         return $this->__mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry',$sortCrit),
            false
         );

       // end function
      }


      public function loadGuestbook(){
      }

      /**
       * @public
       *
       * Loads a single entry with it's editor to perform an update.
       *
       * @param int $id The id of the entry.
       * @return Entry The desired entry.
       */
      public function loadEntry($id){
         
         $crit = new GenericCriterionObject();
         $crit->addOrderIndicator('CreationTimestamp','DESC');
         $crit->addPropertyIndicator('EntryID',$id);
         $gb = $this->__getCurrentGuestbook();
         $entryList = $this->__mapGenericEntries2DomainObjects(
            $gb->loadRelatedObjects('Guestbook2Entry',$crit)
         );
         return $entryList[0];

       // end function
      }

      /**
       * @public
       *
       * Saves the new / edited guestbook entry.
       *
       * @param Entry $entry The guestbook entry to save.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.05.2009<br />
       */
      public function saveEntry($entry){
         $genericEntry = $this->__mapDomainObjects2GenericEntries($entry);
         $orm = &$this->__getGenericORMapper();
         $orm->saveObject($genericEntry);
       // end function
      }


      /**
       * @public
       * 
       * Checks the user's credentials.
       *
       * @param User $user The user to authenticate.
       * @return boolean True in case, the user is allowed to login, false otherwise.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 17.05.2009<br />
       */
      public function validateCredentials($user){

         $authCrit = new GenericCriterionObject();
         $authCrit->addPropertyIndicator('Username',$user->getUsername());
         $authCrit->addPropertyIndicator('Password',md5($user->getPassword()));
         $orm = &$this->__getGenericORMapper();
         $gbUser = $orm->loadObjectByCriterion('User',$authCrit);
         if($gbUser !== null){
            return true;
         }
         return false;

       // end function
      }


      /**
       * @private
       *
       * @param Entry $domEntry The Entry domain object.
       * @return GenericDomainObject The generic domain object representing the Entry object.
       */
      private function __mapDomainObjects2GenericEntries($domEntry){

         $lang = $this->__getCurrentLanguage();

         $domEditor = $domEntry->getEditor();
         $editor = new GenericDomainObject('User');
         $editor->setProperty('Name',$domEditor->getName());
         $editor->setProperty('Email',$domEditor->getEmail());
         $editor->setProperty('Website',$domEditor->getWebsite());
         $editorId = $domEditor->getId();
         if(!empty($editorId)){
            $editor->setProperty('UserID',$editorId);
         }

         // try to load an existing title attribute
         $title = $this->__getGenericTitleAttribute($domEntry);
         //$title = new GenericDomainObject('Attribute');
         $title->setProperty('Name','title');
         $title->setProperty('Value',$domEntry->getTitle());
         $title->addRelatedObject('Attribute2Language',$lang);

         // try to load an existing text attribute
         // (TODO: implement and use __getGenericTitleAttribute())
         $text = new GenericDomainObject('Attribute');
         $text->setProperty('Name','text');
         $text->setProperty('Value',$domEntry->getText());
         $text->addRelatedObject('Attribute2Language',$lang);

         $entry = new GenericDomainObject('Entry');
         $entry->addRelatedObject('Entry2LangDepValues',$title);
         $entry->addRelatedObject('Entry2LangDepValues',$text);
         $entry->addRelatedObject('Editor2Entry',$editor);
         $gb = $this->__getCurrentGuestbook();
         $entry->addRelatedObject('Guestbook2Entry',$gb);

         $entryId = $domEntry->getId();
         if(!empty($entryId)){
            $entry->setProperty('EntryID',$entryId);
         }

         return $entry;

       // end function
      }

      /**
       * Try to load an existing title attribute. If possible, merge the attributes to not
       * generate new objects in database. Otherwise return a new generic domain object.
       *
       * @param Entry $domEntry The entry domain object.
       */
      private function __getGenericTitleAttribute($domEntry){

         // try to load
         $entry = new GenericDomainObject('Entry');
         $entry->setProperty('EntryID',$domEntry->getId());

         $orm = &$this->__getGenericORMapper();

         $crit = new GenericCriterionObject();
         $crit->addPropertyIndicator('Name','title');
         $crit->addRelationIndicator('Attribute2Language',$this->__getCurrentLanguage());
         
         $attributes = $orm->loadRelatedObjects($entry,'Entry2LangDepValues',$crit);
         if(isset($attributes[0])){
            return $attributes[0];
         }

         return new GenericDomainObject('Attribute');

       // end function
      }
      

      private function __mapGenericEntries2DomainObjects($entries = array(),$addEditor = true){

         // return empty array, because having no entries means nothing to do!
         if(count($entries) == 0){
            return array();
          // end if
         }

         // invoke benchmarker to be able to monitor the performance
         $t = &Singleton::getInstance('benchmarkTimer');
         $t->start('__mapGenericEntries2DomainObjects()');
         
         // load the language object for the current language to enable
         // language dependent mapping!
         $orm = &$this->__getGenericORMapper();
         $langCrit = new GenericCriterionObject();
         $langCrit->addPropertyIndicator('ISOCode',$this->__Language);
         $lang = $orm->loadObjectByCriterion('Language',$langCrit); // lazy loading should be introduced!!!

         // define the criterion
         $critEntries = new GenericCriterionObject();
         $critEntries->addRelationIndicator('Attribute2Language',$lang);

         $gbEntries = array();
         foreach($entries as $current){

            // load the entry itself
            $entry = new Entry();
            $entry->setCreationTimestamp($current->getProperty('CreationTimestamp'));

            $attributes = $orm->loadRelatedObjects($current,'Entry2LangDepValues',$critEntries);
            foreach($attributes as $attribute){

               if($attribute->getProperty('Name') == 'title'){
                  $entry->setTitle($attribute->getProperty('Value'));
               }
               if($attribute->getProperty('Name') == 'text'){
                  $entry->setText($attribute->getProperty('Value'));
               }

             // end foreach
            }

            // add the editor's data
            if($addEditor === true){
               $editor = new User();
               $user = $orm->loadRelatedObjects($current,'Editor2Entry');
               $editor->setName($user[0]->getProperty('Name'));
               $editor->setEmail($user[0]->getProperty('Email'));
               $editor->setWebsite($user[0]->getProperty('Website'));
               $editor->setId($user[0]->getProperty('UserID'));
               $entry->setEditor($editor);
             // end if
            }

            $entry->setId($current->getProperty('EntryID'));
            $gbEntries[] = $entry;

          // end foreach
         }

         $t->stop('__mapGenericEntries2DomainObjects()');
         return $gbEntries;

       // end function
      }

      /**
       * @private
       *
       * Returns the desired instance of the GenericORMapper configured for this application case.
       * 
       * @return GenericORRelationMapper The or mapper instance.
       */
      private function &__getGenericORMapper(){

         $ormFact = &$this->__getServiceObject('modules::genericormapper::data','GenericORMapperFactory');
         return $ormFact->getGenericORMapper(
                                       'modules::guestbook2009::data',
                                       'guestbook2009',
                                       'guestbook2009',
                                       'NORMAL',
                                       true
         );
         
       // end function
      }

      /**
       * @private
       *
       * @return GenericDomainObject The active language's representation object.
       */
      private function __getCurrentLanguage(){

         $crit = new GenericCriterionObject();
         $crit->addPropertyIndicator('ISOCode',$this->__Language);
         $orm = &$this->__getGenericORMapper();
         return $orm->loadObjectByCriterion('Language',$crit);

       // end function
      }

      /**
       * @private
       *
       * Returns the current instance of the guestbook.
       *
       * @return GenericDomainObject The current instance of the guestbook.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 16.05.2009<br />
       */
      private function __getCurrentGuestbook(){

         $orm = &$this->__getGenericORMapper();
         $model = &$this->__getServiceObject('modules::guestbook2009::biz','GuestbookModel');
         $gb = $orm->loadObjectByID('Guestbook',$model->get('GuestbookId'));
         return $gb;

       // end function
      }

    // end class
   }
?>