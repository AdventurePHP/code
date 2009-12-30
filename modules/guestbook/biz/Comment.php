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
   *  @class Comment
   *
   *  Comment domain object.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 12.04.2007<br />
   */
   class Comment extends coreObject
   {

      /**
      *  @private
      *  Id of the comment.
      */
      protected $__ID = null;


      /**
      *  @private
      *  Title of the comment.
      */
      protected $__Title;


      /**
      *  @private
      *  Date of the comment.
      */
      protected $__Date;


      /**
      *  @private
      *  Text of the comment.
      */
      protected $__Text;


      /**
      *  @private
      *  Time of the comment.
      */
      protected $__Time;


      public function Comment(){
      }

    // end class
   }
?>