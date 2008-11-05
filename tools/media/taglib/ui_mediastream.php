<?php
   /**
   *  @class ui_mediastream
   *
   *  Implements the base class for the <*:mediastream /> tag implementations. Generates a
   *  generic front controller image source out of a namespace and file name.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 29.10.2008<br />
   */
   class ui_mediastream extends Document
   {

      /**
      *  @private
      *  Indicates, of the tag should remain quite in case of attribute errors.
      */
      var $__NoOutput = false;


      function ui_mediastream(){
      }


      /**
      *  @public
      *
      *  Sets up and checks the tag attributes.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.10.2008<br />
      *  Version 0.2, 01.11.2008<br />
      */
      function onParseTime(){

         // gather attributes
         if($this->getAttribute('namespace') === null){
            trigger_error('['.get_class($this).'::onParseTime()] The tag definition does not contain a "namespace" definition!');
            $this->__NoOutput = true;
          // end if
         }

         $filename = $this->getAttribute('filename');
         if($filename === null){
            trigger_error('['.get_class($this).'::onParseTime()] The tag definition does not contain a "filename" definition!');
            $this->__NoOutput = true;
          // end if
         }

         // split filename into extension and body
         if($this->__NoOutput === false){
            $dot = strrpos($filename,'.');
            $this->__Attributes['extension'] = substr($filename,$dot + 1);
            $this->__Attributes['filebody'] = substr($filename,0,$dot);
          // end if
         }

       // end function
      }


      /**
      *  @public
      *
      *  Generates the tag's output.
      *
      *  @return string $content the desired media url
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 29.10.2008<br />
      *  Version 0.2, 01.11.2008<br />
      *  Version 0.3, 05.11.2008 (Changed action base url generation)<br />
      */
      function transform(){

         if($this->__NoOutput === false){

            // get infos from the registry
            $reg = &Singleton::getInstance('Registry');
            $urlrewrite = $reg->retrieve('apf::core','URLRewriting');
            $actionurl = $Reg->retrieve('apf::core','CurrentRequestURL');

            // return desired media url
            $this->__Attributes['namespace'] = str_replace('::','_',$this->__Attributes['namespace']);
            if($urlrewrite === true){
               return $actionurl.'/~/tools_media-action/streamMedia/namespace/'.$this->__Attributes['namespace'].'/filebody/'.$this->__Attributes['filebody'].'/extension/'.$this->__Attributes['extension'];
             // end if
            }
            else{
               return $actionurl.'?tools_media-action:streamMedia=namespace:'.$this->__Attributes['namespace'].'|filebody:'.$this->__Attributes['filebody'].'|extension:'.$this->__Attributes['extension'];
             // end else
            }

          // end if
         }

       // end function
      }

    // end class
   }
?>