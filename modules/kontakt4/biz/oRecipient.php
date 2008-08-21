<?php
   /**
   *  @package modules::kontakt4::biz
   *  @class oRecipient
   *
   *  Implementiert das Domänenobjekt, das die Empfänger der Konfiguration hält.<br />
   *
   *  @author Christian Schäfer
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
