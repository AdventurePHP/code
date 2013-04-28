<?php
namespace APF\extensions\apfelsms\biz\pages\decorators\providers;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (30.09.12)
 *             v0.2 (19.01.13) Added permissionName parameter
 *
 */
interface SMSAccessCtrlProvider {


   /**
    * @abstract
    * @param SMSPage $page
    * @param mixed $permissionName
    * @return bool
    */
   public function isAccessProtected(SMSPage $page, $permissionName);

}
