<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#@package ([A-Za-z0-9:\-]+)#';

foreach ($files as $file) {
   $content = file_get_contents($file);
   $content = preg_replace_callback($search, function ($matches) {
      if (strpos($matches[1], 'APF') === true) {
         return $matches[0];
      }
      return '@package APF\APF\\' . str_replace('::', '\\', $matches[1]);
   }, $content);
   file_put_contents($file, $content);
}
