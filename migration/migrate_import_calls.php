<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#import(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9_]+)\');#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace single calls
   $content = preg_replace_callback($search, function ($matches) {
      $new = 'use APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[2] . ';';
      echo $matches[0] . ' --> ' . $new;
   }, $content);

   //file_put_contents($file, $content);
}