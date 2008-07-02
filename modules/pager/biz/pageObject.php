<?php
   /**
   *  @package modules::pager::biz
   *  @class pageObject
   *
   *  Repräsentiert das Business-Objekt 'pageObject'.<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 06.08.2006<br />
   */
   class pageObject extends coreObject
   {

      var $__Page;
      var $__Link;
      var $__isSelected;
      var $__entriesCount;
      var $__pageCount;


      function pageObject(){

         $this->__Page = (string)'';
         $this->__Link = (string)'';
         $this->__isSelected = false;
         $this->__entriesCount = (int)0;
         $this->__pageCount = (int)0;

       // end function
      }

    // end class
   }
?>
