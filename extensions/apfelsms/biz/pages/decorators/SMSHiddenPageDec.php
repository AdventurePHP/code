<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\decorators\SMSAbstractPageDec;

/**
 *
 * @package APF\extensions\apfelsms
 * @author  : Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version : v0.1 (06.06.12)
 * @desc    : Hides the page in navigations
 *
 */
class SMSHiddenPageDec extends SMSAbstractPageDec {


   /**
    * @return bool
    */
   public function isHidden() {


      return true;
   }

}
