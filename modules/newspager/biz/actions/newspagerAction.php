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
      *  Implements the abstract run() method.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.02.2007<br />
      *  Version 0.2, 05.02.2008 (language is now directly taken from the AJAX request)<br />
      */
      function run(){

         // get desired page number and language
         $Page = $this->__Input->getAttribute('page');
         $Language = $this->__Input->getAttribute('lang');

         // get manager
         $nM = &$this->__getServiceObject('modules::newspager::data','newspagerManager');

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