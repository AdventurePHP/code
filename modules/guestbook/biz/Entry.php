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
   *  @package modules::guestbook::biz
   *  @class Entry
   *
   *  Entry domain object.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Entry extends coreObject
   {

      /**
      *  @private
      *  Id of the entry.
      */
      protected $__ID = null;


      /**
      *  @private
      *  Name of the author.
      */
      protected $__Name;


      /**
      *  @private
      *  Email of the author.
      */
      protected $__EMail;


      /**
      *  @private
      *  City of the author.
      */
      protected $__City;


      /**
      *  @private
      *  Website of the author.
      */
      protected $__Website;


      /**
      *  @private
      *  ICQ number of the author.
      */
      protected $__ICQ;


      /**
      *  @private
      *  MSN id of the author.
      */
      protected $__MSN;


      /**
      *  @private
      *  Skype name of the author.
      */
      protected $__Skype;


      /**
      *  @private
      *  AIM number of the author.
      */
      protected $__AIM;


      /**
      *  @private
      *  Yahoo id of the author.
      */
      protected $__Yahoo;


      /**
      *  @private
      *  Entry text.
      */
      protected $__Text;


      /**
      *  @private
      *  Comments.
      */
      protected $__Comments = array();


      /**
      *  @private
      *  Date of the entry.
      */
      protected $__Date;


      /**
      *  @private
      *  Time of the entry.
      */
      protected $__Time;


      public function Entry(){
      }


      /**
      *  @public
      *
      *  Returns the list of comments.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      public function getComments(){
         return $this->__Comments;
       // end function
      }


      /**
      *  @public
      *
      *  Adds a comment to the current list.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 12.04.2007<br />
      */
      public function addComment($comment){
         $this->__Comments[] = $comment;
       // end function
      }

    // end class
   }
?>