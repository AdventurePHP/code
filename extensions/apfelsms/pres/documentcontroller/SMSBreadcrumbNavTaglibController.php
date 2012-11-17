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

      if (!empty($basePageId)) {
         $basePage = $this->SMSM->getPage($basePageId);
      } else {
         $basePage = $this->SMSM->getSite()->getStartPage();
      }

      $currentPage = $this->SMSM->getSite()->getCurrentPage();
      $basePageLevel = $basePage->getLevel();
      $basePageId = $basePage->getId();


      ////
      // collect all breadcrumbs

      $reverseCrumbArray = array();
      $crumb = $currentPage;

      do {

         // Ignore origanisation-/system-nodes (hidden nodes without title)
         $crumbNavTitle = $crumb->getNavTitle();
         if (!($crumb->isHidden() && empty($crumbNavTitle))) {
            $reverseCrumbArray[] = $crumb;
         }

         /** @var $oldCrumb SMSPage */
         $oldCrumb = $crumb;
         $crumb = $oldCrumb->getParent();

      } while (
         ($crumb !== null) // parent found?
         &&
         ($basePageLevel <= $oldCrumb->getLevel()) // are we still deeper than basePage?
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
         if (empty($linkText)) {
            $linkText = $crumb->getId();
         }

         $linkTitle = $linkText;
         $linkClasses = ' level_' . $crumb->getLevel();

         if ($key == $lastKey) {
            $linkClasses .= ' last active current';
         }

         if ($key == 0) {
            $linkClasses .= ' first';
         }

         $template->setPlaceHolder('linkURL', StringAssistant::escapeSpecialCharacters($linkURL));
         $template->setPlaceHolder('linkTitle', StringAssistant::escapeSpecialCharacters($linkTitle));
         $template->setPlaceHolder('linkText', StringAssistant::escapeSpecialCharacters($linkText));
         $template->setPlaceHolder('linkClasses', $linkClasses);

         $buffer .= $template->transformTemplate();
      }


      $containerTemplate = $this->getTemplate('breadcrumbNavigationContainer');
      $containerTemplate->setPlaceHolder('breadcrumbs', $buffer);

      $containerTemplate->transformOnPlace();


   }

}
