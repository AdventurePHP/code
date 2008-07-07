<?php
   import('modules::newspager::biz','newspagerManager');


   /**
   *  @package modules::newspager::pres
   *  @class newspager_v1_controller
   *
   *  Document controller for the newspager module.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.20.2008<br />
   */
   class newspager_v1_controller extends baseController
   {

      function newspager_v1_controller(){
      }


      /**
      *  @public
      *
      *  Implements the abstract transformation function of the baseController class.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.20.2008<br />
      *  Version 0.2, 05.01.2008 (language is now published to the java script code)<br />
      */
      function transformContent(){

         // get manager
         $nM = &$this->__getServiceObject('modules::newspager::data','newspagerManager');

         // load default news page
         $N = $nM->getNewsByPage();

         // fill place holders
         $this->setPlaceHolder('NewsLanguage',$this->__Language);
         $this->setPlaceHolder('NewsCount',$N->get('NewsCount'));
         $this->setPlaceHolder('Headline',$N->get('Headline'));
         $this->setPlaceHolder('Subheadline',$N->get('Subheadline'));
         $this->setPlaceHolder('Content',$N->get('Content'));

       // end function
      }

    // end class
   }
?>