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

   /**
    * @package core::filter::output
    * @class HtmlLinkRewriteFilter
    *
    * Implements a URL rewriting output filter for HTML source code. Rewriting can be adjusted
    * using the <em>linkrewrite</em> attribute. If it is set to "true" or not present, links are
    * rewritten, "false" introduces the filter to not rewrite the link. Further, "mailto:" links
    * are not rewritten, too.
    *
    * @author Christian Schäfer
    * @version
    * Version 0.1, 05.05.2007 (First version of the link rewrite filter)<br />
    * Version 0.2, 08.05.2007 (Refactoring as a filter)<br />
    * Version 0.3, 17.06.2007 (Added form action rewriting)<br />
    */
   class HtmlLinkRewriteFilter extends AbstractFilter {

      /**
       * @var string[][] The link rewrite replace pattern definition.
       */
      private static $REPLACE_PATTERN = array('/?' => '/',
                                              './?' => '/',
                                              '=' => '/',
                                              '&' => '/',
                                              '&amp;' => '/',
                                              '?' => '/'
                                          );

      /**
       * @var string The link rewrite deactivation indicator.
       */
      private static $REWRITE_DEACTIVATE_PATTERN = '/linkrewrite="false"/i';

      /**
       * @public
       *
       * Implements the APF filter API to rewrite the links to fit the
       * url rewrite input filter mechanism.
       *
       * @param string $input The html code to rewrite the links within.
       * @return string the Rewritten html code.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 08.05.2007<br />
       * Version 0.2, 04.09.2010 (Introduced regexps due to performance reasons)<br />
       */
      public function filter($input) {
         return preg_replace_callback(
                 '/<form (.*?)action="(.*?)"(.*?)>(.*?)<\/form>/ims',
                 array('HtmlLinkRewriteFilter','replaceForm'),
                 preg_replace_callback(
                         '/<a (.*?)href="(.*?)"(.*?)>(.*?)<\/a>/ims',
                         array('HtmlLinkRewriteFilter','replaceLink'),
                         $input)
                 );
      }

      /**
       * @public
       * @static
       *
       * Callback function for link rewriting.
       *
       * @param string $hits The matches on the current link tag.
       * @return string The replaced link tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.09.2010<br />
       */
      public static function replaceLink($hits) {

         // avoid link rewriting, if it is deactivated by attribute
         if(preg_match(self::$REWRITE_DEACTIVATE_PATTERN, $hits[1])
               || preg_match(self::$REWRITE_DEACTIVATE_PATTERN, $hits[3])) {
            $hits[1] = preg_replace(self::$REWRITE_DEACTIVATE_PATTERN,'',$hits[1]);
            $hits[3] = preg_replace(self::$REWRITE_DEACTIVATE_PATTERN,'',$hits[3]);
         } elseif (substr_count($hits[2], 'mailto:') > 0) {
            // do nothing
         } else {
            $hits[2] = strtr($hits[2], self::$REPLACE_PATTERN);
         }
         return '<a ' . $hits[1] . 'href="' . $hits[2] . '"' . $hits[3] . '>' . $hits[4] . '</a>';
      }

      /**
       * @public
       * @static
       *
       * Callback function for form action rewriting.
       *
       * @param string $hits The matches on the current form tag.
       * @return string The replaced form tag.
       *
       * @author Christian Achatz
       * @version
       * Version 0.1, 04.09.2010<br />
       */
      public static function replaceForm($hits) {
         $hits[2] = strtr($hits[2],self::$REPLACE_PATTERN);
         return '<form ' . $hits[1] . 'action="' . $hits[2] . '"' . $hits[3] . '>' . $hits[4] . '</form>';
      }

    // end class
   }
?>