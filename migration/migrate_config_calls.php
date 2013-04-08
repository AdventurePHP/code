<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#\$this->getConfiguration\(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9\.\-_]+)\'\)#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return '$this->getConfiguration(\'APF\\' . str_replace('::', '\\', $matches[1]) . '\', \'' . $matches[2] . '\')';
   }, $content);

   file_put_contents($file, $content);
}