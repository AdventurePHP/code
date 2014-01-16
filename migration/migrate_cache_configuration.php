<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*cacheconfig.ini');

$search = '#Cache.Provider.Namespace ?= ?"([A-Za-z0-9:\-_]+)"([\n|\r\n]+)?Cache.Provider.Class ?= ?"([A-Za-z0-9\-]+)"#';

$searchNamespace = '#Cache.Namespace ?= ?"([A-Za-z0-9:\-_]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace provider implementation
   $content = preg_replace_callback($search, function ($matches) {
      return 'Cache.Provider = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   // replace cache namespace to remove ::'s
   $content = preg_replace_callback($searchNamespace, function ($matches) {
      return 'Cache.Namespace = "' . str_replace('::', '\\', $matches[1]) . '"';
   }, $content);

   file_put_contents($file, $content);
}