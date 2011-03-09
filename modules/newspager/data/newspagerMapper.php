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
import('modules::newspager::biz', 'newspagerContent');
import('tools::filesystem', 'FilesystemManager');

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
class newspagerMapper extends APFObject {

   /**
    * @var string Defines the dir, where the news content is located.
    */
   private $dataDir = null;

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
   public function init($initParam) {
      $this->dataDir = $initParam;
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
    *  Version 0.2, 18.09.2008 (Introduced variable data dir)<br />
    */
   public function getNewsByPage($page) {

      // read all files located there
      $rawFiles = FilesystemManager::getFolderContent($this->dataDir);

      // get files, that match the current language
      $files = array();
      $count = count($rawFiles);

      for ($i = 0; $i < $count; $i++) {
         if (substr_count($rawFiles[$i], 'news_' . $this->getLanguage() . '_') > 0) {
            $files[] = $rawFiles[$i];
         }
      }

      // throw error when page count is zero!
      $newsCount = count($files);

      if ($newsCount == 0) {
         throw new IncludeException('[newspagerMapper::getNewsByPage()] No news files are '
                 . 'given for language ' . $this->getLanguage(), E_USER_ERROR);
      }

      // if page number is lower then zero, correct it!
      if ($page <= 0) {
         $page = 1;
      }

      // if page number is higher then max, correct it!
      if ($page > $newsCount) {
         $page = $newsCount;
      }

      // read content of file
      $newsArray = file($this->dataDir . '/' . $files[$page - 1]);

      // initialize a new news content object
      $newsItem = new newspagerContent();

      // fill headline
      if (isset($newsArray[0])) {
         $newsItem->setHeadline(trim($newsArray[0]));
      }

      // fill subheadline
      if (isset($newsArray[1])) {
         $newsItem->setSubHeadline(trim($newsArray[1]));
      }

      // fill content
      $count = count($newsArray);
      if ($count >= 3) {
         $content = (string) '';
         for ($i = 2; $i < $count; $i++) {
            $content .= $newsArray[$i];
         }
         $newsItem->setContent(trim($content));
      }

      // set news count
      $newsItem->setNewsCount($newsCount);

      return $newsItem;

   }

}
?>