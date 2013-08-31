<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*actionconfig.ini');

$search = '#FC.ActionNamespace ?= ?"([A-Za-z0-9:\-]+)"([\n|\r\n]+)?FC.ActionClass ?= ?"([A-Za-z0-9\\\\_]+)"#';

$searchInput = '#FC.ActionClass ?= ?"([A-Za-z0-9\\\\_]+)"([\n|\r\n]+)?FC.InputClass ?= ?"([A-Za-z0-9\\\\_]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   // migrate action implementation
   $content = preg_replace_callback($search, function ($matches) {
      return 'FC.ActionClass = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   // migrate input implementation based on previous step to gather the namespace
   $content = preg_replace_callback($searchInput, function ($matches) {
      $namespace = substr($matches[1], 0, strrpos($matches[1], '\\'));
      return 'FC.ActionClass = "' . $matches[1] . '"' . "\n" . 'FC.InputClass = "' . $namespace . '\\' . $matches[3] . '"';
   }, $content);

   // remove empty action param definitions
   $content = str_replace('FC.InputParams = ""', '', $content);

   file_put_contents($file, $content);
}