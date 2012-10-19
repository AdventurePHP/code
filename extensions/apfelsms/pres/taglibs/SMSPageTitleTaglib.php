<?php

import('tools::string', 'StringAssistant');

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (10.08.12)
 *
 */
class SMSPageTitleTaglib extends Document {


   public function transform() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      return StringAssistant::escapeSpecialCharacters($SMSM->getSite()->getCurrentPage()->getTitle());

   }
}
