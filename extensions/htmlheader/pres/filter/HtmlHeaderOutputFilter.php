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
      return $chain->filter(
              str_replace(htmlheader_taglib_gethead::HTML_HEADER_INDICATOR,
                      $this->getHeaderContent(),
                      $input)
      );
   }

   private function getHeaderContent() {

      $iM = &$this->getServiceObject('extensions::htmlheader::biz', 'HtmlHeaderManager');
      /* @var $iM HtmlHeaderManager */

      $output = '';

      $title = $iM->getTitle();
      if ($title !== null) {
         $output .= $title->transform() . PHP_EOL;
      }

      $baseNodes = $this->sortNodes($iM->getBaseNodes());
      foreach ($baseNodes as $base) {
         $output .= $base->transform() . PHP_EOL;
      }

      $metaNodes = $this->sortNodes($iM->getMetaNodes());
      foreach ($metaNodes as $metaNode) {
         $output .= $metaNode->transform() . PHP_EOL;
      }

      $stylesheets = $this->sortNodes($iM->getStylesheetNodes());
      foreach ($stylesheets as $stylesheet) {
         $output .= $stylesheet->transform() . PHP_EOL;
      }

      $javascripts = $this->sortNodes($iM->getJavascriptNodes());
      foreach ($javascripts as $script) {
         $output .= $script->transform() . PHP_EOL;
      }

      return $output;
   }

   /**
    * @protected
    *
    * Sorts the list of header nodes concerning their priority.
    *
    * @param HeaderNode[] $nodes The header nodes to sort.
    * @return HeaderNode[] The sorted header nodes.
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
?>