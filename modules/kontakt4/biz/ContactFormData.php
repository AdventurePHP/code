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
    * @package modules::kontakt4::biz
    * @class ContactFormData
    *
    * Implementiert das Domänenobjekt FormData, das alle Daten des Formulars hält.<br />
    * Dient als Schnittstelleobjekt zwischen pres und biz.<br />
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 03.06.2006<br />
    */
   class ContactFormData extends APFObject {

      private $recipientId = '';
      private $senderName = '';
      private $senderEmail = '';
      private $subject = '';
      private $message = '';

      public function ContactFormData(){
      }

      public function setRecipientId($id){
         $this->recipientId = $id;
      }
      public function setSenderName($name){
         $this->senderName = $name;
      }
      public function setSenderEmail($email){
         $this->senderEmail = $email;
      }
      public function setSubject($subject){
         $this->subject = $subject;
      }
      public function setMessage($message){
         $this->message = $message;
      }

      public function getRecipientId(){
         return $this->recipientId;
      }
      public function getSenderName(){
         return $this->senderName;
      }
      public function getSenderEmail(){
         return $this->senderEmail;
      }
      public function getSubject(){
         return $this->subject;
      }
      public function getMessage(){
         return $this->message;
      }

    // end class
   }
?>