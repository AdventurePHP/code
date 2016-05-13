<?php
/**
 * <!--
 * This file is part of the adventure php framework (APF) published under
 * http://adventure-php-framework.org.
 *
 * The APF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The APF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
 * -->
 */
namespace APF\extensions\apfelsms\pres\documentcontroller;

use APF\extensions\apfelsms\biz\pages\SMSPage;
use APF\extensions\apfelsms\biz\sites\SMSSite;
use APF\extensions\apfelsms\biz\SMSManager;
use APF\tools\string\StringAssistant;


/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version:   v0.1 (08.08.2012)
 *             v.02 (15.05.2013) Added support to keep certain request parameters in url
 *
 */
class SMSNavTagController extends SMSBaseNavTagController {

   /**
    * @var SMSManager $SMSM
    */
   protected $SMSM;

   /**
    * @var bool $autoDepth
    */
   protected $autoDepth = false;

   public function transformContent() {

      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');
      $this->SMSM = $SMSM;

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
         /** @var $SMSS SMSSite */
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

            $tmp = [];

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


      // cull entries which should not be displayed
      $navPages = $this->cullEntries($navPages);
      $entries = $this->buildMenuEntries($navPages, $depth);

      $menuRootTemplate = $this->getTemplate('navRoot');
      $menuRootTemplate->setPlaceholder('entries', $entries);
      $menuRootTemplate->transformOnPlace();

   }

   /**
    * @param SMSPage[] $navPages
    * @param $depth
    *
    * @return string
    * @version v0.1
    *          v0.2 (15.05.2013) Added support to keep certain request parameters in url
    */
   protected function buildMenuEntries(array $navPages, $depth) {

      $lastCount = count($navPages);
      $count = 0;
      $buffer = '';

      $url = $this->getUrlPrototype();

      foreach ($navPages AS $navPage) {

         $count++;

         $linkURL = $navPage->getLink(clone $url);
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

         if ($navPage->isCurrentPage()) {
            $linkClasses .= 'current ';
         }


         $children = $navPage->getChildren();
         if (($depth > 1 || ($this->autoDepth && $navPage->isActive())) && count($children) > 0) {

            // cull entries which not should be displayed
            $subEntries = $this->cullEntries($children);

            if (count($subEntries) > 0) {

               $template = $this->getTemplate('navEntryWithSubs');

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

         } else {
            $template = $this->getTemplate('navEntry');
         }

         $template->setPlaceHolder('URL', StringAssistant::escapeSpecialCharacters($linkURL));
         $template->setPlaceHolder('TITLE', StringAssistant::escapeSpecialCharacters($linkTitle));
         $template->setPlaceHolder('CLASSES', $linkClasses);
         $template->setPlaceHolder('TEXT', StringAssistant::escapeSpecialCharacters($linkText));

         $buffer .= $template->transformTemplate();

      }

      return $buffer;

   }

   /**
    * @param SMSPage[] $navPages
    *
    * @return SMSPage[]|array
    */
   protected function cullEntries(array $navPages) {


      $buffer = [];

      // detect hidden and protected files
      foreach ($navPages AS $navPage) {

         if ($navPage->isHidden() || $navPage->isAccessProtected()) {
            continue;
         }

         $buffer[] = $navPage;
      }

      return $buffer;
   }

}
