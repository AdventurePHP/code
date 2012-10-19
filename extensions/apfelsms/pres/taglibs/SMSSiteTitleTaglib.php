<?php

import('tools::string', 'StringAssistant');

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (10.08.12)
 *
 */
class SMSSiteTitleTaglib extends Document {


   public function transform() {

      /** @var $SMSS SMSSite */
      $SMSS = $this->getDIServiceObject('extensions::apfelsms', 'Site');

      return StringAssistant::escapeSpecialCharacters($SMSS->getWebsiteTitle());

   }
}
