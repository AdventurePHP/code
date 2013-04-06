<?php
include(dirname(__FILE__) . '/migrate_base.php');

$files = find('config', '*umgtconfig.ini');

$search = '#PasswordHashProvider\.([A-Za-z0-9\-]+)\.Namespace ?= ?"([A-Za-z0-9:\-]+)"([\n|\r\n]+)?PasswordHashProvider\.Default\.Class ?= ?"([A-Za-z0-9\-]+)"#';

foreach ($files as $file) {
   $content = file_get_contents($file);

   $content = preg_replace_callback($search, function ($matches) {
      return 'PasswordHashProvider.' . $matches[1] . '.Class = "APF\\' . str_replace('::', '\\', $matches[2]) . '\\' . $matches[4] . '"';
   }, $content);

   file_put_contents($file, $content);
}