<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('.', '*.php');

$search = '#\$this->getServiceObject\(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9_]+)\'\)#m';
$searchWithType = '#\$this->getServiceObject\(\'([A-Za-z0-9:\-]+)\', ?\'([A-Za-z0-9_]+)\', ?([A-Za-z0-9:_]+)\)#m';

$searchDi = '#\$this\->getDIServiceObject\(\'([A-Za-z0-9:\-]+)\', ?#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace single calls
   $content = preg_replace_callback($search, function ($matches) {
      return '$this->getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[2] . '\')';
   }, $content);

   // replace calls with service types
   $content = preg_replace_callback($searchWithType, function ($matches) {
      return '$this->getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[2] . '\', ' . $matches[3] . ')';
   }, $content);

   // replace DI calls
   $content = preg_replace_callback($searchDi, function ($matches) {
      return '$this->getDIServiceObject(\'APF\\' . str_replace('::', '\\', $matches[1]) . '\', ';
   }, $content);

   file_put_contents($file, $content);
}