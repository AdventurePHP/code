<?php
namespace APF\extensions\apfelsms\pres\documentcontroller;

use APF\extensions\apfelsms\biz\SMSManager;
use APF\tools\link\Url;
use APF\tools\string\StringAssistant;


/**
 *
 * @package APF\extensions\apfelsms
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (23.09.12)
 *
 */
class SMSBreadcrumbNavTagController extends SMSBaseNavTagController {


   /**
    * @var SMSManager
    */
   protected $SMSM;


   public function transformContent() {


      /** @var $SMSM SMSManager */
      $SMSM = $this->getDIServiceObject('APF\extensions\apfelsms', 'Manager');
      $this->SMSM = $SMSM;

      $doc = $this->getDocument();
      $basePageId = $doc->getAttribute('SMSBreadcrumbNavBasePageId');


      ////
      // fetch base page

      if(!empty($basePageId)) {
         $basePage = $this->SMSM->getPage($basePageId);
      }
      else {
         $basePage = $this->SMSM->getSite()->getStartPage();
      }

      $currentPage = $this->SMSM->getSite()->getCurrentPage();
      $basePageLevel = $basePage->getLevel();
      $basePageId = $basePage->getId();


      ////
      // collect all breadcrumbs

      /** @var $reverseCrumbArray \APF\extensions\apfelsms\biz\pages\SMSPage[] */
      $reverseCrumbArray = array();
      $crumb = $currentPage;

      do {

         // Ignore origanisation-/system-nodes (hidden nodes without title)
         $crumbNavTitle = $crumb->getNavTitle();
         if(!($crumb->isHidden() && empty($crumbNavTitle))) {
            $reverseCrumbArray[] = $crumb;
         }

         $oldCrumb = $crumb;
         $crumb = $oldCrumb->getParent();

      }
      while (
         ($crumb !== null) // parent found?
         &&
         ($basePageLevel <= $oldCrumb->getLevel()) // are we still deeper than basePage?
         &&
         ($crumb->getId() != $basePageId) // not basePage?
      );

      // add the base page, if not same as current page
      if($currentPage->getId() != $basePageId) {
         $reverseCrumbArray[] = $basePage;
      }

      /** @var $crumbArray \APF\extensions\apfelsms\biz\pages\SMSPage[] */
      $crumbArray = array_reverse($reverseCrumbArray);


      ////
      // build breadcrumbs

      $buffer = '';
      $lastKey = count($crumbArray) - 1;
      
      $url = $this->getUrlPrototype();

      foreach ($crumbArray AS $key => $crumb) {

         $template = $this->getTemplate('breadcrumb');

         $linkURL = $crumb->getLink(clone $url);

         $linkText = $crumb->getNavTitle();
         if($crumb->getId() == $basePageId) {
            // allow custom title for basePage
            $linkText = $doc->getAttribute('SMSBreadcrumbNavBasePageIdTitle', $crumb->getNavTitle());
         }
         if(empty($linkText)) {
            $linkText = $crumb->getId();
         }

         $linkTitle = $linkText;
         $linkClasses = ' level_' . $crumb->getLevel();

         if($key == $lastKey) {
            $linkClasses .= ' last active current';
         }

         if($key == 0) {
            $linkClasses .= ' first';
         }

         $template->setStringPlaceHolder('anchor', 'URL', StringAssistant::escapeSpecialCharacters($linkURL));
         $template->setStringPlaceHolder('anchor', 'TITLE', StringAssistant::escapeSpecialCharacters($linkTitle));
         $template->setStringPlaceHolder('anchor', 'TEXT', StringAssistant::escapeSpecialCharacters($linkText));
         $template->setStringPlaceHolder('anchor', 'CLASSES', $linkClasses);

         $buffer .= $template->transformTemplate();
      }


      $containerTemplate = $this->getTemplate('breadcrumbNavigationContainer');
      $containerTemplate->setPlaceHolder('breadcrumbs', $buffer);

      $containerTemplate->transformOnPlace();

   }

}
