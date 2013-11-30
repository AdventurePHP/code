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

/**
 * @package APF\modules\newspager\biz
 * @class NewsItem
 *
 * Domain object class.<br />
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 02.20.2008<br />
 */
class NewsItem {

   /**
    * @var string Headline of a news page.
    */
   private $headline;

   /**
    * @var string Sub headline of a news page.
    */
   private $subHeadline;

   /**
    * @var string Content of a news page.
    */
   private $content;

   /**
    * @var int Number of news pages.
    */
   private $newsCount;

   public function getHeadline() {
      return $this->headline;
   }

   public function setHeadline($headline) {
      $this->headline = $headline;
   }

   public function getSubHeadline() {
      return $this->subHeadline;
   }

   public function setSubHeadline($subHeadline) {
      $this->subHeadline = $subHeadline;
   }

   public function getContent() {
      return $this->content;
   }

   public function setContent($content) {
      $this->content = $content;
   }

   public function getNewsCount() {
      return $this->newsCount;
   }

   public function setNewsCount($newsCount) {
      $this->newsCount = $newsCount;
   }

}
