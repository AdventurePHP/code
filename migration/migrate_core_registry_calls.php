<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#Registry::(.+)\(\'apf::core\'#';

foreach ($files as $file) {
   $content = file_get_contents($file);
   $content = preg_replace_callback($search, function ($matches) {
      return 'Registry::' . $matches[1] . '(\'APF\core\'';
   }, $content);
   file_put_contents($file, $content);
}
