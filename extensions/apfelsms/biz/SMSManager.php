<?php

import('tools::request', 'RequestHandler');
import('tools::link', 'LinkGenerator');
import('extensions::apfelsms::biz::pages::stores', "SMSPageStoreInterface");

/**
 * @package APFelSMS
 */
class SMSException extends Exception {


}


/**
 * @package APFelSMS
 */
class SMSUnknownTypeException extends SMSException {


}


/**
 * @package APFelSMS
 */
class SMSWrongParameterException extends SMSException {


}


/**
 * @package APFelSMS
 */
class SMSConfigurationException extends SMSException {


}


/**
 * @package APFelSMS
 */
class SMSWrongDataException extends SMSException {


}


//////////////////////////////////////////////////


/**
 * @desc
 * Please configure the SMSManager as DIService in namespace 'extensions::apfelsms' with name 'Manager'.
 * You need to inject an SMSMapper using setMapper() and a site using setSite().
 * Please also configure setup() as setupmethod in your DIService configuration.
 *
 * @package APFelSMS
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.1 (06.06.12)
 *             v0.2 (20.06.12) Added language support in getInstance() and Constructor
 *             v0.3 (25.07.12) Renamed "site" to "page" and changed serveral methods for use as DIService
 */
class SMSManager extends APFObject {


   /**
    * @var SMSMapper Used data mapper
    */
   protected $mapper;


   /**
    * @var SMSSite Used website domain object
    */
   protected $site;


   /**
    * @var SMSPageStore Auxilary object storing pages
    */
   protected $pageStore;


   /**
    * @var string
    */
   protected $pageServiceName = 'StdPage';


   /**
    * @var string
    */
   protected $pageRequestParamName = 'page';


   /**
    * @desc Set up current page ID
    */
   public function setup() {

      //// Try to set up current Page Id

      // get current page id
      $startPageId = $this->site->getStartPageId(true); // get start page id for fallback

      // if no id is present in request, use start page id
      $this->site->setCurrentPageId(RequestHandler::getValue($this->pageRequestParamName, $startPageId));

   }


   /**
    * @return string
    */
   public function getSMSVersion() {
      return 'v0.4';
   }


   /**
    * @param SMSSite $site
    */
   public function setSite(SMSSite $site) {
      $this->site = $site;
   }


   /**
    * @return SMSSite
    */
   public function getSite() {
      return $this->site;
   }


   /**
    * @param SMSMapper $mapper
    */
   public function setMapper(SMSMapper $mapper) {
      $this->mapper = $mapper;
   }


   /**
    * @return SMSMapper
    */
   public function getMapper() {
      return $this->mapper;
   }


   /**
    * @param SMSPageStore $pageStore
    */
   public function setPageStore(SMSPageStore $pageStore) {
      $this->pageStore = $pageStore;
   }


   /**
    * @return SMSPageStore
    */
   public function getPageStore() {
      return $this->pageStore;
   }


   /**
    * @param string $pageServiceName
    */
   public function setPageServiceName($pageServiceName) {
      $this->pageServiceName = $pageServiceName;
   }


   /**
    * @return string
    */
   public function getPageServiceName() {
      return $this->pageServiceName;
   }


   /**
    * @param string $pageRequestParamName
    */
   public function setPageRequestParamName($pageRequestParamName) {
      $this->pageRequestParamName = $pageRequestParamName;
   }


   /**
    * @return string
    */
   public function getPageRequestParamName() {
      return $this->pageRequestParamName;
   }


   /**
    * @param string $type Decorator type name
    * @param string|integer $pageId Page id of the page, the decorator belongs to
    * @return SMSPageDec
    * @throws SMSWrongParameterException
    * @throws SMSUnknownTypeException
    * @throws SMSConfigurationException
    */
   public function getPageDec($type, $pageId) {

      if (empty($type)) {
         throw new SMSWrongParameterException('[SMSManager::getPage] No valid decorator type given. Decorator type is empty.', E_USER_ERROR);
      }

      $serviceName = ucfirst($type); // e.g., decorator type "hidden" has serviceName "Hidden"

      try {
         /** @var $pageDec SMSPageDec */
         $pageDec = $this->getDIServiceObject('extensions::apfelsms::pages::decorators', $serviceName);
         $pageDec->setDecType($type);
      } catch (ConfigurationException $ce) {
         throw new SMSUnknownTypeException('[SMSManager::getPageDec()] Service configuration for page decorators of type "' . $type . '" with serviceName "' . $serviceName . '" could not be found. Maybe you have an invalid page decorator type in your data-source!? Please check your configuration, espacially the serviceobjects.ini in namespace "extensions::apfelsms::pages::decorators".', E_USER_ERROR);
      }
      catch (InvalidArgumentException $ie) {
         throw new SMSConfigurationException('[SMSManager::getPageDec()] Your configuration for page decorators of type "' . $type . '" with serviceName "' . $serviceName . '" is buggy. DIServiceManager throws following exception: ' . $ie, E_USER_ERROR);
      }

      $mappedPageDec = $this->mapper->mapPageDec($pageDec, $pageId);

      return $mappedPageDec;

   }


   /**
    * @param string $id Page Id
    * @return SMSPage
    * @throws SMSUnknownTypeException
    * @throws SMSWrongParameterException
    * @throws SMSConfigurationException
    */
   public function getPage($id) {

      if (empty($id)) {
         throw new SMSWrongParameterException('[SMSManager::getPage] No valid page id given. Page id is empty.', E_USER_ERROR);
      }

      if (!$this->pageStore->isPageSet($id)) {

         try {
            /** @var $page SMSPage */
            $page = $this->getDIServiceObject('extensions::apfelsms::pages', $this->pageServiceName);
            $page->setId($id);
         } catch (ConfigurationException $ce) {
            throw new SMSUnknownTypeException('[SMSManager::getPage()] Configured serviceName for pages "' . $this->pageServiceName . '" is most likely not existent. Please check your configuration, espacially the serviceobjects.ini in namespace "extensions::apfelsms::pages".', E_USER_ERROR);
         }
         catch (InvalidArgumentException $ie) {
            throw new SMSConfigurationException('[SMSManager::getPage()] Your configuration for pages is buggy. Please check service "' . $this->pageServiceName . '". DIServiceManager throws following exception: ' . $ie, E_USER_ERROR);
         }

         $mappedPage = $this->mapper->mapPage($page);

         $this->pageStore->setPage($id, $mappedPage);

      }

      return $this->pageStore->getPage($id);
   }

}
