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

   import('modules::newspager::biz','newspagerManager');


   /**
   *  @namespace modules::newspager::biz
   *  @class newspagerAction
   *
   *  Front controller action implemenatation for AJAX style loading of a news page.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.20.2008<br />
   */
   class newspagerAction extends AbstractFrontcontrollerAction
   {

      function newspagerAction(){
      }


      /**
      *  @public
      *
      *  Implements the abstract run() method.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.02.2007<br />
      *  Version 0.2, 05.02.2008 (language is now directly taken from the AJAX request)<br />
      *  Version 0.3, 18.09.2008 (Added dynamic data dir behaviour)<br />
      */
      function run(){

         // get desired page number, language and data dir
         $Page = $this->__Input->getAttribute('page');
         $Language = $this->__Input->getAttribute('lang');
         $DataDir = base64_decode($this->__Input->getAttribute('datadir'));

         // get manager
         $nM = &$this->__getAndInitServiceObject('modules::newspager::data','newspagerManager',$DataDir);

         // set language
         $nM->set('Language',$Language);

         // load news object
         $N = $nM->getNewsByPage($Page);

         // create xml
         $XML = (string)'';
         $XML .= '<?xml version="1.0" encoding="utf-8" ?>';
         $XML .= '<news>';
         $XML .= '<headline>'.$N->get('Headline').'</headline>';
         $XML .= '<subheadline>'.$N->get('Subheadline').'</subheadline>';
         $XML .= '<content>'.$N->get('Content').'</content>';
         $XML .= '<newscount>'.$N->get('NewsCount').'</newscount>';
         $XML .= '</news>';

         // send xml
         header('Content-Type: text/xml; charset=iso-8859-1');
         echo $XML;

         // close application
         exit(0);

       // end function
      }

    // end class
   }
?>