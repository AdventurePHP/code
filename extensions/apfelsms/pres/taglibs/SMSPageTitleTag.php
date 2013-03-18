<?php
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\tools\string\StringAssistant;

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (10.08.12)
 *
 */
class SMSPageTitleTag extends Document {


   public function transform() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $page = $SMSM->getSite()->getCurrentPage();

      $pageId = $this->getAttribute('pageId');
      if (!empty($pageId)) {
         try {
            $page = $SMSM->getPage($pageId);
         } catch (SMSException $e) {
            return 'Untitled'; // no title could be found (no valid ID)
         }
      }

      return StringAssistant::escapeSpecialCharacters($page->getTitle());

   }
}
