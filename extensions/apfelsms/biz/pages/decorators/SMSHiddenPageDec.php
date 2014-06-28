<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

/**
 * Hides the page in navigations
 *
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 */
class SMSHiddenPageDec extends SMSAbstractPageDec {


   /**
    * @return bool
    */
   public function isHidden() {


      return true;
   }

}
