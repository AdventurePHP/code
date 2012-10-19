<?php

import('tools::link', 'LinkGenerator');
import('tools::string', 'StringAssistant');

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (08.08.12)
 *
 */
class SMSNavTaglibController extends base_controller {


   /**
    * @var SMSManager
    */
   protected $SMSM;


   /**
    * @var bool
    */
   protected $autoDepth = false;


   public function transformContent() {

      $this->SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $doc = $this->getDocument();
      $level = $doc->getAttribute('SMSNavLevel');
      $rellevel = $doc->getAttribute('SMSNavRelLevel');
      $depth = $doc->getAttribute('SMSNavDepth');
      $basePageId = $doc->getAttribute('SMSNavBasePageId');


      ////
      // fetch base page

      if (!empty($basePageId)) {
         $basePage = $this->SMSM->getPage($basePageId);
      } else {
         $SMSS = $this->SMSM->getSite();
         $basePage = $SMSS->getCurrentPage();
      }

      $basePageLevel = $basePage->getLevel();

      ////
      // evaluate input attributes

      // level
      if (strtolower($rellevel) == 'true') {
         $levelSummand = intval($level);
         $targetLevel = $basePageLevel + $levelSummand;
      } else {
         $targetLevel = intval($level);
      }

      if ($targetLevel < 0) {
         $targetLevel = 0;
      }

      // depth
      if ($depth == 'auto') {
         $depth = 1;
         $this->autoDepth = true;
      } else {
         $depth = intval($depth);
      }

      if ($depth < 1) {
         $depth = 1;
      }


      ////
      // collect pages to display in first menu level (no subpages, even in case of depth > 1)


      if ($targetLevel <= $basePageLevel) {

         // go to target level page in current branch
         $targetLevelPage = $basePage;
         while ($targetLevelPage->getLevel() > $targetLevel) {
            $targetLevelPage = $targetLevelPage->getParent();
         }

         $navPages = $targetLevelPage->getSiblings(true); // include target level page


      } else {

         $currentCollectingLevel = $basePageLevel + 1; // because we collect children, not siblings
         $collectedPages = $basePage->getChildren();

         while ($currentCollectingLevel < $targetLevel) {

            $tmp = array();

            foreach ($collectedPages AS $page) {
               /** @var $page SMSPage */
               $tmp = array_merge($tmp, $page->getChildren());
            }

            $collectedPages = $tmp;
            $currentCollectingLevel++;

         }

         $navPages = $collectedPages;
      }

      if (count($navPages) > 0) {
         $this->buildMenu($navPages, $depth);
      }

   }


   /**
    * @param SMSPage[] $navPages
    * @param $depth
    */
   protected function buildMenu(array $navPages, $depth) {

      $entries = $this->buildMenuEntries($navPages, $depth);

      $menuRootTemplate = $this->getTemplate('navRoot');
      $menuRootTemplate->setPlaceholder('entries', $entries);
      $menuRootTemplate->transformOnPlace();

   }


   /**
    * @param SMSPage[] $navPages
    * @param $depth
    * @return string
    */
   protected function buildMenuEntries(array $navPages, $depth) {

      $tmp = $navPages;
      $navPages = array();

      // predetect hidden and protected files
      foreach ($tmp AS $navPage) {

         if ($navPage->isHidden() || $navPage->isAccessProtected()) {
            continue;
         }

         $navPages[] = $navPage;
      }

      $lastCount = count($navPages);
      $count = 0;
      $buffer = '';

      foreach ($navPages AS $navPage) {

         $count++;

         $linkURL = $navPage->getLink(Url::fromCurrent()->resetQuery());
         $linkTitle = $navPage->getNavTitle();
         $linkText = $linkTitle;
         $linkClasses = '';

         if ($count == 1) {
            $linkClasses .= 'first ';
         }

         if ($count == $lastCount) {
            $linkClasses .= 'last ';
         }

         if ($navPage->isActive()) {
            $linkClasses .= 'active ';
         }

         if ($navPage->isCurrentSite()) {
            $linkClasses .= 'current ';
         }


         $children = $navPage->getChildren();
         if (($depth > 1 || ($this->autoDepth && $navPage->isActive())) && count($children) > 0) {

            $template = $this->getTemplate('navEntryWithSubs');

            $subEntries = $navPage->getChildren();

            if ($this->autoDepth) {
               $newDepth = 1;
            } else {
               $newDepth = $depth - 1;
            }

            // recursive for subEntries
            $template->setPlaceHolder('subEntries', $this->buildMenuEntries($subEntries, $newDepth));

         } else {
            $template = $this->getTemplate('navEntry');
         }

         $template->setPlaceHolder('linkURL', $linkURL);
         $template->setPlaceHolder('linkTitle', StringAssistant::escapeSpecialCharacters($linkTitle));
         $template->setPlaceHolder('linkClasses', rtrim($linkClasses));
         $template->setPlaceHolder('linkText', StringAssistant::escapeSpecialCharacters($linkText));

         $buffer .= $template->transformTemplate();

      }

      return $buffer;

   }

}
