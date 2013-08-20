<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 *          : v0.2 (20.08.13) SMSPageDec now extends SMSPage, (re)moved mapData() method
 *
 */
interface SMSPageDec extends SMSPage {


   /**
    * @abstract
    * @return SMSPage
    */
   public function getPage();


   /**
    * @abstract
    * @param SMSPage $page
    */
   public function setPage(SMSPage $page);


   /**
    * @abstract
    * @return string
    */
   public function getDecType();


   /**
    * @abstract
    * @param string $type
    */
   public function setDecType($type);


   /**
    * @abstract
    * @param array $giveThrough
    * @return array
    */
   public function getDecoratorTypes(array $giveThrough = array());


   /**
    * @abstract
    * @param array $giveThrough
    * @return array
    */
   public function getAllDecorators(array $giveThrough = array());


   /**
    * @abstract
    * @param $name
    * @return boolean
    */
   public function providesDecMethod($name);


   /**
    * @abstract
    * @return SMSPage
    */
   public function getPageWithoutDecorators();

   
}
