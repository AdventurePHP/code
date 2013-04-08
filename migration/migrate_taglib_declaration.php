<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#new TagLib\(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9\-]+)\', ?\'([A-Za-z0-9\-]+)\', ?\'([A-Za-z0-9\-]+)\'\)#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'new TagLib(\'APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[2] . '\', \'' . $matches[3] . '\', \'' . $matches[4] . '\')';
   }, $content);

   file_put_contents($file, $content);
}