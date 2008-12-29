<?php
   import('tools::media::taglib','ui_mediastream');


   /**
   *  @namespace modules::usermangement::pres::taglib
   *  @class umgt_taglib_media
   *
   *  Implements the image displaying tablib. Based on the *:mediastream taglib.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 26.12.2008<br />
   */
   class umgt_taglib_media extends ui_mediastream
   {

      function umgt_taglib_media(){
      }


      /**
      *  @public
      *
      *  Overwrites the parent's onParseTime().
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function onParseTime(){
      }


      /**
      *  @public
      *
      *  Overwrites the parent's onAfterAppend().
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function onAfterAppend(){
      }


      /**
      *  @public
      *
      *  Returns the image tag, that includes the image resource (front controller action). The
      *  tag definition also contains size and border instruction.
      *
      *  @return string $imageTagDefinition the image tag
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 26.12.2008<br />
      */
      function transform(){

         // setup the desired attributes
         $this->__Attributes['namespace'] = 'modules::usermanagement::pres::icons';

         // execute the parent's onParseTime()
         parent::onParseTime();

         // generate the image tag
         $attributes = $this->__getAttributesAsString($this->__Attributes,array('namespace','filename','extension','filebody'));
         $imgsrc = parent::transform();
         return '<img src="'.$imgsrc.'" '.$attributes.' style="width: 20px; height: 20px; border-width: 0px;" />';

       // end function
      }

    // end class
   }
?>