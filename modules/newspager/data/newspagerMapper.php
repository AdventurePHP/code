<?php
   import('modules::newspager::biz','newspagerContent');
   import('core::filesystem','filesystemHandler');


   /**
   *  @package modules::newspager::data
   *  @class newspagerMapper
   *
   *  Data layer component for loading the news page objects.<br />
   *
   *  @author Christian Achatz
   *  @version
   *  Version 0.1, 02.20.2008<br />
   */
   class newspagerMapper extends coreObject
   {

      function newspagerMapper(){
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
      function getNewsByPage($PageNumber){

         // create filesystem handler
         $fM = new filesystemHandler('./frontend/news/');

         // read all files located there
         $RawFiles = $fM->showDirContent();

         // get files, that match the current language
         $Files = array();
         $count = count($RawFiles);

         for($i = 0; $i < $count; $i++){

            if(substr_count($RawFiles[$i],'news_'.$this->__Language.'_') > 0){
               $Files[] = $RawFiles[$i];
             // end if
            }

          // end for
         }

         // throw error when page count is zero!
         $NewsCount = count($Files);

         if($NewsCount == 0){
            trigger_error('[newspagerMapper::__getFileNameByPageNumber()] No news files are given for language '.$this->__Language,E_USER_ERROR);
            exit;
          // end if
         }

         // if page number is lower then zero, correct it!
         if($PageNumber <= 0){
            $PageNumber = 1;
          // end if
         }

         // if page number is higher then max, correct it!
         if($PageNumber > $NewsCount){
            $PageNumber = $NewsCount;
          // end if
         }

         // read content of file
         $NewsArray = file('./frontend/news/'.$Files[$PageNumber - 1]);

         // initialize a new news content object
         $N = new newspagerContent();

         // fill headline
         if(isset($NewsArray[0])){
            $N->set('Headline',trim($NewsArray[0]));
          // end if
         }

         // fill subheadline
         if(isset($NewsArray[1])){
            $N->set('Subheadline',trim($NewsArray[1]));
          // end if
         }

         // fill content
         $count = count($NewsArray);
         if($count >= 3){
            $Content = (string)'';
            for($i = 2; $i < $count; $i++){
               $Content .= $NewsArray[$i];
             // end for
            }
            $N->set('Content',trim($Content));

          // end if
         }

         // set news count
         $N->set('NewsCount',$NewsCount);

         // return object
         return $N;

       // end function
      }

    // end class
   }
?>