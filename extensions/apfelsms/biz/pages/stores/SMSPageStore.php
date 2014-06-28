<?php
namespace APF\extensions\apfelsms\biz\pages\stores;

use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (04.08.12)
 *
 */
interface SMSPageStore {


   /**
    * @param string|integer $id
    *
    * @return SMSPage
    */
   public function getPage($id);


   /**
    * @param string|integer $id
    * @param SMSPage $page
    */
   public function setPage($id, SMSPage $page);


   /**
    * @param string|integer $id
    *
    * @return boolean
    */
   public function isPageSet($id);

}
