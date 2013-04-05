<?php
namespace APF\extensions\apfelsms\biz\pages\stores;

/**
 *
 * @package APF\APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (04.08.12)
 *
 */
interface SMSPageStore {


   /**
    * @abstract
    * @param string|integer $id
    * @return SMSPage
    */
   public function getPage($id);


   /**
    * @abstract
    * @param string|integer $id
    * @param SMSPage $page
    */
   public function setPage($id, SMSPage $page);


   /**
    * @abstract
    * @param string|integer $id
    * @return boolean
    */
   public function isPageSet($id);

}
