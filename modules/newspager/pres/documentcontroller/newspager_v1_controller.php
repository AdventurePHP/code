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
   class newspager_v1_controller extends base_controller {

      /**
      *  @public
      *
      *  Implements the abstract transformation function of the base_controller class.<br />
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 02.20.2008<br />
      *  Version 0.2, 05.01.2008 (language is now published to the java script code)<br />
      *  Version 0.3, 18.09.2008 (Introduced datadir attribute to be able to operate the module in more than one application)<br />
      */
      public function transformContent(){

         // get current data dir or trigger error
         $DataDir = $this->__Document->getAttribute('datadir');
         if($DataDir === null){
            throw new InvalidArgumentException('[newspager_v1_controller::transformContent()] Tag '
                    .'attribute "datadir" was not present in the &lt;core:importdesign /&gt; tag '
                    .'definition! Please specify a news content directory!');
            return;
          // end if
         }

         // get manager
         $nM = &$this->__getAndInitServiceObject('modules::newspager::biz','newspagerManager',$DataDir);

         // load default news page
         $N = $nM->getNewsByPage();

         // fill place holders
         $this->setPlaceHolder('NewsLanguage',$this->__Language);
         $this->setPlaceHolder('NewsCount',$N->get('NewsCount'));
         $this->setPlaceHolder('Headline',$N->get('Headline'));
         $this->setPlaceHolder('Subheadline',$N->get('Subheadline'));
         $this->setPlaceHolder('Content',$N->get('Content'));

         // set news service base url
         if(Registry::retrieve('apf::core','URLRewriting') === true){
            $this->setPlaceHolder('NewsServiceBaseURL','/~/modules_newspager_biz-action/Pager/page/');
            $this->setPlaceHolder('NewsServiceLangParam','/lang/');
            $this->setPlaceHolder('NewsServiceDataDir','/datadir/'.base64_encode($DataDir));
          // end if
         }
         else{
            $this->setPlaceHolder('NewsServiceBaseURL','./?modules_newspager_biz-action:Pager=page:');
            $this->setPlaceHolder('NewsServiceLangParam','|lang:');
            $this->setPlaceHolder('NewsServiceDataDir','|datadir:'.base64_encode($DataDir));
          // end else
         }

       // end function
      }

    // end class
   }
?>