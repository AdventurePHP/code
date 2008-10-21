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

      /**
      *  @private
      *  Defines the dir, where the news content is located.
      */
      var $__DataDir = null;


      function newspagerManager(){
      }


      /**
      *  @public
      *
      *  Initializes the manager.
      *
      *  @param string $DataDir the news content data dir
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 18.09.2008<br />
      */
      function init($DataDir){

         // cut trailing slash if necessary
         if(substr($DataDir,strlen($DataDir) - 1) == '/'){
            $this->__DataDir = substr($DataDir,0,strlen($DataDir) -1);
          // end if
         }
         else{
            $this->__DataDir = $DataDir;
          // end else
         }

       // end function
      }


      /**
      *  @public
      *
      *  Loads a news page object.
      *
      *  @param int $PageNumber; desire page number
      *  @return newspagerContent $newspagerContent; newspagerContent domain object
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.02.2007<br />
      *  Version 0.2, 18.09.2008 (DataDir is now applied to the mapper)<br />
      */
      function getNewsByPage($PageNumber = 1){

         // get mapper
         $nM = &$this->__getAndInitServiceObject('modules::newspager::data','newspagerMapper',$this->__DataDir);

         // load and return news object
         return $nM->getNewsByPage($PageNumber);

       // end function
      }

    // end class
   }
?>