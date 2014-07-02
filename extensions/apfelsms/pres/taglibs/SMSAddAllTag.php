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
namespace APF\extensions\apfelsms\pres\taglibs;

use APF\core\pagecontroller\Document;
use APF\core\pagecontroller\TagLib;

/**
 * @author: Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (11.08.12)
 */
class SMSAddAllTag extends Document {


   public function onParseTime() {


      $doc = $this->getParentObject();

      $namespace = 'APF\extensions\apfelsms\pres\taglibs\\';
      $prefix = 'sms';

      $importDesignTaglib = new TagLib($namespace . 'SMSImportDesignTag', $prefix, 'importdesign');
      $navTaglib = new TagLib($namespace . 'SMSNavTag', $prefix, 'nav');
      $breadcrumbNavTaglib = new TagLib($namespace . 'SMSBreadcrumbNavTag', $prefix, 'breadcrumbNav');
      $pageLinkTaglib = new TagLib($namespace . 'SMSPageLinkTag', $prefix, 'pageLink');
      $cssIncludeTaglib = new TagLib($namespace . 'SMSCSSIncludesTag', $prefix, 'cssIncludes');
      $jsIncludeTaglib = new TagLib($namespace . 'SMSJSIncludesTag', $prefix, 'jsIncludes');
      $titleTaglib = new TagLib($namespace . 'SMSTitleTag', $prefix, 'title');
      $pageTitleTaglib = new TagLib($namespace . 'SMSPageTitleTag', $prefix, 'pageTitle');
      $siteTitleTaglib = new TagLib($namespace . 'SMSSiteTitleTag', $prefix, 'siteTitle');

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


   public function transform() {


      return ''; // we are just dummy ;)
   }

}
