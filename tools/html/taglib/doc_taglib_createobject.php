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
   *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
   *  GNU Lesser General Public License for more details.
   *
   *  You should have received a copy of the GNU Lesser General Public License
   *  along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
   *  -->
   */

   import('tools::request','RequestHandler');


   /**
   *  @package tools::html::taglib::doc
   *  @class doc_taglib_createobject
   *
   *  Implements a taglib that creates a child node by the content of a file.
   *
   *  @author Christian Sch�fer
   *  @version
   *  Version 0.1, 04.01.2006<br />
   *  Version 0.2, 29.09.2007 (Renamed to doc_taglib_createobject)<br />
   */
   class doc_taglib_createobject extends Document
   {

      /**
      *  @public
      *
      *  Calls the parent's constructor to initialize the known taglibs.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 29.09.2007 (Renamed to doc_taglib_createobject<br />
      */
      function doc_taglib_createobject(){
         parent::Document();
       // end function
      }


      /**
      *  @public
      *
      *  Reimplements the onParseTime().
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 04.01.2006<br />
      *  Version 0.2, 31.01.2009 (Replaced the variablenHandler)<br />
      */
      function onParseTime(){

         // get the attributes
         $RequestParameter = $this->getAttribute('requestparam');
         $DefaultValue = $this->getAttribute('defaultvalue');

         // get current request param
         $CurrentRequestParameter = RequestHandler::getValue($RequestParameter,$DefaultValue);

         // fill content
         $this->__Content = $this->__getContent($CurrentRequestParameter);

         // extract tags and document controller
         $this->__extractTagLibTags();
         $this->__extractDocumentController();

       // end function
      }


      /**
      *  @protected
      *
      *  Reads the content of a file using the param to indicate it's name. If the file does not
      *  exist, a file with name "404" is taken instead.
      *
      *  @author Christian Sch�fer
      *  @version
      *  Version 0.1, 30.05.2006<br />
      *  Version 0.2, 31.05.2006 (Path changed from /apps/sites  to ./frontend/content)<br />
      *  Version 0.3, 29.09.2007 (Introduced the language in the filename)<br />
      */
      protected function __getContent($pageName){

         $file = './frontend/content/c_'.$this->__Language.'_'.strtolower($pageName).'.html';

         if(!file_exists($file)){
            $file = './frontend/content/c_'.$this->__Language.'_404.html';
          // end else
         }

         return file_get_contents($file);

       // end function
      }

    // end class
   }
?>