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
import('extensions::htmlheader::pres::taglib', 'htmlheader_taglib_gethead');
import('extensions::htmlheader::pres::taglib', 'htmlheader_taglib_getbodyjs');

/**
 * @package extensions::htmlheader::pres::filter
 * @class HtmlHeaderOutputFilter
 *
 * Implements an output filter that injects the content of the html header manager
 * into the HTML page.
 *
 * @author Christian Achatz
 * @version
 * Version 0.1, 28.08.2010<br />
 * Version 0.2, 10.05.2011 (Introduced the global sorting mechanism)<br />
 */
class HtmlHeaderOutputFilter extends APFObject implements ChainedContentFilter {

   public function filter(FilterChain &$chain, $input = null) {

      $replacements = $this->getHeaderContent();

      // replace gethead-taglib
      $input = str_replace(
         htmlheader_taglib_gethead::HTML_HEADER_INDICATOR,
         $replacements[0],
         $input
      );

      // replace getbodyjs-taglib
      $input = str_replace(
         htmlheader_taglib_getbodyjs::HTML_BODYJS_INDICATOR,
         $replacements[1],
         $input
      );

      return $chain->filter($input);
   }

   private function getHeaderContent() {

      $iM = &$this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');
      /* @var $iM HtmlHeaderManager */

      $outputHead = '';
      $outputBody = '';

      $title = $iM->getTitle();
      if ($title !== null) {
         $outputHead .= $title->transform() . PHP_EOL;
      }

      $baseNodes = $this->sortNodes($iM->getBaseNodes());
      foreach ($baseNodes as $base) {
         $outputHead .= $base->transform() . PHP_EOL;
      }

      $metaNodes = $this->sortNodes($iM->getMetaNodes());
      foreach ($metaNodes as $metaNode) {
         $outputHead .= $metaNode->transform() . PHP_EOL;
      }

      $canonical = $iM->getCanonical();
      if ($canonical !== null) {
         $outputHead .= $canonical->transform() . PHP_EOL;
      }

      $stylesheets = $this->sortNodes($iM->getStylesheetNodes());
      foreach ($stylesheets as $stylesheet) {
         $outputHead .= $stylesheet->transform() . PHP_EOL;
      }

      $javaScripts = $this->sortNodes($iM->getJavascriptNodes());
      foreach ($javaScripts as $script) {
         if ($script->getAppendToBody()) {
            $outputBody .= $script->transform() . PHP_EOL;
         } else {
            $outputHead .= $script->transform() . PHP_EOL;
         }
      }

      return array($outputHead, $outputBody);
   }

   /**
    * @protected
    *
    * Sorts the list of header nodes concerning their priority.
    *
    * @param HeaderNode[] $nodes The header nodes to sort.
    * @return HtmlNode[] The sorted header nodes.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2011<br />
    */
   protected function sortNodes(array $nodes) {
      usort($nodes, array($this, 'compare'));
      return $nodes;
   }

   /**
    * @public
    * @static
    *
    * Compares two header nodes concerning their priority.
    *
    * @param HeaderNode $a The first node.
    * @param HeaderNode $b The second node.
    * @return int The comparison result.
    *
    * @author Christian Achatz
    * @version
    * Version 0.1, 10.05.2011<br />
    */
   private function compare(HeaderNode $a, HeaderNode $b) {
      if ($a->getPriority() == $b->getPriority()) {
         return $a->getPriority() == 0 ? 0 : 1; // sort equals again to preserve order!
      }
      return $a->getPriority() > $b->getPriority() ? -1 : 1;
   }

}
