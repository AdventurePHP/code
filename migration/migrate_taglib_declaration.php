<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#new TagLib\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-]+)\'([ |\n|\r\n]*)\)#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'new TagLib(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '\', \'' . $matches[6] . '\', \'' . $matches[8] . '\')';
   }, $content);

   file_put_contents($file, $content);
}