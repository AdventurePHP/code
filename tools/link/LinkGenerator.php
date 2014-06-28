<?php
namespace APF\tools\link;

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

/**
 * The <em>LinkGenerator</em> is a generic link generation tool that can be configured
 * by different link scheme implementations. Normally, configuration is done globally
 * within the bootstrap file, but can be overwritten on each single link generation
 * statement by applying the desired scheme.
 * <p/>
 * By default, the APF ships two link scheme implementations: <em>DefaultLinkScheme</em>
 * for normal urls and the <em>RewriteLinkScheme</em> for rewritten urls following
 * the layout that is resolved by the <em>ChainedStandardInputFilter</em> (<em>DefaultLinkScheme</em>
 * pendant) and the <em>ChainedUrlRewritingInputFilter</em> (<em>RewriteLinkScheme</em> pendant).
 * <p/>
 * This component provides facilities to generate &quot;normal&quot; urls as well as
 * front controller action urls that directly address such actions. The latter ones
 * are often used for resource integration (e.g. dynamic images). In case you need
 * actions to be contained within the url, use the <em>keepInUrl</em> flag within
 * your action implementation instead. This property is respected by the shipped
 * link generation schemes mentioned above.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 29.03.2011<br />
 */
final class LinkGenerator {

   /**
    * @var LinkScheme The link scheme to generate the links with.
    */
   private static $LINK_SCHEME;

   private function __construct() {
   }

   public static function setLinkScheme(LinkScheme $linkScheme) {
      self::$LINK_SCHEME = $linkScheme;
   }

   /**
    * Let's you retrieve the current link scheme, that has been configured
    * for global usage within the bootstrap file.
    *
    * @return LinkScheme The current global link scheme.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 04.04.2011<br />
    */
   public static function getLinkScheme() {
      return self::$LINK_SCHEME;
   }

   /**
    * Let's you retrieve a clone of the current link scheme for further
    * configuration and explicit use.
    *
    * @return LinkScheme A clone of the current link scheme.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 20.04.2011<br />
    */
   public static function cloneLinkScheme() {
      return clone self::$LINK_SCHEME;
   }

   /**
    * @param Url $url The url representation.
    * @param LinkScheme $scheme An optional link scheme to overwrite the global scheme.
    *
    * @return string The formatted url.
    */
   public static function generateUrl(Url $url, LinkScheme $scheme = null) {
      if ($scheme === null) {
         return self::$LINK_SCHEME->formatLink($url);
      }

      return $scheme->formatLink($url);
   }

   /**
    * @param Url $url The url representation.
    * @param string $namespace The action's namespace.
    * @param string $name The action's name
    * @param array $params The action's parameters.
    * @param LinkScheme $scheme An optional link scheme to overwrite the global scheme.
    *
    * @return string the formatted url.
    */
   public static function generateActionUrl(Url $url, $namespace, $name, array $params = array(), LinkScheme $scheme = null) {
      if ($scheme === null) {
         return self::$LINK_SCHEME->formatActionLink($url, $namespace, $name, $params);
      }

      return $scheme->formatActionLink($url, $namespace, $name, $params);
   }

}
