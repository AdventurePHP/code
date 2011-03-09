<?php
/**
 * Implements an output filter that injects the content of the html header manager
 * into the HTML page.
 */
class HtmlHeaderOutputFilter extends APFObject implements ChainedContentFilter {

   /**
    * @var string[] Defines the node types, that should be included before the textual nodes.
    */
   private static $jsFileNodes = array('StaticJsNode','DynamicJsNode');

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

      $baseNodes = $iM->getBaseNodes();
      foreach ($baseNodes as $base) {
         $output .= $base->transform() . PHP_EOL;
      }

      $metaNodes = $iM->getMetaNodes();
      foreach ($metaNodes as $metaNode) {
         $output .= $metaNode->transform() . PHP_EOL;
      }

      $stylesheets = $iM->getStylesheetNodes();
      foreach ($stylesheets as $stylesheet) {
         $output .= $stylesheet->transform() . PHP_EOL;
      }

      // sort js files according to their dynamic or static character to not
      // generate js errors by accessing functionality that has not been included
      // this is done, by queing the static ones for later transformation but with
      // respect to the order the scrips were added!
      $javascripts = $iM->getJavascriptNodes();
      $queue = array();
      foreach ($javascripts as $script) {
         if (in_array(get_class($script), self::$jsFileNodes)) {
            $output .= $script->transform() . PHP_EOL;
         } else {
            $queue[] = $script;
         }
      }
      foreach ($queue as $script) {
         $output .= $script->transform() . PHP_EOL;
      }

      return $output;
   }

}
?>