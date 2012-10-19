<?php
/**
 *
 * @package APFelSMS
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (11.08.12)
 *
 */
class SMSAddAllTaglib extends Document {


   /**
    *
    */
   public function onParseTime() {

      $doc = $this->getParentObject();

      $namespace = 'extensions::apfelsms::pres::taglibs';
      $prefix = 'sms';

      $importDesignTaglib = new TagLib($namespace, 'SMSImportDesignTaglib', $prefix, 'importdesign');
      $navTaglib = new TagLib($namespace, 'SMSNavTaglib', $prefix, 'nav');
      $breadcrumbNavTaglib = new TagLib($namespace, 'SMSBreadcrumbNavTaglib', $prefix, 'breadcrumbNav');
      $pageLinkTaglib = new TagLib($namespace, 'SMSPageLinkTaglib', $prefix, 'pageLink');
      $cssIncludeTaglib = new Taglib($namespace, 'SMSCSSIncludesTaglib', $prefix, 'cssIncludes');
      $jsIncludeTaglib = new TagLib($namespace, 'SMSJSIncludesTaglib', $prefix, 'jsIncludes');
      $titleTaglib = new TagLib($namespace, 'SMSTitleTaglib', $prefix, 'title');
      $pageTitleTaglib = new TagLib($namespace, 'SMSPageTitleTaglib', $prefix, 'pageTitle');
      $siteTitleTaglib = new TagLib($namespace, 'SMSSiteTitleTaglib', $prefix, 'siteTitle');


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
