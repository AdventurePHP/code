<?php
   import('modules::newspager::data','newspagerMapper');


   /**
   *  @package modules::newspager::biz
   *  @class newspagerManager
   *
   *  Business component for loading the news page objects.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.20.2008<br />
   */
   class newspagerManager extends coreObject
   {

      function newspagerManager(){
      }


      /**
      *  @public
      *
      *  Loads a news page object.<br />
      *
      *  @param int $PageNumber; desire page number
      *  @return newspagerContent $newspagerContent; newspagerContent domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.02.2007<br />
      */
      function getNewsByPage($PageNumber = 1){

         // get mapper
         $nM = &$this->__getServiceObject('modules::newspager::data','newspagerMapper');

         // load and return news object
         return $nM->getNewsByPage($PageNumber);

       // end function
      }

    // end class
   }
?>