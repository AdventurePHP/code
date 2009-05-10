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
       * Loads the list of Entries
       * @return Entry[] The desired entries for the current guestbook.
       */
      public function loadEntryList(){

         $orm = &$this->__getGenericORMapper();
         $gb = $orm->loadObjectByID('Guestbook',1);
         return $this->__mapGenericEntries2DomainObjects($gb->loadRelatedObjects('Guestbook2Entry'));

       // end function
      }

      public function loadGuestbook(){
         
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

         $title = new GenericDomainObject('Attribute');
         $title->setProperty('Name','title');
         $title->setProperty('Value',$domEntry->getTitle());
         $title->addRelatedObject('Attribute2Language',$lang);

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

         echo printObject($entry);

         return $entry;

       // end function
      }
      
      private function __getCurrentLanguage(){

         $crit = new GenericCriterionObject();
         $crit->addPropertyIndicator('ISOCode',$this->__Language);

         $orm = &$this->__getGenericORMapper();
         return $orm->loadObjectByCriterion('Language',$crit);

       // end function
      }

      private function __getCurrentGuestbook(){
         $gb = new GenericDomainObject('Guestbook');
         $gb->setProperty('GuestbookID',1);
         return $gb;
      }


      private function __mapGenericEntries2DomainObjects($entries = array()){

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
         foreach($entries as $entry){

            // load the
            $gbEntry = new Entry();
            $attributes = $orm->loadRelatedObjects($entry,'Entry2LangDepValues',$critEntries);

            foreach($attributes as $attribute){

               if($attribute->getProperty('Name') == 'title'){
                  $gbEntry->setTitle($attribute->getProperty('Value'));
               }
               if($attribute->getProperty('Name') == 'text'){
                  $gbEntry->setText($attribute->getProperty('Value'));
               }

             // end foreach
            }

            $gbEntries[] = $gbEntry;

          // end foreach
         }

         $t->stop('__mapGenericEntries2DomainObjects()');
         return $gbEntries;

       // end function
      }

      /**
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

    // end class
   }
?>