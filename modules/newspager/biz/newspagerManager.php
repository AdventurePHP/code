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
      private $__DataDir = null;


      function newspagerManager(){
      }


      /**
      *  @public
      *
      *  Initializes the manager.
      *
      *  @param string $initParam the news content data dir
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 18.09.2008<br />
      */
      function init($initParam){

         // cut trailing slash if necessary
         if(substr($initParam,strlen($initParam) - 1) == '/'){
            $this->__DataDir = substr($initParam,0,strlen($initParam) -1);
          // end if
         }
         else{
            $this->__DataDir = $initParam;
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
         $nM = &$this->__getAndInitServiceObject('modules::newspager::data','newspagerMapper',$this->__DataDir);
         return $nM->getNewsByPage($PageNumber);
       // end function
      }

    // end class
   }
?>