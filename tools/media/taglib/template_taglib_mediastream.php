<?php
   import('tools::media::taglib','ui_mediastream');

   /**
   *  @class template_taglib_mediastream
   *
   *  Implements the template:mediastream tag. See class ui_mediastream for more details.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 01.11.2008<br />
   *  Version 0.2, 10.11.2008 (Bugfix: tag was not transformed within a template)<br />
   *  Version 0.3, 10.11.2008 (Removed the onParseTime() method, because the registerTagLibModule() function now is obsolete)<br />
   */
   class template_taglib_mediastream extends ui_mediastream
   {

      function template_taglib_mediastream(){
      }

    // end class
   }
?>