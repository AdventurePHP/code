<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#import\(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9_]+)\'\);#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'use APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[2] . ';';
   }, $content);

   file_put_contents($file, $content);
}