<?php
namespace APF\extensions\apfelsms\biz\pages\decorators;

use APF\extensions\apfelsms\biz\pages\decorators\SMSAliasPageDec;
use APF\tools\link\Url;

/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (02.10.12)
 *
 * @desc Same as alias pageDec, but also generates links to referenced page
 */
class SMSRedirectPageDec extends SMSAliasPageDec {


   /**
    * @param Url $url
    * @return string
    */
   public function getLink(Url $url) {

      return $this->getReferencedPage()->getLink($url);
   }


   /**
    * @return bool
    */
   public function isCurrentPage() {

      return parent::isCurrentPage() || $this->getReferencedPage()->isCurrentPage();
   }


   /**
    * @return bool
    */
   public function isActive() {
      
      return parent::isActive() || $this->getReferencedPage()->isActive();
   }

}
