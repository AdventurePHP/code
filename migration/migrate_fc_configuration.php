<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*actionconfig.ini');

$search = '#FC.ActionNamespace ?= ?"([A-Za-z0-9:\-]+)"([\n|\r\n]+)?FC.ActionClass ?= ?"([A-Za-z0-9\-]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'FC.ActionClass = "APF\\' . str_replace('::', '\\', $matches[1]) . '\\' . $matches[3] . '"';
   }, $content);

   file_put_contents($file, $content);
}