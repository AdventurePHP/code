<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = filterApfDirectories(find('.', '*.php'));

$search = '#\$this->getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)([ |\n|\r\n]*)\'\)#m';
$searchStatic = '#ServiceManager::getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m'; // s

$searchWithType = '#\$this->getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m';
$searchWithTypeStatic = '#ServiceManager::getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m'; // s

$searchWithInstanceId = '#\$this->getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m'; // s
$searchWithInstanceIdStatic = '#ServiceManager::getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m'; // s

$searchWithInit = '#\$this->getAndInitServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)\'([A-Za-z0-9_]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#';

$searchDi = '#\$this->getDIServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m';
$searchDiStatic = '#DIServiceManager::getServiceObject\(([ |\n|\r\n]*)\'([A-Za-z0-9:\-]+)\',([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+),([ |\n|\r\n]*)(.+)([ |\n|\r\n]*)\)#m'; // s

foreach ($files as $file) {
   $content = file_get_contents($file);

   // static service manager w/ service type and instance id
   $content = preg_replace_callback($searchWithInstanceId, function ($matches) {
      return '$this->getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', \'' . $matches[4] . '\', ' . $matches[6] . ', ' . $matches[8] . ')';
   }, $content);

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

   // static service manager w/ service type and instance id
   $content = preg_replace_callback($searchWithInstanceIdStatic, function ($matches) {
      return 'ServiceManager::getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', \'' . $matches[4] . '\', ' . $matches[6] . ', ' . $matches[8] . ', ' . $matches[10] . ', ' . $matches[12] . ')';
   }, $content);

   // static service manager w/ service type
   $content = preg_replace_callback($searchWithTypeStatic, function ($matches) {
      return 'ServiceManager::getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', \'' . $matches[4] . '\', ' . $matches[6] . ', ' . $matches[8] . ', ' . $matches[10] . ')';
   }, $content);

   // static service manager standard
   $content = preg_replace_callback($searchStatic, function ($matches) {
      return 'ServiceManager::getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', \'' . $matches[4] . '\', ' . $matches[6] . ', ' . $matches[8] . ')';
   }, $content);

   // replace DI calls
   $content = preg_replace_callback($searchDi, function ($matches) {
      return '$this->getDIServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', ' . $matches[4] . ')';
   }, $content);

   // static DI calls
   $content = preg_replace_callback($searchDiStatic, function ($matches) {
      return 'DIServiceManager::getServiceObject(\'APF\\' . str_replace('::', '\\', $matches[2]) . '\', ' . $matches[4] . ', ' . $matches[6] . ', ' . $matches[8] . ')';
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