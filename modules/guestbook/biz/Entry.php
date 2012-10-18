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
 * @class Entry
 *
 * Entry domain object.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 12.04.2007<br />
 */
final class Entry {

   /**
    * @var string Id of the entry.
    */
   private $id = null;

   /**
    * @var string Name of the author.
    */
   private $name;

   /**
    * @var string Email of the author.
    */
   private $email;

   /**
    * @var string City of the author.
    */
   private $city;

   /**
    * @var string Website of the author.
    */
   private $website;

   /**
    * @var string ICQ number of the author.
    */
   private $icq;

   /**
    * @var string MSN id of the author.
    */
   private $msn;

   /**
    * @var string Skype name of the author.
    */
   private $skype;

   /**
    * @var string AIM number of the author.
    */
   private $aim;

   /**
    * @var string Yahoo id of the author.
    */
   private $yahoo;

   /**
    * @var string Entry text.
    */
   private $text;

   /**
    * @var string Comments.
    */
   private $comments = array();

   /**
    * @var string Date of the entry.
    */
   private $date;

   /**
    * @var string Time of the entry.
    */
   private $time;

   /**
    * @public
    *
    * Returns the list of comments.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   public function getComments() {
      return $this->comments;
   }

   /**
    * @public
    *
    * Adds a comment to the current list.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 12.04.2007<br />
    */
   public function addComment(Comment $comment) {
      $this->comments[] = $comment;
   }

   public function getId() {
      return $this->id;
   }

   public function setId($id) {
      $this->id = $id;
   }

   public function getName() {
      return $this->name;
   }

   public function setName($name) {
      $this->name = $name;
   }

   public function getEmail() {
      return $this->email;
   }

   public function setEmail($email) {
      $this->email = $email;
   }

   public function getCity() {
      return $this->city;
   }

   public function setCity($city) {
      $this->city = $city;
   }

   public function getWebsite() {
      return $this->website;
   }

   public function setWebsite($website) {
      $this->website = $website;
   }

   public function getIcq() {
      return $this->icq;
   }

   public function setIcq($icq) {
      $this->icq = $icq;
   }

   public function getMsn() {
      return $this->msn;
   }

   public function setMsn($msn) {
      $this->msn = $msn;
   }

   public function getSkype() {
      return $this->skype;
   }

   public function setSkype($skype) {
      $this->skype = $skype;
   }

   public function getAim() {
      return $this->aim;
   }

   public function setAim($aim) {
      $this->aim = $aim;
   }

   public function getYahoo() {
      return $this->yahoo;
   }

   public function setYahoo($yahoo) {
      $this->yahoo = $yahoo;
   }

   public function getText() {
      return $this->text;
   }

   public function setText($text) {
      $this->text = $text;
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

}
