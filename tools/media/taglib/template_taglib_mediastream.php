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
   */
   class template_taglib_mediastream extends ui_mediastream
   {

      function template_taglib_mediastream(){
      }


      /**
      *  @public
      *
      *  Registers the taglib to be transformed by the parent (template taglib).
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 10.11.2008 (Bugfix: see http://forum.adventure-php-framework.org/de/viewtopic.php?p=342#p342)<br />
      */
      function onAfterAppend(){
         $this->__ParentObject->registerTagLibModule(get_class($this));
       // end function
      }

    // end class
   }
?>