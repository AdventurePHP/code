<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#\$this->getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)([ |\n|\r\n]*)\'\)#m';
$searchWithType = '#\$this->getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)([A-Za-z0-9:_]+)([ |\n|\r\n]*)\)#m';

$searchWithInit = '#\$this->getAndInitServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#';

$searchDi = '#\$this->getDIServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace single calls
   $content = preg_replace_callback($search, function ($matches) {
      return '$this->getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '\')';
   }, $content);

   // replace calls with service types
   $content = preg_replace_callback($searchWithType, function ($matches) {
      return '$this->getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '\', ' . $matches[6] . ')';
   }, $content);

   // replace calls with init calls
   $content = preg_replace_callback($searchWithInit, function ($matches) {
      return '$this->getAndInitServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '\', ' . $matches[6] . ')';
   }, $content);

   // replace DI calls
   $content = preg_replace_callback($searchDi, function ($matches) {
      return '$this->getDIServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', ' . $matches[4] . ')';
   }, $content);

   // replace Singleton usages
   $content = str_replace(
      'Singleton::getInstance(\'BenchmarkTimer\')',
      'Singleton::getInstance(\'APF\core\benchmark\BenchmarkTimer\')',
      $content
   );
   $content = str_replace(
      'Singleton::getInstance(\'Logger\')',
      'Singleton::getInstance(\'APF\core\logging\Logger\')',
      $content
   );
   $content = str_replace(
      'Singleton::getInstance(\'Frontcontroller\')',
      'Singleton::getInstance(\'APF\core\frontcontroller\Frontcontroller\')',
      $content
   );

   file_put_contents($file, $content);
}