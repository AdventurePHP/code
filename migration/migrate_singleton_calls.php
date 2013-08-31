<?php
include(dirname(__FILE__) . '/migrate_base.php');

$search = '#(Singleton|SessionSingleton)::getInstance\(\'([A-Za-z0-9_]+)\'\)#';

// Resolve APF class usages within application code ////////////////////////////////////////////////////////////////////
$files = filterApplicationDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      global $classMap;
      if (isset($classMap[$matches[2]])) {
         return $matches[1] . '::getInstance(\'' . $classMap[$matches[2]] . '\')';
      } else {
         return $matches[0];
      }
   }, $content);

   file_put_contents($file, $content);
}

// Resolve application class usages within application code ////////////////////////////////////////////////////////////
$files = filterApfDirectories(find('.', '*.php'));
$classMap = getClassMap($files);

// apply class map to entire code
$files = filterApfDirectories(find('.', '*.php'));

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      global $classMap;
      if (isset($classMap[$matches[2]])) {
         return $matches[1] . '::getInstance(\'' . $classMap[$matches[2]] . '\')';
      } else {
         return $matches[0];
      }
   }, $content);

   file_put_contents($file, $content);
}