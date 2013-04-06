<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*serviceobjects.ini');

$search = '#namespace ?= ?"([A-Za-z0-9:\-]+)"([\n|\r\n]+)?class ?= ?"([A-Za-z0-9\-]+)"#';

$searchReference = '#init.([A-Za-z\-_]+).namespace ?= ?"([A-Za-z0-9:]+)"#';

$searchGormInit = '#conf.([A-Za-z\-_]+).method ?= ?"setConfigNamespace"([\*| |\n|\r\n]+)conf.([A-Za-z\-_]+).value ?= ?"([A-Za-z0-9:\-]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // replace service implementation definitions
   $content = preg_replace_callback($search, function ($matches) {
      return 'class = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   // replace DI wiring definitions
   $content = preg_replace_callback($searchReference, function ($matches) {
      return 'init.' . $matches[1] . '.namespace = "APF\\' . str_replace('::', '\\', $matches[2]) . '"';
   }, $content);

   // replace GORM wiring definitions
   $content = preg_replace_callback($searchGormInit, function ($matches) {
      return 'conf.' . $matches[1] . '.method = "setConfigNamespace"' . $matches[2] . 'conf.' . $matches[3] . '.value = "APF\\' . str_replace('::', '\\', $matches[4]) . '"';
   }, $content);

   file_put_contents($file, $content);
}