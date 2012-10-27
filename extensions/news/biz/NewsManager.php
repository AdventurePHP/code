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
import('extensions::news::biz', 'News');
import('tools::request', 'RequestHandler');

/**
 * @package extensions::news::biz
 * @class NewsManager
 *
 * Manages the news system.
 *
 * @author Ralf Schubert <ralf.schubert@the-screeze.de>
 * @version
 * Version 1.0, 16.06.2011<br />
 */
class NewsManager extends APFObject {

   /**
    * @var GenericORRelationMapper
    */
   protected $ORM = null;

   /**
    * Set's the data component.
    *
    * @param GenericORRelationMapper $ORM
    * @return Postbox Returns itself.
    */
   public function setORM(GenericORRelationMapper &$ORM) {
      $this->ORM = $ORM;
      return $this;
   }

   /**
    * Returns the data component.
    *
    * @return GenericORRelationMapper
    */
   public function getORM() {
      return $this->ORM;
   }

   /**
    * Saves the given News.
    *
    * @param News $News
    */
   public function saveNews(News $News) {
      if ($News->getAppKey() === '') {
         $News->setAppKey($this->getContext());
      }
      $this->ORM->saveObject($News);
   }

   /**
    * Loads a list of News.
    *
    * @param int $start The number of the first element which should be loaded.
    * @param int $count The number of how many news could be loaded.
    * @param string $order The Order of how the news should be sorted by creation ('ASC' Or 'DESC')
    * @param string $appKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
    *
    * @return News[] A List of news.
    */
   public function getNews($start = 0, $count = 10, $order = 'DESC', $appKey = null) {
      if ($appKey === null) {
         $appKey = $this->getContext();
      }

      $crit = new GenericCriterionObject();
      $crit
            ->addCountIndicator($start, $count)
            ->addOrderIndicator('CreationTimestamp', $order)
            ->addPropertyIndicator('AppKey', $appKey);

      return $this->ORM->loadObjectListByCriterion('News', $crit);
   }

   /**
    * Returns the news with the given Id.
    *
    * @param int $id The news' id.
    * @return News Returns null if it wasn't found.
    */
   public function getNewsById($id) {
      return $this->ORM->loadObjectById('News', (int)$id);
   }

   /**
    * Counts the News which exist for the given application key.
    *
    * @param string $appKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
    * @return int The number of existing news.
    */
   public function getNewsCount($appKey = null) {
      if ($appKey === null) {
         $appKey = $this->getContext();
      }

      $crit = new GenericCriterionObject();
      $crit->addPropertyIndicator('AppKey', $appKey);
      return $this->ORM->loadObjectCount('News', $crit);
   }

   /**
    * Loads a list of news, supporting pagination.
    *
    * @param int $page Optional. If given, the news of the given page will be loaded, otherwise new page number will be loaded vom url parameter as defined.
    * @param string $order The Order of how the news should be sorted by creation ('ASC' Or 'DESC')
    * @param string $appKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
    *
    * @return News[]
    */
   public function getNewsByPage($page = null, $order = 'DESC', $appKey = null) {
      $page = $this->getPageNumber($appKey, $page);

      $Cfg = $this->getConfiguration('extensions::news', 'news');
      $Paging = $Cfg->getSection('Paging');
      $EntriesPerPage = (int)$Paging->getValue('EntriesPerPage');

      $Start = ($page - 1) * $EntriesPerPage;

      return $this->getNews($Start, $EntriesPerPage, $order, $appKey);
   }

   /**
    * Deletes the given News.
    *
    * @param News $news
    */
   public function deleteNews(News $news) {
      $this->ORM->deleteObject($news);
   }

   /**
    * Counts the number of newspages.
    *
    * @param string $appKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
    * @return int
    */
   public function getPageCount($appKey = null) {
      if ($appKey === null) {
         $appKey = $this->getContext();
      }

      $Cfg = $this->getConfiguration('extensions::news', 'news');
      $EntriesPerPage = (int)$Cfg->getSection('Paging')->getValue('EntriesPerPage');
      $NewsCount = $this->getNewsCount($appKey);

      return ceil($NewsCount / $EntriesPerPage);
   }

   /**
    * Returns the validated number of the current page.
    *
    * @param string $appKey Optional. Default: Current context. The application identifier, which is used to differentiate different news instances.
    * @param int $page Optional. If set, the given page number will be validated.
    *
    * @return int The current page number, which is within the possible range.
    */
   public function getPageNumber($appKey = null, $page = null) {
      if ($page === null) {
         $Cfg = $this->getConfiguration('extensions::news', 'news');
         $PageParameter = $Cfg->getSection('Paging')->getValue('PageParameter');

         $page = RequestHandler::getValue($PageParameter, 1);
      }

      $page = (int)$page;
      $PageCount = $this->getPageCount($appKey);

      if ($PageCount < $page) {
         $page = $PageCount;
      }

      if ($page < 1) {
         $page = 1;
      }

      return $page;
   }

}
