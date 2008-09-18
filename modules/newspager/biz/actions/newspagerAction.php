<?php
   import('modules::newspager::biz','newspagerManager');


   /**
   *  @package modules::newspager::biz
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