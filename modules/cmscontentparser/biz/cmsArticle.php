<?php
   /**
   *  Package modules::contentparser::biz<br />
   *  Klasse cmsArticle<br />
   *  Implementiert das Business-Objekt 'cmsArticle'<br />
   *  <br />
   *  Christian Schäfer<br />
   *  Version 0.1, 20.05.2005<br />
   *  Version 0.2, 15.08.2006<br />
   */
   class cmsArticle
   {

      var $__Name;
      var $__Content;
      var $__Version;
      var $__Status;
      var $__ID;


      function cmsArticle(){

         $this->__Name = (string)'';
         $this->__Inhalt = (string)'';
         $this->__Version = (string)'';
         $this->__Status = (string)'';
         $this->__ID = (string)'';

       // end function
      }

      function setName($Name){
         $this->__Name = $Name;
      }
      function setContent($Content){
         $this->__Content = $Content;
      }
      function setVersion($Version){
         $this->__Version = $Version;
      }
      function setStatus($Status = 'public'){
         $this->__Status = $Status;
      }
      function setID($ID){
         $this->__ID = $ID;
      }

      function getName(){
         return $this->__Name;
      }
      function getContent(){
         return $this->__Content;
      }
      function getVersion(){
         return $this->__Version;
      }
      function getStatus(){
         return $this->__Status;
      }
      function getID(){
         return $this->__ID;
      }

    // end class
   }
?>
