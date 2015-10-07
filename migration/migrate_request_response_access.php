<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

// Due to huge implementation effort to leave static calls used within static methods
// documentation will include hints to manually replace $this->get*() calls with
// their static pendants (e.g. self::get*Static()).
$searchRequest = '$this->getRequest()';
$searchResponse = '$this->getResponse()';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, $searchRequest) === false && strpos($content, $searchResponse) === false) {
      continue;
   }

   $content = str_replace($searchRequest, '$this->getRequest()', $content);
   $content = str_replace($searchResponse, '$this->getResponse()', $content);

   file_put_contents($file, $content);

}
