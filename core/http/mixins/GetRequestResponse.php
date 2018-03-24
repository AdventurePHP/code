<?php
namespace APF\core\http\mixins;

use APF\core\frontcontroller\Frontcontroller;
use APF\core\http\RequestImpl;
use APF\core\http\ResponseImpl;
use APF\core\pagecontroller\APFObject;
use APF\core\singleton\Singleton;

trait GetRequestResponse {

   /**
    * @return RequestImpl The request implementation.
    */
   protected function getRequest() {
      return self::getRequestStatic();
   }

   /**
    * @return RequestImpl|APFObject
    */
   protected static function getRequestStatic() {
      return Singleton::getInstance(Frontcontroller::$requestImplClass);
   }

   /**
    * @return ResponseImpl The response implementation.
    */
   protected function getResponse() {
      return self::getResponseStatic();
   }

   /**
    * @return ResponseImpl|APFObject
    */
   protected static function getResponseStatic() {
      return Singleton::getInstance(Frontcontroller::$responseImplClass);
   }

}
