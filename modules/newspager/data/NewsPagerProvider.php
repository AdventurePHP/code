<?php
namespace APF\modules\newspager\data;

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
use APF\core\pagecontroller\APFObject;
use APF\core\pagecontroller\IncludeException;
use APF\modules\newspager\biz\NewsItem;
use APF\tools\filesystem\FilesystemItem;
use APF\tools\filesystem\Folder;

/**
 * @package APF\modules\newspager\data
 * @class NewsPagerProvider
 *
 * Data layer component for loading the news page objects.<br />
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsPagerProvider extends APFObject {

   /**
    * @public
    *
    * Loads a news page object.
    *
    * @param string $dataDir Defines the dir, where the news content is located.
    * @param int $page Desired page number.
    * @return NewsItem The NewsItem domain object.
    * @throws IncludeException In case no news files are found.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2007<br />
    * Version 0.2, 18.09.2008 (Introduced variable data dir)<br />
    * Version 0.3, 23.08.2012 (Change to new File-/Folder-class)<br />
    */
   public function getNewsByPage($dataDir, $page) {

      // cut trailing slash if necessary
      if (substr($dataDir, strlen($dataDir) - 1) == '/') {
         $dataDir = substr($dataDir, 0, strlen($dataDir) - 1);
      }

      // read all files located there
      $folder = new Folder();
      $rawFiles = $folder->open($dataDir)->getContent();

      // get files, that match the current language
      /* @var $files FilesystemItem[] */
      $files = array();
      foreach ($rawFiles as $data) {
         if (substr_count($data->getName(), 'news_' . $this->getLanguage() . '_') > 0) {
            $files[] = $data;
         }
      }

      // throw error when page count is zero!
      $newsCount = count($files);

      if ($newsCount == 0) {
         throw new IncludeException('[NewsPagerProvider::getNewsByPage()] No news files are '
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
      $rawItem = json_decode(file_get_contents($dataDir . '/' . $files[$page - 1]->getName()));

      // fill a new news content object
      $item = new NewsItem();

      $item->setHeadline($rawItem->headline);
      $item->setSubHeadline($rawItem->subline);
      $item->setContent($rawItem->content);

      $item->setNewsCount($newsCount);

      return $item;
   }

}
