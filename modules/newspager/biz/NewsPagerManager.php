<?php
namespace APF\modules\newspager\biz;

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
use APF\modules\newspager\data\NewsPagerMapper;

/**
 * @package modules::newspager::biz
 * @class NewsPagerManager
 *
 * Business component for loading the news page objects.<br />
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsPagerManager extends APFObject {

   /**
    * @var string Defines the dir, where the news content is located.
    */
   private $dataDir = null;

   public function init($initParam) {

      // cut trailing slash if necessary
      if (substr($initParam, strlen($initParam) - 1) == '/') {
         $this->dataDir = substr($initParam, 0, strlen($initParam) - 1);
      } else {
         $this->dataDir = $initParam;
      }

   }

   /**
    * @public
    *
    * Loads a news page object.
    *
    * @param int $page desire page number
    * @return NewsItem The desired news item for the given page.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 02.02.2007<br />
    * Version 0.2, 18.09.2008 (DataDir is now applied to the mapper)<br />
    */
   public function getNewsByPage($page = 1) {
      /* @var $mapper NewsPagerMapper */
      $mapper = & $this->getAndInitServiceObject('APF\modules\newspager\data\NewsPagerMapper', $this->dataDir);
      return $mapper->getNewsByPage($page);
   }

}
