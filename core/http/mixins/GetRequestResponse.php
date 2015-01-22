<?php
namespace APF\core\http\mixins;

use APF\core\frontcontroller\Frontcontroller;
use APF\core\http\RequestImpl;
use APF\core\http\ResponseImpl;
use APF\core\singleton\Singleton;

trait GetRequestResponse {

   /**
    * @return RequestImpl The request implementation.
    */
   protected static function &getRequest() {
      return Singleton::getInstance(Frontcontroller::$requestImplClass);
   }

   /**
    * @return ResponseImpl
    */
   protected static function &getResponse() {
      return Singleton::getInstance(Frontcontroller::$responseImplClass);
   }

} 