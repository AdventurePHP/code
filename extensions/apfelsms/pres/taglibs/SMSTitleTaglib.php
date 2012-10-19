<?php

import('tools::string', 'StringAssistant');

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (08.08.12)
 *
 */
class SMSTitleTaglib extends Document {


   /**
    * @var string
    */
   protected static $titleTemplate = '<title>{PAGETITLE} - {SITETITLE}</title>';


   /**
    * @return string
    */
   public function transform() {

      /** @var $site SMSSite */
      $site = $this->getDIServiceObject('extensions::apfelsms', 'Site');

      $siteTitle = StringAssistant::escapeSpecialCharacters($site->getWebsiteTitle());

      $currentPage = $site->getCurrentPage();

      if ($currentPage === null) {
         $pageTitle = $siteTitle;
      } else {
         /** @var $currentPage SMSPage */
         $pageTitle = $currentPage->getNavTitle();
      }

      $pageTitle = StringAssistant::escapeSpecialCharacters($pageTitle);

      return str_replace(
         array('{PAGETITLE}', '{SITETITLE}'),
         array($pageTitle, $siteTitle),
         self::$titleTemplate
      );

   }

}
