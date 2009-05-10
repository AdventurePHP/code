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

   import('modules::guestbook2009::biz','Entry');
   import('modules::guestbook2009::biz','Guestbook');
   import('modules::guestbook2009::biz','User');
   import('tools::http','HeaderManager');
   
   /**
    * Description of GuestbookService
    *
    * @author Administrator
    */
   final class GuestbookService extends coreObject
   {
   
      public function loadPagedEntryList(){

         $mapper = &$this->__getMapper();
         return $mapper->loadEntryList();

         /*$entry = new Entry();
         $entry->setCreationTimestamp(time());
         $entry->setText('Mein Text...');
         $entry->setTitle('title');
         return array($entry,$entry);*/
      
       // end function
      }

      public function loadGuestbook(){
      }

      /**
       * @public
       *
       * Saves the entry and forwards to the list view.
       *
       * @param Entry $entry The guestbook entry to save.
       * 
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.05.2009<br />
       */
      public function saveEntry($entry){

         $mapper = &$this->__getMapper();
         $mapper->saveEntry($entry);

         // forward to the desired view to prevent F5-bugs
         //HeaderManager::forward('./?pagepart=list');

       // end function
      }

      /**
       * @private
       *
       * Returns the configured instance of the guestbook's data component.
       *
       * @return GuestbookMapper The mapper instance.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 10.05.2009<br />
       */
      private function &__getMapper(){
         return $this->__getDIServiceObject('modules::guestbook2009::data','GuestbookMapper');
      }
      
      // Mehrfache Widerverwendbarkeit: durch unterschiedliche Datenbanken!
      // Ansonsten über mehrere DAOs, die per z.B. Registry in der index.php
      // konfigurierbar sind!

    // end class
   }
?>