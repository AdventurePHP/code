<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

// replace methods in tags and document controller...
$searchPlaceHolder = 'setPlaceHolderIfExist(';
$searchPlaceHolders = 'setPlaceHoldersIfExist(';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // if no occurrence, go ahead
   if (strpos($content, $searchPlaceHolder) === false || strpos($content, $searchPlaceHolders) === false) {
      continue;
   }

   $content = str_replace($searchPlaceHolder, 'setPlaceHolder(', $content);
   $content = str_replace($searchPlaceHolders, 'setPlaceHolders(', $content);

   file_put_contents($file, $content);

}
