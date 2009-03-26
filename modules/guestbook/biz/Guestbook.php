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

   /**
   *  @namespace modules::guestbook::biz
   *  @class Guestbook
   *
   *  Guestbook domain object.
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Guestbook extends coreObject
   {

      /**
      *  @private
      *  Id of the guestbook.
      */
      protected $__ID = null;


      /**
      *  @private
      *  Name of the guestbook.
      */
      protected $__Name;


      /**
      *  @private
      *  Description of the guestbook.
      */
      protected $__Description;


      /**
      *  @private
      *  Entries of the giestbook.
      */
      protected $__Entries = array();


      /**
      *  @private
      *  Admin username.
      */
      protected $__Admin_Username;


      /**
      *  @private
      *  Admin password.
      */
      protected $__Admin_Password;


      public function Guestbook(){
      }


      /**
      *  @public
      *
      *  Returns the list of entries of the guestbook.
      *
      *  @return array $entries The entries list
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      public function getEntries(){
         return $this->__Entries;
       // end function
      }


      /**
      *  @public
      *
      *  Fills the entry list.
      *
      *  @param Entry[] $entries A list of entries
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      public function setEntries($entries){
         $this->__Entries = $entries;
       // end function
      }


      /**
      *  @public
      *
      *  Adds an entry to the list.
      *
      *  @param Entry $entry An entry object
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      public function addEntry($entry){
         $this->__Entries[] = $entry;
       // end function
      }

    // end class
   }
?>