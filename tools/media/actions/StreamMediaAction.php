<?php
   /**
   *  <!--
   *  This file is part of the adventure php framework (APF) published under
   *  http://adventure-php-framework.org.
   *
   *  The APF is free software: you can redistribute it and/or modify
   *  it under the terms of the GNU Lesser General Public License as published
   *  by the Free Software Foundation, either version 3 of the License, or
   *  (at your option) any later version.
   *
   *  The APF is distributed in the hope that it will be useful,
   *  but WITHOUT ANY WARRANTY; without even the implied warranty of
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   /**
   *  @class StreamMediaAction
   *
   *  Implementation of the streamMesia action, that streams various media files (css, image, ...)
   *  to the client. This action is the "backend" for the <stream:media /> tag.
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 01.11.2008<br />
   */
   class StreamMediaAction extends AbstractFrontcontrollerAction
   {

      /**
      *  @private
      *  Mapping table for associating file extensions with content type headers.
      */
      var $__ExtensionMap = array(
                                  'png' => 'image/png',
                                  'jpeg' => 'image/jpg',
                                  'jpg' => 'image/jpg',
                                  'gif' => 'image/gif',
                                  'css' => 'text/css',
                                  'xml' => 'text/xml'
                                 );


      function StreamMediaAction(){
      }


      /**
      *  @public
      *
      *  Implements the action's run method.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.11.2008<br />
      */
      function run(){

         // read params from the input object
         $namespace = str_replace('_','/',$this->__Input->getAttribute('namespace'));
         $filebody = $this->__Input->getAttribute('filebody');
         $extenstion = $this->__Input->getAttribute('extension');
         $filename = $filebody.'.'.$extenstion;

         // map extention to known mime type
         $contentType = $this->__getContentType4Extension($extenstion);

         // send desired header
         header('Content-Type: '.$contentType);

         // send content
         @readfile(APPS__PATH.'/'.$namespace.'/'.$filename);

         // end program
         exit(0);

       // end function
      }


      /**
      *  @private
      *
      *  Returns the content type suitable for the given file extension.
      *
      *  @param string $extension the file extension
      *  @return string $contentType the desired content type or "application/octet-stream" when extension is unknown
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 01.11.2008<br />
      */
      function __getContentType4Extension($extension){

         if(isset($this->__ExtensionMap[$extension])){
            return $this->__ExtensionMap[$extension];
          // end if
         }
         else{
            return (string)'application/octet-stream';
          // end else
         }

       // end function
      }

    // end function
   }
?>