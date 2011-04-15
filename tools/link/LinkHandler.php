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
 * @class LinkHandler
 * @deprecated Use the LinkGenerator instead.
 *
 * Presents a method to generate and validate urls.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 04.05.2005<br />
 * Version 0.2, 11.05.2005<br />
 * Version 0.3, 27.06.2005<br />
 * Version 0.4, 25.04.2006<br />
 * Version 0.5, 27.03.2007 (Replaced deprecated code)<br />
 * Version 0.6, 21.06.2008 (Introduced Registry)<br />
 */
class LinkHandler {

   private function LinkHandler() {
   }

   /**
    * @public
    * @static
    *
    * Implements a link creation tool for page controller based application. Generates links
    * given a base url and a set of manipulation params. Allows you to add, change and delete
    * params within the base url.
    * <p/>
    * This components lets you easily generate links, that respect all included params without
    * having to add then presenting the $_SERVER['REQUEST_URI'] as the first param.
    * <p/>
    * In case you apply the base url
    * <pre>http://myhost.de/index.php?Seite=123&Button=Send&Benutzer=456&Passwort=789</pre>
    * to the component, along with the manipulation params
    * <pre>array('Seite' => 'neueSeite','Button' => '')</pre>
    * the resulting url will be
    * <pre>http://myhost.de/index.php?Seite=neueSeite&Benutzer=456&Passwort=789</pre>
    *
    * @param string $url The base url to generate a link with.
    * @param string[] $parameter The params to manipulate the base link.
    * @param boolean $urlRewriting Indicates, whether the link should be generated in url
    *                              rewrite style (true) or not (false).
    * @param boolean $encodeAmpersands True in case the ampersands should be encoded to
    *                                  <em>&amp;</em>, false if not.
    * @return string The desired url.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 25.04.2006<br />
    * Version 0.2, 01.05.2006 (Removed bug, that values with length of one were filtered out)<br />
    * Version 0.3, 06.05.2006 (Removed bug, that misisng query generated an error)<br />
    * Version 0.4, 29.07.2006 (Refactoring to be able to create rewrite links)<br />
    * Version 0.5, 14.08.2006 (The third param is now initialized using the APPS__URL_REWRITING constant)<br />
    * Version 0.6, 24.02.2007 (Renamed to generateLink())<br />
    * Version 0.7, 27.05.2007 (Bugfix: non existent url paths are considered empty)<br />
    * Version 0.8, 02.06.2007 (Ampersands are now converted in case url rewriting is not active)<br />
    * Version 0.9, 16.06.2007 (Ampersands are now decoded at the beginning to avoid parse errors)<br />
    * Version 1.0, 26.08.2007 (URL is now checked to be a string. URL params do no like multi dimensional arrays!)<br />
    * Version 1.1, 21.06.2008 (Introduced the Registry to retrieve the URLRewriting information)<br />
    * Version 1.2, 19.03.2011 (Introduced the $encodeAmpersands param to not encode ampersands for e.g. ajax request urls)<br />
    * Version 1.3, 10.04.2011 (Replaced internal functionality by the LinkGenerator)<br />
    */
   public static function generateLink($url, array $parameter, $urlRewriting = null, $encodeAmpersands = true) {

      // to enable pre-1.14 behaviour, create an url representation lazily
      $url = Url::fromString($url);

      $url->mergeQuery($parameter);

      // retrieve current link scheme and save original
      $current = LinkGenerator::getLinkScheme();
      $scheme = clone $current;

      // handle encoded ampersands setting
      $scheme->setEncodeAmpersands($encodeAmpersands);

      return LinkGenerator::generateUrl($url, $scheme);
   }

}
?>