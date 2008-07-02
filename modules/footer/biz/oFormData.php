<?php
   import('modules::footer::biz','abstractObject');


   /**
   *  Package modules::footer::biz<br />
   *  Klasse oFormData<br />
   *  Implementiert das Domänenobjekt FormData, das alle Daten des Formulars hält.<br />
   *  Dient als Schnittstelleobjekt zwischen pres und biz.<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 03.06.2006<br />
   */
   class oFormData extends abstractObject
   {

      var $__SenderName;
      var $__SenderEMail;
      var $__RecipientName;
      var $__RecipientEMail;
      var $__Subject;
      var $__Text;
      var $__Page;


      function oFormData(){

         $this->__SenderName = (string)'';
         $this->__SenderEMail = (string)'';
         $this->__RecipientName = (string)'';
         $this->__RecipientEMail = (string)'';
         $this->__Subject = (string)'';
         $this->__Text = (string)'';
         $this->__Page = (string)'';

       // end function
      }

    // end class
   }
?>
