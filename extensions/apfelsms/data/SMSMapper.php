<?php
namespace APF\extensions\apfelsms\data;

use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\pages\decorators\SMSPageDec;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version :  v0.2 (18.06.2012)
 *             v0.2 (28.04.2013) Added getPageType()-method to support multiple page types in one application
 */
interface SMSMapper {


   /**
    * @abstract
    * @param SMSPage $page
    * @param SMSPage
    */
   public function mapPage(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return SMSPage
    */
   public function mapPageWithoutDecorators(SMSPage $page);


   /**
    * @abstract
    * @param SMSPageDec $pageDec
    * @param string|integer $pageId
    * @return SMSPageDec
    */
   public function mapPageDec(SMSPageDec $pageDec, $pageId);


   /**
    * @abstract
    * @param string|integer $pageId
    * @return mixed
    * @since v0.3
    */
   public function getPageType($pageId);


   /**
    * @abstract
    * @param SMSPage $page
    * @return array
    */
   public function getChildrenIds(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return array
    */
   public function getSiblingAndOwnIds(SMSPage $page);


   /**
    * @abstract
    * @param SMSPage $page
    * @return string
    */
   public function getParentId(SMSPage $page);

}
