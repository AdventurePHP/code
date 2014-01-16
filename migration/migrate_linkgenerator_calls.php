<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$searchWithOptions = '#LinkGenerator::generateActionUrl\(([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)\'([A-Za-z0-9:\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#ms';
$searchWithoutOptions = '#LinkGenerator::generateActionUrl\(([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)\'([A-Za-z0-9:\-_]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9\-_]+)\'([ |\n|\r\n]*)\)#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // with options
   $content = preg_replace_callback($searchWithOptions, function ($matches) {
      return 'LinkGenerator::generateActionUrl(' . $matches[2] . ', \'APF\\' . str_replace('::', '\\', $matches[4]) . '\', \'' . $matches[6] . '\', ' . $matches[8] . ')';
   }, $content);

   // simple call
   $content = preg_replace_callback($searchWithoutOptions, function ($matches) {
      return 'LinkGenerator::generateActionUrl(' . $matches[2] . ', \'APF\\' . str_replace('::', '\\', $matches[4]) . '\', \'' . $matches[6] . '\')';
   }, $content);

   file_put_contents($file, $content);
}