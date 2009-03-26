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
   *  @namespace modules::guestbook::pres::documentcontroller
   *  @class guestbookBaseController
   *  @abstract
   *
   *  Abstrakter DocumentController, der Basis-Funktionen für weitere DocumentController des Moduls bereitstellt..<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   abstract class guestbookBaseController extends baseController
   {

      function guestbookBaseController(){
      }


      /**
      *  @private
      *
      *  Returns the instance of the manager
      *
      *  @return GuestbookManager $gM The guestbook manager
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 05.05.2007<br />
      */
      protected function &__getGuestbookManager(){

         $Parent = &$this->__Document->getByReference('ParentObject');
         $GuestbookID = $Parent->getAttribute('guestbookid');
         return $this->__getAndInitServiceObject('modules::guestbook::biz','guestbookManager',$GuestbookID);

       // end function
      }

    // end class
   }
?>