<?php
namespace APF\modules\pager\pres\documentcontroller;

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
use APF\tools\link\LinkGenerator;
use APF\tools\request\RequestHandler;

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
class pager_2_v1_controller extends BaseDocumentController {

   public function transformContent() {

      // fill document attributes to local variable
      $document = $this->getDocument();

      $config = $document->getAttribute('Config');
      $anchorName = $document->getAttribute('AnchorName');

      /* @var $pages PagerPage[] */
      $pages = $document->getAttribute('Pages');

      // do not display the pager in case we have no pages
      if (count($pages) == 0) {
         $this->content = '';
         return;
      }

      $pageCount = (int)0;
      $currentPage = (int)0;

      $buffer = '';

      for ($i = 0; $i < count($pages); $i++) {

         if ($pages[$i]->isSelected() == true) {
            $template = & $this->getTemplate('Page_Selected');
            $currentPage = $pages[$i]->getPage();
         } else {
            $template = & $this->getTemplate('Page');
         }

         if (isset($anchorName)) {
            $template->setPlaceHolder('Link', $pages[$i]->getLink() . '#' . $anchorName);
         } else {
            $template->setPlaceHolder('Link', $pages[$i]->getLink());
         }
         $template->setPlaceHolder('Seite', $pages[$i]->getPage());

         $buffer .= $template->transformTemplate();

         $pageCount = $pages[$i]->getPageCount();
      }
      $this->setPlaceHolder('Inhalt', $buffer);

      // display previous page link

      if ($currentPage > 1) {

         $page = $currentPage - 1;
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array($config['ParameterPageName'] => $page)));
         $prevActive = & $this->getTemplate('VorherigeSeite_Aktiv');
         if (isset($anchorName)) {
            $prevActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $prevActive->setPlaceHolder('Link', $link);
         }
         $this->setPlaceHolder('VorherigeSeite', $prevActive->transformTemplate());
      } else {
         $prevInactive = & $this->getTemplate('VorherigeSeite_Inaktiv');
         $this->setPlaceHolder('VorherigeSeite', $prevInactive->transformTemplate());
      }

      // display next page link
      if ($currentPage < $pageCount) {

         $page = $currentPage + 1;
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array($config['ParameterPageName'] => $page)));
         $nextActive = & $this->getTemplate('NaechsteSeite_Aktiv');

         if (isset($anchorName)) {
            $nextActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $nextActive->setPlaceHolder('Link', $link);
         }

         $this->setPlaceHolder('NaechsteSeite', $nextActive->transformTemplate());
      } else {
         $nextInactive = & $this->getTemplate('NaechsteSeite_Inaktiv');
         $this->setPlaceHolder('NaechsteSeite', $nextInactive->transformTemplate());
      }

      // display the dynamic page size bar
      if ($config['DynamicPageSizeActivated'] == true) {

         $entriesPerPageConfig = array(5, 10, 15, 20);
         $entriesPerPage = RequestHandler::getValue($config['ParameterCountName'], $config['EntriesPerPage']);
         $buffer = (string)'';

         foreach ($entriesPerPageConfig as $count) {

            if ($entriesPerPage == $count) {
               $template = & $this->getTemplate('EntriesPerPage_Aktiv');
            } else {
               $template = & $this->getTemplate('EntriesPerPage_Inaktiv');
            }

            $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(array($config['ParameterPageName'] => '1', $config['ParameterCountName'] => $count)));

            if (isset($anchorName)) {
               $template->setPlaceHolder('Link', $link . '#' . $anchorName);
            } else {
               $template->setPlaceHolder('Link', $link);
            }

            $template->setPlaceHolder('Count', $count);
            $buffer .= $template->transformTemplate();
         }

         $dynPageSize = & $this->getTemplate('DynamicPageSize');
         $dynPageSize->setPlaceHolder('EntriesPerPage', $buffer);

         // display language dependent labels
         $entriesPerPageTmpl = & $this->getTemplate('EntriesPerPage_' . $this->language);
         $dynPageSize->setPlaceHolder('EntriesPerPage_Display', $entriesPerPageTmpl->transformTemplate());

         $dynPageSize->transformOnPlace();
      }

   }

}
