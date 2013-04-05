<?php
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\tools\string\StringAssistant;

/**
 *
 * @package APF\APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (10.08.12)
 *
 */
class SMSSiteTitleTag extends Document {


   public function transform() {

      /** @var $SMSS SMSSite */
      $SMSS = $this->getDIServiceObject('APF\extensions\apfelsms', 'Site');

      return StringAssistant::escapeSpecialCharacters($SMSS->getWebsiteTitle());

   }
}
