<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::link','FrontcontrollerLinkHandler');

   /**
    * @package tools::media::taglib
    * @class ui_mediastream
    *
    * Implements the base class for the <*:mediastream /> tag implementations. Generates a
    * generic front controller image source out of a namespace and file name.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 29.10.2008<br />
    */
   class ui_mediastream extends Document {

      /**
       * @protected
       * @var boolean Indicates, of the tag should remain quite in case of attribute errors.
       */
      protected $__NoOutput = false;

      public function ui_mediastream(){
      }

      /**
       * @public
       *
       * Sets up and checks the tag attributes.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.10.2008<br />
       * Version 0.2, 01.11.2008<br />
       */
      public function onParseTime(){

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
            $this->setAttribute('extension',substr($filename,$dot + 1));
            $this->setAttribute('filebody',substr($filename,0,$dot));
          // end if
         }

       // end function
      }

      /**
       * @public
       *
       * Generates the tag's output.
       *
       * @return string The desired media url
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 29.10.2008<br />
       * Version 0.2, 01.11.2008<br />
       * Version 0.3, 05.11.2008 (Changed action base url generation)<br />
       * Version 0.4, 07.11.2008 (Refactored the url generation due to some addressing bugs)<br />
       * Version 0.5, 20.06.2010 (Adapted parameter order to support old rewrite rules that do file extension matching for routing exceptions)<br />
       */
      public function transform(){

         if($this->__NoOutput === false){

            // get infos from the registry
            $urlrewrite = Registry::retrieve('apf::core','URLRewriting');
            $actionurl = Registry::retrieve('apf::core','CurrentRequestURL');

            // build action statement
            $this->setAttribute('namespace',str_replace('::','_',$this->getAttribute('namespace')));
            if($urlrewrite === true){
               $actionParam = array(
                  'tools_media-action/streamMedia' => 'namespace/'
                  .$this->getAttribute('namespace').'/extension/'.$this->getAttribute('extension')
                  .'/filebody/'.$this->getAttribute('filebody')
                  
               );
             // end if
            }
            else{
               $actionParam = array(
                  'tools_media-action:streamMedia' => 'namespace:'
                  .$this->getAttribute('namespace').'|extension:'.$this->getAttribute('extension')
                  .'|filebody:'.$this->getAttribute('filebody')
                  
               );
             // end else
            }

            return FrontcontrollerLinkHandler::generateLink($actionurl,$actionParam);

          // end if
         }

       // end function
      }

    // end class
   }
?>