<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (23.09.12)
 *
 */
class SMSBreadcrumbNavTaglibController extends base_controller {

   /**
    * @var SMSManager
    */
   protected $SMSM;

   public function transformContent() {

      $this->SMSM = $this->getDIServiceObject('extensions::apfelsms', 'Manager');

      $doc = $this->getDocument();
      $basePageId = $doc->getAttribute('SMSBreadcrumbNavBasePageId');


      ////
      // fetch base page

      /* @var $basePage SMSPage */
      if (!empty($basePageId)) {
         $basePage = $this->SMSM->getPage($basePageId);
      } else {
         $basePage = $this->SMSM->getSite()->getStartPage();
      }

      $currentPage = $this->SMSM->getSite()->getCurrentPage();
      $basePageLevel = $basePage->getLevel();
      $basePageId = $basePage->getId();


      ////
      // collect als breadcrumbs

      $reverseCrumbArray = array();
      $crumb = $currentPage;

      do {
         $reverseCrumbArray[] = $crumb;
         $oldCrumb = $crumb;
         $crumb = $oldCrumb->getParent();
      } while (
         ($crumb !== null) // parent found?
         &&
         ($basePageLevel <= $oldCrumb->getLevel()) // deeper than basePage?
         &&
         ($crumb->getId() != $basePageId) // not basePage?
      );

      // add the base page, if not same as current page
      if ($currentPage->getId() != $basePageId) {
         $reverseCrumbArray[] = $basePage;
      }

      $crumbArray = array_reverse($reverseCrumbArray);


      ////
      // build breadcrumbs

      $buffer = '';
      $lastKey = count($crumbArray) - 1;

      foreach ($crumbArray AS $key => $crumb) {

         $template = $this->getTemplate('breadcrumb');

         $linkURL = $crumb->getLink(Url::fromCurrent()->resetQuery());
         $linkText = $crumb->getNavTitle();
         if ($crumb->getId() == $basePageId) {
            // allow custom title for basePage
            $linkText = $doc->getAttribute('SMSBreadcrumbNavBasePageIdTitle', $crumb->getNavTitle());
         }
         $linkTitle = $linkText;
         $linkClasses = ' level_' . $crumb->getLevel();

         if ($key == $lastKey) {
            $linkClasses .= ' last active current';
         }

         if ($key == 0) {
            $linkClasses .= ' first';
         }

         $template->setPlaceHolder('linkURL', $linkURL);
         $template->setPlaceHolder('linkTitle', $linkTitle);
         $template->setPlaceHolder('linkText', $linkText);
         $template->setPlaceHolder('linkClasses', $linkClasses);

         $buffer .= $template->transformTemplate();
      }


      $containerTemplate = $this->getTemplate('breadcrumbNavigationContainer');
      $containerTemplate->setPlaceHolder('breadcrumbs', $buffer);

      $containerTemplate->transformOnPlace();


   }

}
