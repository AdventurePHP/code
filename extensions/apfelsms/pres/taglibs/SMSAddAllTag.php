<?php
namespace APF\extensions\apfelsms\pres\taglibs;

/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (11.08.12)
 *
 */
class SMSAddAllTag extends Document {


   /**
    *
    */
   public function onParseTime() {

      $doc = $this->getParentObject();

      $namespace = 'extensions::apfelsms::pres::taglibs';
      $prefix = 'sms';

      $importDesignTaglib = new TagLib($namespace, 'SMSImportDesignTag', $prefix, 'importdesign');
      $navTaglib = new TagLib($namespace, 'SMSNavTag', $prefix, 'nav');
      $breadcrumbNavTaglib = new TagLib($namespace, 'SMSBreadcrumbNavTag', $prefix, 'breadcrumbNav');
      $pageLinkTaglib = new TagLib($namespace, 'SMSPageLinkTag', $prefix, 'pageLink');
      $cssIncludeTaglib = new Taglib($namespace, 'SMSCSSIncludesTag', $prefix, 'cssIncludes');
      $jsIncludeTaglib = new TagLib($namespace, 'SMSJSIncludesTag', $prefix, 'jsIncludes');
      $titleTaglib = new TagLib($namespace, 'SMSTitleTag', $prefix, 'title');
      $pageTitleTaglib = new TagLib($namespace, 'SMSPageTitleTag', $prefix, 'pageTitle');
      $siteTitleTaglib = new TagLib($namespace, 'SMSSiteTitleTag', $prefix, 'siteTitle');


      $doc->addTagLib($importDesignTaglib);
      $doc->addTagLib($navTaglib);
      $doc->addTagLib($breadcrumbNavTaglib);
      $doc->addTagLib($pageLinkTaglib);
      $doc->addTagLib($cssIncludeTaglib);
      $doc->addTagLib($jsIncludeTaglib);
      $doc->addTagLib($titleTaglib);
      $doc->addTagLib($pageTitleTaglib);
      $doc->addTagLib($siteTitleTaglib);

   }


   /**
    * @return string
    */
   public function transform() {
      return ''; // we are just dummy ;)
   }

}
