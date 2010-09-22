<?php
   /**
    * <!--
    * This file is part of the adventure php framework (APF) published under
    * http://adventure-php-framework.org.
    *
    * The APF is free software: you can redistribute it and/or modify
    * it under the terms of the GNU Lesser General Public License as published
    * by the Free Software Foundation, either version 3 of the License, or
    * (at your option) any later version.
    *
    * The APF is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU Lesser General Public License for more details.
    *
    * You should have received a copy of the GNU Lesser General Public License
    * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
    * -->
    */

   import('tools::link', 'FrontcontrollerLinkHandler');
   import('tools::request', 'RequestHandler');

   /**
    * @package modules::schwarzesbrett::pres::documentcontroller::pager
    * @class pager_2_v1_controller
    *
    * Implements a document controller displaying the model information of the pager. This
    * includes:
    * <ul>
    * <li>List of pages</li>
    * <li>Previous and next button</li>
    * <li>Dynamic amount of entries per page (if activated)</li>
    *
    * @author Christian Achatz, Daniel Seemaier
    * @version
    * Version 0.1, 06.08.2006<br />
    * Version 0.2, 26.11.2006 (Pager elements are not displayed in case no pages are available)<br />
    * Version 0.3, 03.01.2007 (Page controller V2 ready)<br />
    * Version 0.4, 11.03.2007 (Migrated to new page controller)<br />
    * Version 0.5, 16.11.2007 (Migrated to FrontcontrollerLinkHandler)<br />
    * Version 0.6, 21.09.2010 (Migrated to page addressing instead of the start parameter)<br />
    */
   class pager_2_v1_controller extends base_controller {

      public function transformContent() {

         // do not display the pager in case we have no pages
         if (count($this->__Attributes['Pages']) == 0) {
            $this->__Content = '';
            return;
         }

         $pageCount = (int) 0;
         $currentPage = (int) 0;
         $entriesCount = (int) 0;

         $buffer = (string) '';

         for ($i = 0; $i < count($this->__Attributes['Pages']); $i++) {

            if ($this->__Attributes['Pages'][$i]->isSelected() == true) {
               $template = &$this->__getTemplate('Page_Selected');
               $currentPage = $this->__Attributes['Pages'][$i]->getPage();
            } else {
               $template = &$this->__getTemplate('Page');
            }

            if (isset($this->__Attributes['AnchorName'])) {
               $template->setPlaceHolder('Link', $this->__Attributes['Pages'][$i]->getLink() . '#' . $this->__Attributes['AnchorName']);
            } else {
               $template->setPlaceHolder('Link', $this->__Attributes['Pages'][$i]->getLink());
            }
            $template->setPlaceHolder('Seite', $this->__Attributes['Pages'][$i]->getPage());

            $buffer .= $template->transformTemplate();

            $pageCount = $this->__Attributes['Pages'][$i]->getPageCount();
            $entriesCount = $this->__Attributes['Pages'][$i]->getEntriesCount();

         }
         $this->setPlaceHolder('Inhalt', $buffer);

         // display previous page link
         if ($currentPage > 1) {

            $page = $currentPage - 1;
            $link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array($this->__Attributes['Config']['ParameterPageName'] => $page));

            $prevActive = & $this->__getTemplate('VorherigeSeite_Aktiv');
            if (isset($this->__Attributes['AnchorName'])) {
               $prevActive->setPlaceHolder('Link', $link . '#' . $this->__Attributes['AnchorName']);
            } else {
               $prevActive->setPlaceHolder('Link', $link);
            }
            $this->setPlaceHolder('VorherigeSeite', $prevActive->transformTemplate());

         } else {
            $prevInactive = & $this->__getTemplate('VorherigeSeite_Inaktiv');
            $this->setPlaceHolder('VorherigeSeite', $prevInactive->transformTemplate());
         }

         // display next page link
         if ($currentPage < $pageCount) {

            $page = $currentPage + 1;
            $link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array($this->__Attributes['Config']['ParameterPageName'] => $page));

            $nextActive = & $this->__getTemplate('NaechsteSeite_Aktiv');

            if (isset($this->__Attributes['AnchorName'])) {
               $nextActive->setPlaceHolder('Link', $link . '#' . $this->__Attributes['AnchorName']);
            } else {
               $nextActive->setPlaceHolder('Link', $link);
            }

            $this->setPlaceHolder('NaechsteSeite', $nextActive->transformTemplate());

         } else {
            $nextInactive = & $this->__getTemplate('NaechsteSeite_Inaktiv');
            $this->setPlaceHolder('NaechsteSeite', $nextInactive->transformTemplate());
         }

         // display the dynamic page size bar
         if($this->__Attributes['Config']['DynamicPageSizeActivated'] == true){

            $entriesPerPageConfig = array(5, 10, 15, 20);
            $entriesPerPage = RequestHandler::getValue($this->__Attributes['Config']['ParameterCountName'], $this->__Attributes['Config']['EntriesPerPage']);
            $buffer = (string) '';

            foreach ($entriesPerPageConfig as $count) {

               if ($entriesPerPage == $count) {
                  $template = &$this->__getTemplate('EntriesPerPage_Aktiv');
               } else {
                  $template = &$this->__getTemplate('EntriesPerPage_Inaktiv');
               }

               $link = FrontcontrollerLinkHandler::generateLink($_SERVER['REQUEST_URI'], array($this->__Attributes['Config']['ParameterPageName'] => '1', $this->__Attributes['Config']['ParameterCountName'] => $count));

               if (isset($this->__Attributes['AnchorName'])) {
                  $template->setPlaceHolder('Link', $link . '#' . $this->__Attributes['AnchorName']);
               } else {
                  $template->setPlaceHolder('Link', $link);
               }

               $template->setPlaceHolder('Count', $count);
               $buffer .= $template->transformTemplate();

            }

            $dynPageSize = &$this->__getTemplate('DynamicPageSize');
            $dynPageSize->setPlaceHolder('EntriesPerPage', $buffer);

            // display language dependent labels
            $entriesPerPageTmpl = &$this->__getTemplate('EntriesPerPage_' . $this->__Language);
            $dynPageSize->setPlaceHolder('EntriesPerPage_Display', $entriesPerPageTmpl->transformTemplate());

            $dynPageSize->transformOnPlace();

         }

       // end function
      }

    // end class
   }
?>