<?php
namespace APF\core\filter;

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
use APF\core\benchmark\BenchmarkTimer;
use APF\core\registry\Registry;
use APF\core\singleton\Singleton;

/**
 * @package APF\core\filter
 * @class ChainedUrlRewritingOutputFilter
 *
 * Implements the output filter for the new filter chain implementation
 * (since release 1.13). Rewrites links and form actions if activated
 * within your application's bootstrap file.
 * <p/>
 * As of APF 2.0 this filter complies with the <em>ChainedUrlRewritingInputFilter</em>
 * that resolves an external url layout to an internal neutral representation. By
 * default (no url rewriting is registered) no output filter is registered.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 13.01.2011<br />
 * Version 0.2, 09.04.2013 (Refactored to an output filter that only applies in case url rewriting configuration is active)<br />
 */
class ChainedUrlRewritingOutputFilter implements ChainedContentFilter {

   /**
    * @var string[][] The link rewrite replace pattern definition.
    */
   private static $REPLACE_PATTERN = array(
      '/?' => '/',
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
   private static $REWRITE_CONTROL_PATTERN = '/linkrewrite="([A-Za-z]+)"/i';

   public function filter(FilterChain &$chain, $input = null) {

      /* @var $t BenchmarkTimer */
      $t = & Singleton::getInstance('APF\core\benchmark\BenchmarkTimer');

      $id = get_class($this);
      $t->start($id);

      $input = preg_replace_callback(
         '/<form (.*?)action="(.*?)"(.*?)>(.*?)<\/form>/ims',
         array('APF\core\filter\ChainedUrlRewritingOutputFilter', 'replaceForm'),
         preg_replace_callback(
            '/<a (.*?)href="(.*?)"(.*?)>(.*?)<\/a>/ims',
            array('APF\core\filter\ChainedUrlRewritingOutputFilter', 'replaceLink'),
            $input)
      );

      $t->stop($id);

      // delegate filtering to the applied chain
      return $chain->filter($input);
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
      if (preg_match(self::$REWRITE_DEACTIVATE_PATTERN, $hits[1])
            || preg_match(self::$REWRITE_DEACTIVATE_PATTERN, $hits[3])
      ) {
         $hits[1] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[1]);
         $hits[3] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[3]);
      } elseif (substr_count($hits[2], 'mailto:') > 0) {
         // do nothing
      } else {
         $hits[1] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[1]);
         $hits[2] = strtr($hits[2], self::$REPLACE_PATTERN);
         $hits[3] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[3]);
      }
      $hits[1] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[1]);
      $hits[3] = preg_replace(self::$REWRITE_CONTROL_PATTERN, '', $hits[3]);
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
      $hits[2] = strtr($hits[2], self::$REPLACE_PATTERN);
      return '<form ' . $hits[1] . 'action="' . $hits[2] . '"' . $hits[3] . '>' . $hits[4] . '</form>';
   }

}
