<?php
namespace APF\extensions\apfelsms\biz;

use APF\core\configuration\ConfigurationException;
use APF\core\pagecontroller\APFObject;
use APF\extensions\apfelsms\biz\pages\decorators\SMSPageDec;
use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\pages\stores\SMSPageStore;
use APF\extensions\apfelsms\biz\sites\SMSSite;
use APF\extensions\apfelsms\data\SMSMapper;
use APF\tools\request\RequestHandler;
use Exception;
use InvalidArgumentException;

/**
 * @package APF\extensions\apfelsms
 */
class SMSException extends Exception {

}


/**
 * @package APF\extensions\apfelsms
 */
class SMSUnknownTypeException extends SMSException {


}


/**
 * @package APF\extensions\apfelsms
 */
class SMSWrongParameterException extends SMSException {


}


/**
 * @package APF\extensions\apfelsms
 */
class SMSConfigurationException extends SMSException {


}


/**
 * @package APF\extensions\apfelsms
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
 * @package APF\extensions\apfelsms
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

      // get current page pageId
      $startPageId = $this->site->getStartPageId(); // get start page pageId for fallback

      // if no pageId is present in request, use start page pageId
      $this->site->setCurrentPageId(RequestHandler::getValue($this->pageRequestParamName, $startPageId));

   }


   /**
    * @return string
    */
   final public function getSMSVersion() {


      return 'v1.0';
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
    * @desc
    * Create new page decorator object with DIServiceManager and map with properties.
    *
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

      ////
      // determine service name

      $serviceName = ucfirst($type); // e.g., decorator type "hidden" has serviceName "Hidden"

      ////
      // create pageDec object with DIServiceManager

      try {

         /** @var $pageDec SMSPageDec */
         $pageDec = $this->getDIServiceObject('APF\extensions\apfelsms\pages\decorators', $serviceName);
         $pageDec->setDecType($type);

      } catch (ConfigurationException $ce) {
         throw new SMSUnknownTypeException('[SMSManager::getPageDec()] Service configuration for page decorators of type "' . $type . '" with serviceName "' . $serviceName . '" could not be found. Maybe you have an invalid page decorator type in your data-source!? Please check your configuration, espacially the serviceobjects.ini in namespace "extensions\apfelsms\pages\decorators".', E_USER_ERROR);
      } catch (InvalidArgumentException $ie) {
         throw new SMSConfigurationException('[SMSManager::getPageDec()] Your configuration for page decorators of type "' . $type . '" with serviceName "' . $serviceName . '" is buggy. DIServiceManager throws following exception: ' . $ie, E_USER_ERROR);
      }

      ////
      // map pageDec
      $mappedPageDec = $this->mapper->mapPageDec($pageDec, $pageId);

      return $mappedPageDec;

   }


   /**
    * @desc
    * Load page type from mapper, create new page object with DIServiceManager.
    * Map page with pageDecorators and properties.
    *
    * @param string|integer $pageId Page id
    * @throws SMSException
    * @throws SMSWrongParameterException
    * @throws SMSConfigurationException
    * @throws SMSUnknownTypeException
    * @return SMSPage
    * @version :  v0.1
    *             v0.2 (28.04.2013) Added support for different page types in one application
    */
   public function getPage($pageId) {


      if (empty($pageId)) {
         throw new SMSWrongParameterException('[SMSManager::getPage] No valid page id given. Page id is empty.', E_USER_ERROR);
      }

      ////
      // check if page is prensent in page store. if not, creeate it.
      if (!$this->pageStore->isPageSet($pageId)) {

         ////
         // determine page type

         try {

            // determine page type for page id
            $pageType = $this->mapper->getPageType($pageId);
            if (empty($pageType)) {
               $pageType = $this->pageServiceName; // choose default as fallback
            }

         } catch (SMSException $smse) {
            throw new SMSWrongParameterException('[SMSManager::getPage()] Could not find page with id "' . $pageId . '" in data source.', E_USER_ERROR);
         }

         ////
         // determine service name

         $serviceName = ucfirst($pageType); // e.g. page type "stdPage" has serviceName "StdPage"

         ////
         // create page object with DIServiceManager

         try {

            /** @var $page SMSPage */
            $page = $this->getDIServiceObject('APF\extensions\apfelsms\pages', $serviceName);
            $page->setId($pageId);

         } catch (ConfigurationException $ce) {
            throw new SMSUnknownTypeException('[SMSManager::getPage()] Configured serviceName for pages "' . $serviceName . '" is most likely not existent. Please check your configuration, espacially the serviceobjects.ini in namespace "extensions::apfelsms::pages".', E_USER_ERROR);
         } catch (InvalidArgumentException $ie) {
            throw new SMSConfigurationException('[SMSManager::getPage()] Your configuration for pages is buggy. Please check service "' . $serviceName . '". DIServiceManager throws following exception: ' . $ie, E_USER_ERROR);
         }

         ////
         // map page
         $mappedPage = $this->mapper->mapPage($page);

         ////
         // add page to page store
         $this->pageStore->setPage($pageId, $mappedPage);

      }

      ////
      // get page from page store
      return $this->pageStore->getPage($pageId);
   }

}
