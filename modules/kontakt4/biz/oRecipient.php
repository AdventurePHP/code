<?php
   /**
   *  @package modules::kontakt4::biz
   *  @class oRecipient
   *
   *  Implementiert das Dom�nenobjekt, das die Empf�nger der Konfiguration h�lt.<br />
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 03.06.2005<br />
   */
   class oRecipient extends coreObject
   {
      var $__Name;
      var $__Adresse;


      function oRecipient(){
         $this->__Name = (string)'';
         $this->__Adresse = (string)'';
       // end function
      }

    // end class
   }
?>
