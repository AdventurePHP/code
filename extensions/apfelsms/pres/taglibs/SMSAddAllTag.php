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

/**
 * @author Jan Wiese <jan.wiese@adventure-php-framework.org>
 * @version: v0.1 (11.08.12)
 */
class SMSAddAllTag extends Document {

   public function onParseTime() {

      $namespace = 'APF\extensions\apfelsms\pres\taglibs\\';
      $prefix = 'sms';

      self::addTagLib($namespace . 'SMSImportDesignTag', $prefix, 'importdesign');
      self::addTagLib($namespace . 'SMSNavTag', $prefix, 'nav');
      self::addTagLib($namespace . 'SMSBreadcrumbNavTag', $prefix, 'breadcrumbNav');
      self::addTagLib($namespace . 'SMSPageLinkTag', $prefix, 'pageLink');
      self::addTagLib($namespace . 'SMSCSSIncludesTag', $prefix, 'cssIncludes');
      self::addTagLib($namespace . 'SMSJSIncludesTag', $prefix, 'jsIncludes');
      self::addTagLib($namespace . 'SMSTitleTag', $prefix, 'title');
      self::addTagLib($namespace . 'SMSPageTitleTag', $prefix, 'pageTitle');
      self::addTagLib($namespace . 'SMSSiteTitleTag', $prefix, 'siteTitle');
   }

   public function transform() {
      return ''; // we are just dummy ;)
   }

}
