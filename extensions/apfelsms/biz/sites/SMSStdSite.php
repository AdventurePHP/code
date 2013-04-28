<?php
namespace APF\extensions\apfelsms\biz\sites;

use APF\core\pagecontroller\APFObject;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\extensions\apfelsms\biz\SMSWrongParameterException;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\sites\SMSSite;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (30.07.12)
 *             v0.2 (18.08.12) Removed rootPageId (wasn't used)
 *             v0.3 (23.09.12) Removed obsolete pageStore (moved to SMSManager)
 */
class SMSStdSite extends APFObject implements SMSSite {


   /**
    * @var string
    */
   protected $websiteTitle = '';


   /**
    * @var string|integer
    */
   protected $currentPageId = null;


   /**
    * @var string|integer
    */
   protected $startPageId = null;


   /**
    * @var string|integer
    */
   protected $forbiddenPageId = null;


   /**
    * @var string|integer
    */
   protected $notFoundPageId = null;


   /**
    * @return string
    */
   public function getWebsiteTitle() {
      return $this->websiteTitle;
   }


   /**
    * @param string $websiteTitle
    */
   public function setWebsiteTitle($websiteTitle) {
      $this->websiteTitle = $websiteTitle;
   }


   /**
    * @param SMSPage $startPage
    */
   public function setStartPage(SMSPage $startPage) {
      $this->startPageId = $startPage->getId();
   }


   /**
    * @return SMSPage
    */
   public function getStartPage() {

      if ($this->getStartPageId() === null) {
         return null;
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      return $SMSM->getPage($this->getStartPageId());

   }


   /**
    * @param string|integer $startPageId
    * @throws SMSWrongParameterException
    */
   public function setStartPageId($startPageId) {
      $this->startPageId = $startPageId;
   }

   /**
    * @return string|integer
    */
   public function getStartPageId() {
      return $this->startPageId;
   }


   /**
    * @param SMSPage $currentPage
    */
   public function setCurrentPage(SMSPage $currentPage) {
      $this->currentPageId = $currentPage->getId();
   }


   /**
    * @return SMSPage|null
    */
   public function getCurrentPage() {

      if ($this->getCurrentPageId() === null) {
         return null;
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      try {
         $currentPage = $SMSM->getPage($this->getCurrentPageId());
      } catch (SMSWrongParameterException $e) {
         // in case of invalid request id
         $currentPage = $this->get404Page();
         $this->setCurrentPageId($this->get404PageId());
      }
      return $currentPage;

   }


   /**
    * @param string|integer $currentPageId
    * @throws SMSWrongParameterException
    */
   public function setCurrentPageId($currentPageId) {

      $this->currentPageId = $currentPageId;

   }


   /**
    * @return string|integer
    */
   public function getCurrentPageId() {

      return $this->currentPageId;

   }


   /**
    * @param string|integer $pageId
    */
   public function set403PageId($pageId) {
      $this->forbiddenPageId = $pageId;
   }


   /**
    * @return string|integer
    */
   public function get403PageId() {
      return $this->forbiddenPageId;
   }


   /**
    * @param SMSPage $page
    */
   public function set403Page(SMSPage $page) {
      $this->set403PageId($page->getId());
   }


   /**
    * @return SMSPage
    */
   public function get403Page() {

      if ($this->get403PageId() === null) {
         return null;
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      return $SMSM->getPage($this->get403PageId());
   }


   /**
    * @return boolean
    */
   public function currentIs403Page() {
      return $this->getCurrentPageId() == $this->get403PageId();
   }


   /**
    * @param string|integer $pageId
    */
   public function set404PageId($pageId) {
      $this->notFoundPageId = $pageId;
   }


   /**
    * @return string|integer
    */
   public function get404PageId() {
      return $this->notFoundPageId;
   }


   /**
    * @param SMSPage $page
    */
   public function set404Page(SMSPage $page) {
      $this->set404PageId($page->getId());
   }


   /**
    * @return SMSPage
    */
   public function get404Page() {

      if ($this->get404PageId() === null) {
         return null;
      }

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');

      return $SMSM->getPage($this->get404PageId());
   }


   /**
    * @return boolean
    */
   public function currentIs404Page() {
      return $this->getCurrentPageId() == $this->get404PageId();
   }


}
