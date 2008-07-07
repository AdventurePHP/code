<?php
   /**
   *  @package modules::kontakt4::biz
   *  @class oFormData
   *
   *  Implementiert das Dom�nenobjekt FormData, das alle Daten des Formulars h�lt.<br />
   *  Dient als Schnittstelleobjekt zwischen pres und biz.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 03.06.2006<br />
   */
   class oFormData extends coreObject
   {
      var $__RecipientID;
      var $__SenderName;
      var $__SenderEMail;
      var $__Subject;
      var $__Text;


      function oFormData(){

         $this->__RecipientID = (string)'';
         $this->__SenderName = (string)'';
         $this->__SenderEMail = (string)'';
         $this->__Subject = (string)'';
         $this->__Text = (string)'';

       // end function
      }

    // end class
   }
?>
