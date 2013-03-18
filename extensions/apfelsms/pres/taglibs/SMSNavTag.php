<?php
namespace APF\extensions\apfelsms\pres\taglibs;

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (08.08.12)
 *
 */
class SMSNavTag extends Document {


   public function transform() {

      ////
      // fetch attributes from taglib tag

      // navigation level
      $level = $this->getAttribute('level', '0');

      // navigation is relative (in respect to level of current page)
      $rellevel = $this->getAttribute('rellevel', 'false');

      // depth of levels (how many levels are displayed (in submenus))
      $depth = $this->getAttribute('depth', '1');

      // base page id
      $basePageId = $this->getAttribute('basePageId');


      ////
      // fetch template name and namespace

      $tmplName = $this->getAttribute('template', 'navTaglib');
      $tmplNamespace = $this->getAttribute('namespace', 'extensions::apfelsms::pres::templates');


      $page = new Page();
      $page->setContext($this->getContext());
      $page->setLanguage($this->getLanguage());

      $page->loadDesign($tmplNamespace, $tmplName);

      $rootDoc = $page->getRootDocument();
      $rootDoc->setAttribute('SMSNavLevel', $level);
      $rootDoc->setAttribute('SMSNavRelLevel', $rellevel);
      $rootDoc->setAttribute('SMSNavDepth', $depth);
      $rootDoc->setAttribute('SMSNavBasePageId', $basePageId);


      return $page->transform();

   }

}
