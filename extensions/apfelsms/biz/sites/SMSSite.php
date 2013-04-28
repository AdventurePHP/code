<?php
namespace APF\extensions\apfelsms\biz\sites;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (30.07.12)
 *
 */
interface SMSSite {


   /**
    * @abstract
    * @return string
    */
   public function getWebsiteTitle();


   /**
    * @abstract
    * @param string $websiteTitle
    */
   public function setWebsiteTitle($websiteTitle);


   /**
    * @abstract
    * @param SMSPage $startPage
    */
   public function setStartPage(SMSPage $startPage);


   /**
    * @abstract
    * @return SMSPage
    */
   public function getStartPage();


   /**
    * @abstract
    * @param string|integer $startPageId
    */
   public function setStartPageId($startPageId);


   /**
    * @abstract
    * @param
    * @return string|integer
    */
   public function getStartPageId();


   /**
    * @abstract
    * @param SMSPage $currentPage
    */
   public function setCurrentPage(SMSPage $currentPage);


   /**
    * @abstract
    * @return SMSPage
    */
   public function getCurrentPage();


   /**
    * @abstract
    * @param string|integer $currentPageId
    */
   public function setCurrentPageId($currentPageId);


   /**
    * @abstract
    * @return string|integer
    */
   public function getCurrentPageId();


   /**
    * @abstract
    * @param string|integer $pageId
    */
   public function set403PageId($pageId);


   /**
    * @abstract
    * @return string|integer
    */
   public function get403PageId();


   /**
    * @abstract
    * @param SMSPage $page
    */
   public function set403Page(SMSPage $page);


   /**
    * @abstract
    * @return SMSPage
    */
   public function get403Page();


   /**
    * @abstract
    * @return boolean
    */
   public function currentIs403Page();


   /**
    * @abstract
    * @param string|integer $pageId
    */
   public function set404PageId($pageId);


   /**
    * @abstract
    * @return string|integer
    */
   public function get404PageId();


   /**
    * @abstract
    * @param SMSPage $page
    */
   public function set404Page(SMSPage $page);


   /**
    * @abstract
    * @return SMSPage
    */
   public function get404Page();


   /**
    * @abstract
    * @return boolean
    */
   public function currentIs404Page();

}
