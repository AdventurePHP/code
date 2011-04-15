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
import('tools::link', 'LinkGenerator');

/**
 * @package tools::link
 * @class FrontcontrollerLinkHandler
 * @deprecated Use the LinkGenerator instead.
 *
 * Implements a LinkHandler for front controller purposes.
 *
 * @author Christian Sch�fer
 * @version
 * Version 0.1, 10.02.2007<br />
 * Version 0.2, 24.02.2007 (Added new method generateActionLink())<br />
 * Version 0.3, 08.07.2007 (Complete redesign due to changes of the request filter)<br />
 * Version 0.4, 29.10.2007 (Added new method generateURLParams())<br />
 */
class FrontcontrollerLinkHandler {

   private function __construct() {
   }

   /**
    * @public
    * @static
    *
    * Implements a link creation tool for front controller based application. Generates links as
    * you know of the LinkHandler facility, but additionally includes actions, that define the
    * class member <code>$keepInURL=true</code>. This means, that these actions are automatically
    * included in the url, that is returned by this method.
    * <p/>
    * The first param applies a basic url that is manipulated using the <em>$newParams</em>
    * argument. The third - optional - param defines, whether the url should be generated in
    * rewrite style (true) or not (false).
    * <p/>
    * Example:
    * Applying the url
    * <pre>/Page/ChangeLog/param1/value1/param2/value2</pre>
    * along with the param array
    * <pre>
    * array(
    *       'modules_guestbook_biz-action:LoadEntryList' => 'pagesize:20|pager:false|adminview:true',
    *       'Page' => 'Guestbook'
    *      );
    * </pre>
    * the resulting url with url rewriting on is
    * <pre>/Page/Guestbook/param1/value1/param2/value2/~/modules_guestbook_biz-action/LoadEntryList/pagesize/20/pager/false/adminview/true</pre>
    * In normal url mode, you get
    * <pre>?Page=Guestbook&param1=value1&param2=value2&modules_guestbook_biz-action:LoadEntryList=pagesize:20|pager:false|adminview:true.</pre>
    *
    * @param string $url The base url to generate the link with.
    * @param array $newParams A list of url params for manipulation.
    * @param bool $urlRewriting Indicates, whether the url should be generated in url rewrite
    *                           style (true) or not (false).
    * @param boolean $encodeAmpersands True in case the ampersands should be encoded to
    *                                  <em>&amp;</em>, false if not.
    * @return string The desired url.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 24.02.2007<br />
    * Version 0.2, 08.07.2007 (Complete redesign due to redesign of the request filter)<br />
    * Version 0.3, 26.08.2007 (URL is now checked to be a string. URL params do no like multi dimensional arrays!)<br />
    * Version 0.4, 09.11.2007 (Fix for problem with DUMMY actions and filtering for actions with KeepInURL=false)<br />
    * Version 0.5, 10.01.2008 (Fix for problem with DUMMY actions with URL_REWRITING = false)<br />
    * Version 0.6, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
    * Version 0.7, 10.04.2011 (Replaced internal functionality by the LinkGenerator)<br />
    */
   public static function generateLink($url, array $newParams = array(), $urlRewriting = null, $encodeAmpersands = true) {

      // to enable pre-1.14 behaviour, create an url representation lazily
      $url = Url::fromString($url);
      $url->mergeQuery($newParams);

      // retrieve current link scheme and save original
      $current = LinkGenerator::getLinkScheme();
      $scheme = clone $current;

      // handle encoded ampersands setting
      $scheme->setEncodeAmpersands($encodeAmpersands);

      return LinkGenerator::generateUrl($url, $scheme);
   }

}
?>