<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (23.09.12)
 *
 */
class SMSBreadcrumbNavTag extends Document {

   public function transform() {


      ////
      // fetch attributes from taglib tag

      // fetch basePageId
      $basePageId = $this->getAttribute('basePageId');

      // fetch basePageIdTitle
      $basePageIdTitle = $this->getAttribute('basePageIdTitle');


      ////
      // fetch template name and namespace

      $tmplName = $this->getAttribute('template', 'breadcrumbNavTaglib');
      $tmplNamespace = $this->getAttribute('namespace', 'extensions::apfelsms::pres::templates');


      $page = new Page();
      $page->setContext($this->getContext());
      $page->setLanguage($this->getLanguage());

      $page->loadDesign($tmplNamespace, $tmplName);

      $rootDoc = $page->getRootDocument();
      $rootDoc->setAttribute('SMSBreadcrumbNavBasePageId', $basePageId);
      $rootDoc->setAttribute('SMSBreadcrumbNavBasePageIdTitle', $basePageIdTitle);


      return $page->transform();

   }
}
