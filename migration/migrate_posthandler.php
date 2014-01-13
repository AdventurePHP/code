<?php
include(dirname(__FILE__) . '/migrate_base.php');

$search = '#PostHandler::getValue\(([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#';
$searchWithDefault = '#PostHandler::getValue\(([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#';
$searchMultiple = '#PostHandler::getValues\(([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#';

$files = filterApfDirectories(find('.', '*.php'));

foreach ($files as $file) {
   $content = file_get_contents($file);

   if (strpos($content, 'PostHandler') !== false) {

      // remove old use
      $content = str_replace('use APF\tools\request\PostHandler;', '', $content);
      $content = str_replace('use \PostHandler;', '', $content);

      // add new use statement
      $content = addUseStatement($content, 'APF\tools\request\RequestHandler');

      // migrate calls
      $content = preg_replace($search, 'RequestHandler::getValue($2, RequestHandler::USE_POST_PARAMS)', $content);
      $content = preg_replace($searchWithDefault, 'RequestHandler::getValue($2, $4, RequestHandler::USE_POST_PARAMS)', $content);
      $content = preg_replace($searchMultiple, 'RequestHandler::getValues($2, RequestHandler::USE_POST_PARAMS)', $content);

   }

   file_put_contents($file, $content);
}
