<?php
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\Page;

/**
 *
 * @package APF\extensions\apfelsms
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

      // keep request parameters
      $keepRequestParams = $this->getAttribute('keepRequestParams');

      ////
      // fetch template name and namespace

      $tmplName = $this->getAttribute('template', 'breadcrumbNavTaglib');
      $tmplNamespace = $this->getAttribute('namespace', 'APF\extensions\apfelsms\pres\templates');


      $page = new Page();
      $page->setContext($this->getContext());
      $page->setLanguage($this->getLanguage());

      $page->loadDesign($tmplNamespace, $tmplName);

      $rootDoc = $page->getRootDocument();
      $rootDoc->setAttribute('SMSBreadcrumbNavBasePageId', $basePageId);
      $rootDoc->setAttribute('SMSBreadcrumbNavBasePageIdTitle', $basePageIdTitle);
      $rootDoc->setAttribute('SMSBaseNavKeepRequestParams', $keepRequestParams);

      return $page->transform();

   }
}
