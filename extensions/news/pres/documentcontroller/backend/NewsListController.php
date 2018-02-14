<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * https://adventure-php-framework.org.
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
namespace APF\extensions\news\pres\documentcontroller\backend;

use APF\core\registry\Registry;
use APF\extensions\news\pres\documentcontroller\NewsBaseController;
use APF\tools\link\LinkGenerator;
use APF\tools\link\Url;

/**
 * Document controller for listing the news in the backend with links for editing
 * and deleting.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0,  16.06.2011<br />
 */
class NewsListController extends NewsBaseController {

   public function transformContent() {

      $appKey = $this->getAppKey();

      $newsManager = $this->getNewsManager();

      $count = $newsManager->getNewsCount($appKey);
      $newsList = $newsManager->getNews(0, $count, 'DESC', $appKey);

      if (count($newsList) === 0) {
         $this->getTemplate('noentry')->transformOnPlace();
         return;
      }

      $dataArray = [];

      // retrieve the charset from the registry to guarantee interoperability!
      $charset = Registry::retrieve('APF\core', 'Charset');

      foreach ($newsList as &$news) {
         $dataArray[] = [
               'Title' => htmlentities($news->getTitle(), ENT_QUOTES, $charset, false),
               'Date' => $news->getCreationTimestamp(),
               'LinkEdit' => LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
                     [
                           'backendview' => 'edit',
                           'editnewsid' => (int)$news->getObjectId()
                     ]
               )
               ),
               'LinkDelete' => LinkGenerator::generateUrl(Url::fromCurrent()->mergeQuery(
                     [
                           'backendview' => 'delete',
                           'deletenewsid' => (int)$news->getObjectId()
                     ]
               )
               )
         ];
      }

      $iterator = $this->getIterator('newslist');
      $iterator->fillDataContainer($dataArray);
      $iterator->transformOnPlace();
   }

   /**
    * Overwriting parent's function
    *
    * @return string The application identifier (for login purposes).
    */
   protected function getAppKey() {
      return $this->getDocument()->getParentObject()->getAttribute('app-ident', $this->getContext());
   }

}
