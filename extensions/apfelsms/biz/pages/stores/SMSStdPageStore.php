<?php
namespace APF\extensions\apfelsms\biz\pages\stores;

use APF\core\pagecontroller\APFObject;
use APF\extensions\apfelsms\biz\pages\SMSPage;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (04.08.12)
 *
 */
class SMSStdPageStore extends APFObject implements SMSPageStore {


   /**
    * @var SMSPage[]
    */
   protected $pages = array();


   /**
    * @param string|integer $id
    * @return SMSPage
    */
   public function getPage($id) {


      if (!isset($this->pages[$id])) {
         return null;
      }

      return $this->pages[$id];

   }


   /**
    * @param string|integer $id
    * @return boolean
    */
   public function isPageSet($id) {


      if (isset($this->pages[$id])) {
         return true;
      }

      return false;

   }


   /**
    * @param string|integer $id
    * @param SMSPage $page
    */
   public function setPage($id, SMSPage $page) {


      $this->pages[$id] = $page;
   }

}
