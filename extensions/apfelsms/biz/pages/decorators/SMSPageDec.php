<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 *          : v0.2 (20.08.13) SMSPageDec now extends SMSPage, (re)moved mapData() method
 *
 */
interface SMSPageDec extends SMSPage {


   /**
    * @return SMSPage
    */
   public function getPage();


   /**
    * @param SMSPage $page
    */
   public function setPage(SMSPage $page);


   /**
    * @return string
    */
   public function getDecType();


   /**
    * @param string $type
    */
   public function setDecType($type);


   /**
    * @param array $giveThrough
    *
    * @return array
    */
   public function getDecoratorTypes(array $giveThrough = array());


   /**
    * @param array $giveThrough
    *
    * @return array
    */
   public function getAllDecorators(array $giveThrough = array());


   /**
    * @param $name
    *
    * @return boolean
    */
   public function providesDecMethod($name);


   /**
    * @return SMSPage
    */
   public function getPageWithoutDecorators();

}
