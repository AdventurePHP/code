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
namespace APF\modules\pager\pres\documentcontroller;

use APF\core\pagecontroller\BaseDocumentController;
use APF\modules\pager\biz\PageItem;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Implements a document controller displaying the model information of the pager. This
 * includes:
 * <ul>
 * <li>List of pages</li>
 * <li>Previous and next button</li>
 * <li>Dynamic amount of entries per page (if activated)</li>
 * </ul>
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
class AdvancedPagerController extends BaseDocumentController {

   public function transformContent() {

      // fill document attributes to local variable
      $document = $this->getDocument();

      $pageUrlParameterName = $document->getAttribute('PageUrlParameterName');
      $countUrlParameterName = $document->getAttribute('CountUrlParameterName');
      $entriesPerPage = $document->getAttribute('EntriesPerPage');
      $isDynamicPageSizeActivated = $document->getAttribute('DynamicPageSizeActivated');

      $anchorName = $document->getAttribute('AnchorName');

      /* @var $pages PageItem[] */
      $pages = $document->getAttribute('Pages');

      // do not display the pager in case we have no pages
      if (count($pages) == 0) {
         return;
      }

      $content = $this->getTemplate('content');

      $pageCount = (int) 0;
      $currentPage = (int) 0;

      $buffer = '';

      for ($i = 0; $i < count($pages); $i++) {

         if ($pages[$i]->isSelected() == true) {
            $template = $this->getTemplate('Page_Selected_' . $this->getLanguage());
            $currentPage = $pages[$i]->getPage();
         } else {
            $template = $this->getTemplate('Page_' . $this->getLanguage());
         }

         if (isset($anchorName)) {
            $template->setPlaceHolder('Link', $pages[$i]->getLink() . '#' . $anchorName);
         } else {
            $template->setPlaceHolder('Link', $pages[$i]->getLink());
         }
         $template->setPlaceHolder('Page', $pages[$i]->getPage());

         $buffer .= $template->transformTemplate();

         $pageCount = $pages[$i]->getPageCount();
      }
      $content->setPlaceHolder('Content', $buffer);

      // display previous page link
      if ($currentPage > 1) {

         $page = $currentPage - 1;
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([$pageUrlParameterName => $page]));
         $prevActive = $this->getTemplate('PreviousPage_Active_' . $this->getLanguage());
         if (isset($anchorName)) {
            $prevActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $prevActive->setPlaceHolder('Link', $link);
         }
         $content->setPlaceHolder('PreviousPage', $prevActive->transformTemplate());
      } else {
         $prevInactive = $this->getTemplate('PreviousPage_Inactive');
         $content->setPlaceHolder('PreviousPage', $prevInactive->transformTemplate());
      }

      // display first page link
      if ($currentPage > 2) {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([$pageUrlParameterName => '1']));
         $firstActive = $this->getTemplate('FirstPage_Active_' . $this->getLanguage());
         if (isset($anchorName)) {
            $firstActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $firstActive->setPlaceHolder('Link', $link);
         }
         $content->setPlaceHolder('FirstPage', $firstActive->transformTemplate());
      }

      // display next page link
      if ($currentPage < $pageCount) {

         $page = $currentPage + 1;
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([$pageUrlParameterName => $page]));
         $nextActive = $this->getTemplate('NextPage_Active_' . $this->getLanguage());

         if (isset($anchorName)) {
            $nextActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $nextActive->setPlaceHolder('Link', $link);
         }

         $content->setPlaceHolder('NextPage', $nextActive->transformTemplate());
      } else {
         $nextInactive = $this->getTemplate('NextPage_Inactive');
         $content->setPlaceHolder('NextPage', $nextInactive->transformTemplate());
      }

      // display last page link
      if ($currentPage < $pageCount - 1) {
         $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([$pageUrlParameterName => $pageCount]));
         $lastActive = $this->getTemplate('LastPage_Active_' . $this->getLanguage());
         if (isset($anchorName)) {
            $lastActive->setPlaceHolder('Link', $link . '#' . $anchorName);
         } else {
            $lastActive->setPlaceHolder('Link', $link);
         }
         $content->setPlaceHolder('LastPage', $lastActive->transformTemplate());
      }

      // display the dynamic page size bar
      if ($isDynamicPageSizeActivated == true) {

         $entriesPerPageConfig = [5, 10, 15, 20];
         $entriesPerPage = $this->getRequest()->getParameter($countUrlParameterName, $entriesPerPage);
         $buffer = (string) '';

         foreach ($entriesPerPageConfig as $count) {

            if ($entriesPerPage == $count) {
               $template = $this->getTemplate('EntriesPerPage_Active_' . $this->getLanguage());
            } else {
               $template = $this->getTemplate('EntriesPerPage_Inactive_' . $this->getLanguage());
            }

            $link = LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery([$pageUrlParameterName => '1', $countUrlParameterName => $count]));

            if (isset($anchorName)) {
               $template->setPlaceHolder('Link', $link . '#' . $anchorName);
            } else {
               $template->setPlaceHolder('Link', $link);
            }

            $template->setPlaceHolder('Count', $count);
            $buffer .= $template->transformTemplate();
         }

         $dynPageSize = $this->getTemplate('DynamicPageSize');
         $dynPageSize->setPlaceHolder('EntriesPerPage', $buffer);

         // display language dependent labels
         $entriesPerPageTmpl = $this->getTemplate('EntriesPerPage_' . $this->getLanguage());
         $dynPageSize->setPlaceHolder('EntriesPerPage_Display', $entriesPerPageTmpl->transformTemplate());

         $content->setPlaceHolder('DynamicPageSize', $dynPageSize->transformTemplate());
      }

      $content->transformOnPlace();

   }

}
