<?php
   import('tools::html::taglib','ui_getstring');


   /**
   *  @package tools::html::taglib
   *  @class template_taglib_getstring
   *
   *  Implementiert die TagLib für den Tag "<template:getstring />".<br />
   *
   *  @author Christian Schäfer
   *  @version
   *  Version 0.1, 21.04.2006<br />
   */
   class template_taglib_getstring extends ui_getstring
   {

      function template_taglib_getstring(){
      }


      /**
      *  @public
      *
      *  Implementierung der abstrakten Methode aus "coreObject". Bindet eine TagLib ein.<br />
      *
      *  @author Christian Schäfer
      *  @version
      *  Version 0.1, 21.04.2007<br />
      */
      function onParseTime(){

         // Beim Template registrieren, damit das Objekt transformiert wird
         $this->__ParentObject->registerTagLibModule(get_class($this));

       // end function
      }

    // end class
   }
?>