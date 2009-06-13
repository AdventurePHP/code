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

   import('modules::comments::pres::documentcontroller','commentBaseController');
   import('tools::datetime','dateTimeManager');
   import('tools::string','AdvancedBBCodeParser');
   import('tools::link','frontcontrollerLinkHandler');


   /**
    * @namespace modules::comments::pres::documentcontroller
    * @class comment_listing_v1_controller
    *
    * Implements the document controller for the 'listing.html' template.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 22.08.2007<br />
    * Version 0.2, 20.04.2008 (Added a display restriction)<br />
    * Version 0.3, 12.06.2008 (Removed the display restriction)<br />
    */
   class comment_listing_v1_controller extends commentBaseController {

      /**
      *  @public
      *
      *  Displays the paged comment list.
      *
      *  @author Christian Achatz
      *  @version
      *  Version 0.1, 22.08.2007<br />
      *  Version 0.2, 02.09.2007<br />
      *  Version 0.3, 09.03.2008 (Changed deactivation due to indexation)<br />
      *  Version 0.4, 12.06.2008 (Removed display limitation quick hack)<br />
      *  Version 0.5, 30.01.2009 (Replaced the bbCodeParser with the AdvancedBBCodeParser)<br />
      */
      function transformContent(){

         $this->__loadCategoryKey();
         $M = &$this->__getAndInitServiceObject('modules::comments::biz','commentManager',$this->__CategoryKey);

         // load the entries using the business component
         $entries = $M->loadEntries();

         $buffer = (string)'';
         $template = &$this->__getTemplate('ArticleComment');

         // init bb code parser (remove some provider, that we don't need configuration files)
         $bP = &$this->__getServiceObject('tools::string','AdvancedBBCodeParser');
         $bP->removeProvider('standard.font.color');
         $bP->removeProvider('standard.font.size');

         for($i = 0; $i < count($entries); $i++){

            $template->setPlaceHolder('Number',$i + 1);
            $template->setPlaceHolder('Name',$entries[$i]->get('Name'));
            $template->setPlaceHolder('Date',dateTimeManager::convertDate2Normal($entries[$i]->get('Date')));
            $template->setPlaceHolder('Time',$entries[$i]->get('Time'));
            $template->setPlaceHolder('Comment',$bP->parseCode($entries[$i]->get('Comment')));

            $buffer .= $template->transformTemplate();

          // end for
         }

         // display hint, if no entries are to display
         if(count($entries) < 1){
            $Template__NoEntries = &$this->__getTemplate('NoEntries');
            $buffer = $Template__NoEntries->transformTemplate();
          // end if
         }

         // display the list
         $this->setPlaceHolder('Content',$buffer);

         // display the pager
         $this->setPlaceHolder('Pager',$M->getPager('comments'));

         // get the pager url params from the business component
         // to be able to delete them from the url.
         $urlParams = $M->getURLParameter();

         // generate the add comment link
         $this->setPlaceHolder(
                'Link',
                frontcontrollerLinkHandler::generateLink(
                                          $_SERVER['REQUEST_URI'],
                                          array(
                                                $urlParams['StartName'] => '',
                                                $urlParams['CountName'] => '',
                                                'coview' => 'form'
                                          )
                )
         );

       // end function
      }

    // end class
   }
?>