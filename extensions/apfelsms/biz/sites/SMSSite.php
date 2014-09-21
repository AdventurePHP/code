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
namespace APF\extensions\apfelsms\biz\sites;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (30.07.12)
 *
 */
interface SMSSite {


   /**
    * @return string
    */
   public function getWebsiteTitle();


   /**
    * @param string $websiteTitle
    */
   public function setWebsiteTitle($websiteTitle);


   /**
    * @param SMSPage $startPage
    */
   public function setStartPage(SMSPage $startPage);


   /**
    * @return SMSPage
    */
   public function getStartPage();


   /**
    * @param string|integer $startPageId
    */
   public function setStartPageId($startPageId);


   /**
    * @return string|integer
    */
   public function getStartPageId();


   /**
    * @param SMSPage $currentPage
    */
   public function setCurrentPage(SMSPage $currentPage);


   /**
    * @return SMSPage
    */
   public function getCurrentPage();


   /**
    * @param string|integer $currentPageId
    */
   public function setCurrentPageId($currentPageId);


   /**
    * @return string|integer
    */
   public function getCurrentPageId();


   /**
    * @param string|integer $pageId
    */
   public function set403PageId($pageId);


   /**
    * @return string|integer
    */
   public function get403PageId();


   /**
    * @param SMSPage $page
    */
   public function set403Page(SMSPage $page);


   /**
    * @return SMSPage
    */
   public function get403Page();


   /**
    * @return boolean
    */
   public function currentIs403Page();


   /**
    * @param string|integer $pageId
    */
   public function set404PageId($pageId);


   /**
    * @return string|integer
    */
   public function get404PageId();


   /**
    * @param SMSPage $page
    */
   public function set404Page(SMSPage $page);


   /**
    * @return SMSPage
    */
   public function get404Page();


   /**
    * @return boolean
    */
   public function currentIs404Page();

}
