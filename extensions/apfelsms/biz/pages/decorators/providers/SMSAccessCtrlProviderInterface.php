<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (30.09.12)
 *
 */
interface SMSAccessCtrlProvider {

   /**
    * @abstract
    * @param SMSPage $page
    * @return bool
    */
   public function isAccessProtected(SMSPage $page);

}
