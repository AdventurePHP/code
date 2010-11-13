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

   /**
    * @package modules::guestbook::biz
    * @class Comment
    *
    * Comment domain object.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   final class Comment {

      /**
       * @var string Id of the comment.
       */
      private $id = null;

      /**
       * @var string Title of the comment.
       */
      private $title;

      /**
       * @var string Date of the comment.
       */
      private $date;

      /**
       * @var string Time of the comment.
       */
      private $time;

      /**
       * @var string Text of the comment.
       */
      private $text;

      public function getId() {
         return $this->id;
      }

      public function setId($id) {
         $this->id = $id;
      }

      public function getTitle() {
         return $this->title;
      }

      public function setTitle($title) {
         $this->title = $title;
      }

      public function getDate() {
         return $this->date;
      }

      public function setDate($date) {
         $this->date = $date;
      }

      public function getTime() {
         return $this->time;
      }

      public function setTime($time) {
         $this->time = $time;
      }

      public function getText() {
         return $this->text;
      }

      public function setText($text) {
         $this->text = $text;
      }

   }
?>