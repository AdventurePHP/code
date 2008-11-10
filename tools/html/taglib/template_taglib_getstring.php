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
   *  Version 0.2, 10.11.2008 (Removed the onParseTime() method, because the registerTagLibModule() function now is obsolete)<br />
   */
   class template_taglib_getstring extends ui_getstring
   {

      function template_taglib_getstring(){
      }

    // end class
   }
?>