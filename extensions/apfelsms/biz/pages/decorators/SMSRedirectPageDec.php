<?php

import('extensions::apfelsms::biz::pages::decorators', 'SMSAliasPageDec');

/**
 *
 * @package APFelSMS
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

}
