<?php
   import('tools::html::taglib','item_taglib_placeholder');


   /**
   *  @package tools::html::taglib
   *  @class iterator_taglib_item
   *
   *  Implementiert die Repräsentation eines Items.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 01.06.2008<br />
   */
   class iterator_taglib_item extends Document
   {

      /**
      *  @public
      *
      *  Fügt die verwendeten TagLibs hinzu.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function iterator_taglib_item(){
         $this->__TagLibs[] = new TagLib('tools::html::taglib','item','placeholder');
       // end functioin
      }


      /**
      *  @public
      *
      *  Implementiert die Methode onParseTime() für die aktuelle TagLib.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.06.2008<br />
      */
      function onParseTime(){
         $this->__extractTagLibTags();
       // end function
      }

    // end class
   }
?>