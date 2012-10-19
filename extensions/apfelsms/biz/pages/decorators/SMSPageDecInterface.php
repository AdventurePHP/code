<?php
/**
 *
 * @package APFelSMS
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 *
 */
interface SMSPageDec {


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


   /**
    * @abstract
    * @param array $data
    */
   public function mapData(array $data);

}
