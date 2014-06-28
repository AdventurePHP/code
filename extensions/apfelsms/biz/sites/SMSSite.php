<?php
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
