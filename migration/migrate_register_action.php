<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '->registerAction(';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, $search) === false) {
      continue;
   }

   $content = str_replace($search, '->addAction(', $content);

   file_put_contents($file, $content);

}
