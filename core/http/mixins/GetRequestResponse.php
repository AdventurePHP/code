<?php
namespace APF\core\http\mixins;

use APF\core\http\RequestImpl;
use APF\core\http\ResponseImpl;
use APF\core\singleton\Singleton;

trait GetRequestResponse {

   /**
    * @return RequestImpl
    */
   protected static function &getRequest() {
      return Singleton::getInstance('APF\core\http\RequestImpl');
   }

   /**
    * @return ResponseImpl
    */
   protected static function &getResponse() {
      return Singleton::getInstance('APF\core\http\ResponseImpl');
   }

} 