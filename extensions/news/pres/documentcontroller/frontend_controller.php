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
import('extensions::news::pres::documentcontroller', 'NewsBaseController');
import('tools::link', 'LinkGenerator');

/**
 * @package extensions::news::pres::documentcontroller::backend
 * @class frontend_controller
 *
 * Document controller for the frontend of the news.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 18.06.2011<br />
 */
class frontend_controller extends NewsBaseController {

   public function transformContent() {
      $appKey = $this->getAppKey();

      $newsManager = $this->getNewsManager();
      $newsList = $newsManager->getNewsByPage(null, 'DESC', $appKey);

      if (count($newsList) === 0) {
         $this->getTemplate('noentry')->transformOnPlace();
         return;
      }

      $Cfg = $this->getConfiguration('extensions::news', 'news.ini');
      $AllowHtml = ($Cfg->getSection('General')->getValue('AllowHtml') == 'TRUE') ? TRUE : FALSE;

      $List = $this->getIterator('list');

      $Data = array();

      // retrieve the charset from the registry to guarantee interoperability!
      $charset = Registry::retrieve('apf::core', 'Charset');

      foreach ($newsList as &$news) {
         $Date = new DateTime($news->getProperty('CreationTimestamp'));
         $Author = '';

         if ($news->getAuthor() !== '') {
            $authorTpl = $this->getTemplate('author');
            $authorTpl->setPlaceHolder('authorname', $news->getAuthor());
            $Author = $authorTpl->transformTemplate();
         }

         $Text = $AllowHtml ? $news->getText() : htmlentities($news->getText(), ENT_QUOTES, $charset, false);
         $Data[] = array(
            'title' => htmlentities($news->getTitle(), ENT_QUOTES, $charset, false),
            'text' => $Text,
            'date' => $Date->format('d.m.Y H:i:s'),
            'author' => $Author
         );
      }

      $List->fillDataContainer($Data);
      $List->setPlaceHolder('pager', $this->buildPager($appKey));

      $List->transformOnPlace();
   }

   /**
    * Builds the html of the pager.
    *
    * @param string $appKey
    * @return string
    */
   protected function buildPager($appKey) {
      $newsManager = $this->getNewsManager();
      $PageCount = $newsManager->getPageCount($appKey);

      // we don't need a pager for 0 or 1 pages
      if ($PageCount <= 1) {
         return '';
      }

      $Cfg = $this->getConfiguration('extensions::news', 'news.ini');
      $PageParameter = $Cfg->getSection('Paging')->getValue('PageParameter');

      $Page = $newsManager->getPageNumber($appKey);
      $Links = array();
      for ($x = 1; $x <= $PageCount; $x++) {
         $Link = LinkGenerator::generateUrl(
            URL::fromCurrent()
                  ->mergeQuery(
               array(
                  $PageParameter => $x
               )
            )
         );
         if ($Page === $x) {
            $Links[] = '<a href="' . $Link . '" class="active">' . $x . '</a>';
         } else {
            $Links[] = '<a href="' . $Link . '">' . $x . '</a>';
         }
      }

      $Tpl = $this->getTemplate('pager');
      $Tpl->setPlaceHolder('pages', implode('&nbsp;&nbsp;|&nbsp;&nbsp;', $Links));

      return $Tpl->transformTemplate();
   }

}
